<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\DailyVideoAssignment;
use App\Models\ReferralCommission;
use App\Services\ReferralDistributionService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DailyCommissionDistribution extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'commissions:distribute-daily {--force : Force reprocessing even if already processed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Distribute daily referral commissions for video earnings (always processes yesterday)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = Carbon::yesterday(); // Always process yesterday - mandatory condition
        $force = $this->option('force');
        
        $this->info("Processing daily commission distribution for: {$date->format('Y-m-d')}");
        
        try {
            // Get users who have daily video earnings for the specified date
            $dailyEarnings = DailyVideoAssignment::select('user_id', DB::raw('SUM(earning_amount) as total_daily_earning'))
                ->where('assignment_date', $date)
                ->where('is_watched', true)
                ->whereNotNull('earning_amount')
                ->where('earning_amount', '>', 0)
                ->groupBy('user_id')
                ->having('total_daily_earning', '>', 0)
                ->get();
            
            // Load user relationships manually
            $userIds = $dailyEarnings->pluck('user_id')->toArray();
            $users = User::whereIn('id', $userIds)->get()->keyBy('id');
            
            // Attach users to earnings
            $dailyEarnings = $dailyEarnings->map(function($earning) use ($users) {
                $earning->user = $users[$earning->user_id] ?? null;
                return $earning;
            })->filter(function($earning) {
                return $earning->user !== null;
            });
            
            if ($dailyEarnings->isEmpty()) {
                $this->info('No daily earnings found to process for commission distribution.');
                return Command::SUCCESS;
            }
            
            // Check which users haven't been processed yet for this date
            if (!$force) {
                try {
                    // Try to use the new earning_type column if it exists
                    $processedUserIds = ReferralCommission::whereDate('distributed_at', $date)
                        ->where('earning_type', 'daily_video_total')
                        ->pluck('earner_user_id')
                        ->unique()
                        ->toArray();
                } catch (\Exception $e) {
                    // Fallback: If earning_type column doesn't exist, check by null daily_video_assignment_id
                    $processedUserIds = ReferralCommission::whereDate('distributed_at', $date)
                        ->whereNull('daily_video_assignment_id')
                        ->pluck('earner_user_id')
                        ->unique()
                        ->toArray();
                }
                
                // Filter out already processed users
                $originalCount = $dailyEarnings->count();
                $dailyEarnings = $dailyEarnings->filter(function ($earning) use ($processedUserIds) {
                    return !in_array($earning->user_id, $processedUserIds);
                });
                
                $filteredCount = count($processedUserIds);
                if ($filteredCount > 0) {
                    $this->info("Skipping {$filteredCount} users who have already been processed for this date.");
                }
            }
            
            if ($dailyEarnings->isEmpty()) {
                $this->info('All users have already been processed for commission distribution on this date.');
                return Command::SUCCESS;
            }
            
            $this->info("Found {$dailyEarnings->count()} users with daily earnings to process...");
            
            $distributionService = new ReferralDistributionService();
            $totalDistributed = 0;
            $totalUsers = 0;
            $errorCount = 0;
            
            $progressBar = $this->output->createProgressBar($dailyEarnings->count());
            $progressBar->start();
            
            foreach ($dailyEarnings as $userEarning) {
                try {
                    // Delete existing commissions if forcing reprocessing
                    if ($force) {
                        try {
                            // Try to delete using earning_type if column exists
                            ReferralCommission::whereDate('distributed_at', $date)
                                ->where('earner_user_id', $userEarning->user_id)
                                ->where('earning_type', 'daily_video_total')
                                ->delete();
                        } catch (\Exception $e) {
                            // Fallback: Delete using null daily_video_assignment_id
                            ReferralCommission::whereDate('distributed_at', $date)
                                ->where('earner_user_id', $userEarning->user_id)
                                ->whereNull('daily_video_assignment_id')
                                ->delete();
                        }
                    }
                    
                    // Create a virtual assignment object for daily total
                    $virtualAssignment = (object) [
                        'id' => 'daily_' . $userEarning->user_id . '_' . $date->format('Y-m-d'),
                        'user_id' => $userEarning->user_id,
                        'user' => $userEarning->user,
                        'earning_amount' => $userEarning->total_daily_earning,
                        'assignment_date' => $date,
                        'earning_type' => 'daily_video_total'
                    ];
                    
                    $result = $distributionService->distributeDailyCommissions($virtualAssignment);
                    
                    if ($result['success']) {
                        $totalDistributed += $result['total_distributed'];
                        $totalUsers++;
                    } else {
                        $errorCount++;
                        $this->warn("Failed to distribute for user {$userEarning->user_id}: {$result['message']}");
                    }
                    
                } catch (\Exception $e) {
                    $errorCount++;
                    $this->error("Error processing user {$userEarning->user_id}: {$e->getMessage()}");
                }
                
                $progressBar->advance();
            }
            
            $progressBar->finish();
            $this->newLine();
            
            // Summary report
            $this->info("Commission Distribution Summary for {$date->format('Y-m-d')}:");
            $this->table(
                ['Metric', 'Value'],
                [
                    ['Total Users Processed', $totalUsers],
                    ['Total Commission Distributed', '$' . number_format($totalDistributed, 6)],
                    ['Errors Encountered', $errorCount],
                    ['Processing Date', $date->format('Y-m-d H:i:s')],
                ]
            );
            
            // Update daily summary statistics
            $this->updateDailySummary($date, $totalUsers, $totalDistributed);
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error("Fatal error during commission distribution: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
    
    /**
     * Update daily summary statistics
     */
    private function updateDailySummary(Carbon $date, int $totalUsers, float $totalDistributed): void
    {
        try {
            // Get detailed statistics for the day
            $commissions = ReferralCommission::whereDate('distributed_at', $date)->get();
            
            $levelTotals = [];
            for ($i = 1; $i <= 7; $i++) {
                $levelTotals["level_{$i}_total"] = $commissions->where('level', $i)->sum('commission_amount');
            }
            
            $uniqueUsers = $commissions->unique('referrer_user_id')->count();
            $totalLevels = $commissions->sum('level');
            
            // Store daily summary
            DB::table('daily_commission_summaries')->updateOrInsert(
                ['date' => $date->format('Y-m-d')],
                array_merge([
                    'total_users' => $totalUsers,
                    'total_distributed' => $totalDistributed,
                    'total_users_earned' => $uniqueUsers,
                    'total_levels_processed' => $totalLevels,
                    'processed_at' => now(),
                    'updated_at' => now(),
                    'created_at' => now()
                ], $levelTotals)
            );
            
            $this->info("Daily summary updated successfully.");
            
            // Show level breakdown
            $this->info("Level breakdown:");
            for ($i = 1; $i <= 7; $i++) {
                if ($levelTotals["level_{$i}_total"] > 0) {
                    $this->line("  Level {$i}: $" . number_format($levelTotals["level_{$i}_total"], 6));
                }
            }
            
        } catch (\Exception $e) {
            // If table doesn't exist, just log the summary
            $this->warn("Could not update daily summary table: {$e->getMessage()}");
            $this->info("Consider running: php artisan migrate");
        }
    }
}
