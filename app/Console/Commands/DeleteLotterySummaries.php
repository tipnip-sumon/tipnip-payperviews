<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\LotteryOptimizationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DeleteLotterySummaries extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'lottery:delete-summaries 
                            {--days=90 : Delete summaries older than this many days}
                            {--user= : Delete summaries for specific user ID}
                            {--start-date= : Start date for deletion range (YYYY-MM-DD)}
                            {--end-date= : End date for deletion range (YYYY-MM-DD)}
                            {--id= : Delete specific summary by ID}
                            {--duplicates : Delete duplicate summaries}
                            {--all : Delete ALL summaries (requires confirmation)}
                            {--force : Force deletion without confirmation}';

    /**
     * The console command description.
     */
    protected $description = 'Delete lottery daily summaries with various filtering options';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🗑️  Starting Lottery Summary Deletion...');
        
        $optimizationService = new LotteryOptimizationService();
        $force = $this->option('force');
        
        // Show current statistics
        $this->showCurrentStats();
        
        // Determine deletion type
        if ($this->option('all')) {
            $this->handleDeleteAll($optimizationService, $force);
        } elseif ($this->option('duplicates')) {
            $this->handleDeleteDuplicates($optimizationService, $force);
        } elseif ($this->option('id')) {
            $this->handleDeleteById($optimizationService, $this->option('id'), $force);
        } elseif ($this->option('user')) {
            $this->handleDeleteByUser($optimizationService, $force);
        } else {
            $this->handleDeleteByAge($optimizationService, $this->option('days'), $force);
        }
        
        $this->info('🏁 Summary deletion complete!');
        $this->showCurrentStats();
    }

    /**
     * Handle deletion by age
     */
    private function handleDeleteByAge(LotteryOptimizationService $service, int $days, bool $force)
    {
        if (!$force && !$this->confirm("Delete lottery summaries older than {$days} days?")) {
            $this->info('❌ Operation cancelled.');
            return;
        }

        $this->info("🧹 Deleting summaries older than {$days} days...");
        $result = $service->deleteOldSummaries($days);
        
        if ($result['success']) {
            $this->info("✅ {$result['message']}");
            $this->displayStats($result['stats']);
        } else {
            $this->error("❌ {$result['message']}");
        }
    }

    /**
     * Handle deletion by user
     */
    private function handleDeleteByUser(LotteryOptimizationService $service, bool $force)
    {
        $userId = $this->option('user');
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("❌ User with ID {$userId} not found.");
            return;
        }

        $startDate = $this->option('start-date') ? Carbon::parse($this->option('start-date')) : null;
        $endDate = $this->option('end-date') ? Carbon::parse($this->option('end-date')) : null;
        
        $dateRange = '';
        if ($startDate && $endDate) {
            $dateRange = " from {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}";
        } elseif ($startDate) {
            $dateRange = " from {$startDate->format('Y-m-d')}";
        } elseif ($endDate) {
            $dateRange = " until {$endDate->format('Y-m-d')}";
        }

        if (!$force && !$this->confirm("Delete lottery summaries for user {$user->username}{$dateRange}?")) {
            $this->info('❌ Operation cancelled.');
            return;
        }

        $this->info("🧹 Deleting summaries for user {$user->username}{$dateRange}...");
        $result = $service->deleteUserSummaries($user, $startDate, $endDate);
        
        if ($result['success']) {
            $this->info("✅ {$result['message']}");
            $this->displayStats($result['stats']);
        } else {
            $this->error("❌ {$result['message']}");
        }
    }

    /**
     * Handle deletion by ID
     */
    private function handleDeleteById(LotteryOptimizationService $service, int $id, bool $force)
    {
        if (!$force && !$this->confirm("Delete lottery summary with ID {$id}?")) {
            $this->info('❌ Operation cancelled.');
            return;
        }

        $this->info("🧹 Deleting summary with ID {$id}...");
        $result = $service->deleteSummaryById($id);
        
        if ($result['success']) {
            $this->info("✅ {$result['message']}");
            $this->displayStats($result['stats']);
        } else {
            $this->error("❌ {$result['message']}");
        }
    }

    /**
     * Handle deletion of duplicates
     */
    private function handleDeleteDuplicates(LotteryOptimizationService $service, bool $force)
    {
        if (!$force && !$this->confirm('Delete duplicate lottery summaries?')) {
            $this->info('❌ Operation cancelled.');
            return;
        }

        $this->info('🧹 Deleting duplicate summaries...');
        $result = $service->deleteDuplicateSummaries();
        
        if ($result['success']) {
            $this->info("✅ {$result['message']}");
            $this->displayStats($result['stats']);
        } else {
            $this->error("❌ {$result['message']}");
        }
    }

    /**
     * Handle deletion of all summaries
     */
    private function handleDeleteAll(LotteryOptimizationService $service, bool $force)
    {
        $this->warn('⚠️  WARNING: This will delete ALL lottery summaries!');
        
        if (!$force) {
            if (!$this->confirm('Are you sure you want to delete ALL lottery summaries?')) {
                $this->info('❌ Operation cancelled.');
                return;
            }
            
            if (!$this->confirm('This action cannot be undone. Are you ABSOLUTELY sure?')) {
                $this->info('❌ Operation cancelled.');
                return;
            }
        }

        $this->info('🧹 Deleting ALL summaries...');
        $result = $service->deleteAllSummaries(true);
        
        if ($result['success']) {
            $this->warn("✅ {$result['message']}");
            $this->displayStats($result['stats']);
        } else {
            $this->error("❌ {$result['message']}");
        }
    }

    /**
     * Display current lottery summary statistics
     */
    private function showCurrentStats()
    {
        $this->info('📊 Current Lottery Summary Statistics:');
        
        $totalSummaries = DB::table('lottery_daily_summaries')->count();
        $totalUsers = DB::table('lottery_daily_summaries')->distinct('user_id')->count();
        $oldestSummary = DB::table('lottery_daily_summaries')->min('summary_date');
        $newestSummary = DB::table('lottery_daily_summaries')->max('summary_date');
        $storageUsed = $totalSummaries * 0.5; // ~0.5KB per summary
        
        $duplicates = DB::table('lottery_daily_summaries')
            ->select('user_id', 'summary_date', DB::raw('COUNT(*) as count'))
            ->groupBy('user_id', 'summary_date')
            ->having('count', '>', 1)
            ->count();
        
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Summaries', number_format($totalSummaries)],
                ['Unique Users', number_format($totalUsers)],
                ['Date Range', ($oldestSummary && $newestSummary) ? "{$oldestSummary} to {$newestSummary}" : 'N/A'],
                ['Duplicate Groups', number_format($duplicates)],
                ['Estimated Storage', number_format($storageUsed, 1) . ' KB']
            ]
        );
    }

    /**
     * Display operation statistics
     */
    private function displayStats(array $stats)
    {
        if (empty($stats)) {
            return;
        }

        $this->info('📊 Operation Statistics:');
        $tableData = [];
        
        foreach ($stats as $key => $value) {
            $formattedKey = ucwords(str_replace('_', ' ', $key));
            
            if (is_array($value)) {
                $formattedValue = json_encode($value, JSON_PRETTY_PRINT);
            } elseif (is_numeric($value)) {
                $formattedValue = number_format($value, 2);
            } else {
                $formattedValue = $value;
            }
            
            $tableData[] = [$formattedKey, $formattedValue];
        }
        
        $this->table(['Metric', 'Value'], $tableData);
    }
}
