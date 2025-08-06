<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CleanupSessionNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:cleanup 
                            {--days=30 : Delete notifications older than specified days}
                            {--force : Force deletion without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old session notifications from the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $force = $this->option('force');
        
        $this->info("Session Notifications Cleanup Started");
        $this->info("Removing notifications older than {$days} days...");
        
        // Calculate cutoff date
        $cutoffDate = Carbon::now()->subDays($days);
        
        // Count notifications to be deleted
        $readNotificationsCount = DB::table('user_session_notifications')
            ->where('is_read', true)
            ->where('read_at', '<', $cutoffDate)
            ->count();
            
        $oldUnreadCount = DB::table('user_session_notifications')
            ->where('is_read', false)
            ->where('created_at', '<', $cutoffDate->subDays(7)) // Keep unread for extra 7 days
            ->count();
        
        $totalCount = $readNotificationsCount + $oldUnreadCount;
        
        if ($totalCount === 0) {
            $this->info("No old notifications found to clean up.");
            return 0;
        }
        
        $this->info("Found {$totalCount} notifications to clean up:");
        $this->info("- {$readNotificationsCount} read notifications older than {$days} days");
        $this->info("- {$oldUnreadCount} unread notifications older than " . ($days + 7) . " days");
        
        // Confirm deletion unless forced
        if (!$force && !$this->confirm('Do you want to proceed with the cleanup?')) {
            $this->info('Cleanup cancelled.');
            return 0;
        }
        
        try {
            DB::beginTransaction();
            
            // Delete read notifications older than specified days
            $deletedRead = DB::table('user_session_notifications')
                ->where('is_read', true)
                ->where('read_at', '<', $cutoffDate)
                ->delete();
            
            // Delete unread notifications older than specified days + 7 extra days
            $deletedUnread = DB::table('user_session_notifications')
                ->where('is_read', false)
                ->where('created_at', '<', $cutoffDate->subDays(7))
                ->delete();
            
            DB::commit();
            
            $totalDeleted = $deletedRead + $deletedUnread;
            
            $this->info("✅ Cleanup completed successfully!");
            $this->info("📊 Deleted {$totalDeleted} notifications:");
            $this->info("   - {$deletedRead} read notifications");
            $this->info("   - {$deletedUnread} old unread notifications");
            
            // Log the cleanup activity
            Log::info('Session notifications cleanup completed', [
                'deleted_read' => $deletedRead,
                'deleted_unread' => $deletedUnread,
                'total_deleted' => $totalDeleted,
                'cutoff_date' => $cutoffDate->toDateTimeString(),
                'days_retention' => $days
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            $this->error("❌ Cleanup failed: " . $e->getMessage());
            Log::error('Session notifications cleanup failed', [
                'error' => $e->getMessage(),
                'cutoff_date' => $cutoffDate->toDateTimeString()
            ]);
            return 1;
        }
        
        return 0;
    }
}
