<?php

namespace App\Console\Commands;

use App\Models\LotteryDraw;
use App\Models\User;
use App\Services\LotteryOptimizationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class OptimizeLotteryData extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'lottery:optimize 
                            {--days=7 : Number of days to keep before optimization}
                            {--force : Force optimization without confirmation}
                            {--summaries : Create daily summaries for users}
                            {--delete-old-summaries=90 : Delete summaries older than this many days}
                            {--cleanup-summaries : Clean up old and duplicate summaries}';

    /**
     * The console command description.
     */
    protected $description = 'Optimize lottery data by cleaning virtual tickets and creating daily summaries';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🎰 Starting Lottery Data Optimization...');
        
        $optimizationService = new LotteryOptimizationService();
        $days = $this->option('days');
        $force = $this->option('force');
        $createSummaries = $this->option('summaries');
        $deleteOldSummaries = $this->option('delete-old-summaries');
        $cleanupSummaries = $this->option('cleanup-summaries');
        
        // Show current statistics
        $this->showCurrentStats();
        
        if (!$force && !$this->confirm('Do you want to proceed with lottery optimization?')) {
            $this->info('Optimization cancelled.');
            return;
        }
        
        $this->info('🧹 Optimizing lottery draws...');
        
        // Optimize old draws
        $result = $optimizationService->optimizeOldDraws($days);
        
        if ($result['success']) {
            $this->info("✅ Optimized {$result['stats']['draws_processed']} lottery draws");
            $this->info("🗑️  Deleted {$result['stats']['virtual_tickets_deleted']} virtual tickets");
            $this->info("💾 Estimated storage saved: {$result['stats']['storage_saved']} KB");
        } else {
            $this->error("❌ Optimization failed: {$result['message']}");
            return;
        }
        
        // Create daily summaries if requested
        if ($createSummaries) {
            $this->info('📊 Creating daily lottery summaries...');
            $this->createDailySummaries($optimizationService);
        }
        
        // Clean up old summaries if requested
        if ($cleanupSummaries || $deleteOldSummaries) {
            $this->info('🗑️  Cleaning up lottery summaries...');
            $this->cleanupSummaries($optimizationService, $deleteOldSummaries, $force);
        }
        
        // Show final statistics
        $this->info('🏁 Optimization complete!');
        $this->showCurrentStats();
    }
    
    /**
     * Show current lottery statistics
     */
    private function showCurrentStats()
    {
        $this->info('📊 Current Lottery Statistics:');
        
        $totalDraws = LotteryDraw::count();
        $completedDraws = LotteryDraw::whereIn('status', ['completed', 'drawn'])->count();
        $optimizedDraws = LotteryDraw::where('cleanup_performed', true)->count();
        
        $virtualTickets = DB::table('lottery_tickets')->where('is_virtual', true)->count();
        $realTickets = DB::table('lottery_tickets')->where('is_virtual', false)->count();
        
        $summariesCount = DB::table('lottery_daily_summaries')->count();
        
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Draws', number_format($totalDraws)],
                ['Completed Draws', number_format($completedDraws)],
                ['Optimized Draws', number_format($optimizedDraws)],
                ['Virtual Tickets', number_format($virtualTickets)],
                ['Real Tickets', number_format($realTickets)],
                ['Daily Summaries', number_format($summariesCount)]
            ]
        );
    }
    
    /**
     * Create daily summaries for users
     */
    private function createDailySummaries(LotteryOptimizationService $service)
    {
        $users = User::whereHas('lotteryTickets', function($query) {
            $query->where('is_virtual', false)
                  ->where('purchased_at', '>=', now()->subDays(30));
        })->get();
        
        $this->info("Creating summaries for {$users->count()} users...");
        
        $progressBar = $this->output->createProgressBar($users->count());
        $successCount = 0;
        
        foreach ($users as $user) {
            // Create summaries for the last 30 days
            for ($i = 0; $i < 30; $i++) {
                $date = now()->subDays($i);
                $result = $service->createDailyLotterySummary($user, $date);
                
                if ($result['success'] && $result['summary']) {
                    $successCount++;
                }
            }
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine();
        $this->info("✅ Created {$successCount} daily summaries");
    }

    /**
     * Clean up lottery summaries
     */
    private function cleanupSummaries(LotteryOptimizationService $service, int $daysToKeep, bool $force)
    {
        // Delete old summaries
        if ($daysToKeep > 0) {
            $result = $service->deleteOldSummaries($daysToKeep);
            if ($result['success']) {
                $this->info("✅ {$result['message']}");
                if (!empty($result['stats'])) {
                    $this->line("   💾 Storage freed: {$result['stats']['storage_freed']} KB");
                    $this->line("   👥 Users affected: {$result['stats']['users_affected']}");
                }
            } else {
                $this->error("❌ {$result['message']}");
            }
        }

        // Delete duplicates
        $result = $service->deleteDuplicateSummaries();
        if ($result['success']) {
            $this->info("✅ {$result['message']}");
            if (!empty($result['stats']) && $result['stats']['summaries_deleted'] > 0) {
                $this->line("   🔄 Duplicate groups: {$result['stats']['duplicate_groups']}");
                $this->line("   💾 Storage freed: {$result['stats']['storage_freed']} KB");
            }
        } else {
            $this->error("❌ {$result['message']}");
        }
    }
}
