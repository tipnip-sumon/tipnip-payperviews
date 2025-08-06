<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DailyVideoService;

class CleanupOldVideoAssignments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'video:cleanup-assignments {--days=30 : Number of days to keep assignments}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old daily video assignments to keep the database optimized';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        
        $this->info("Cleaning up video assignments older than {$days} days...");
        
        $videoService = new DailyVideoService();
        $deletedCount = $videoService->cleanupOldAssignments($days);
        
        $this->info("Successfully cleaned up {$deletedCount} old video assignments.");
        
        return Command::SUCCESS;
    }
}
