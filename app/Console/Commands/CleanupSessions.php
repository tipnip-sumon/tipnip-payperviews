<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanupSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'session:cleanup {--force : Force cleanup without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old and duplicate sessions to prevent database bloat';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🧹 Starting session cleanup...');
        
        try {
            // Get statistics before cleanup
            $totalBefore = DB::table('sessions')->count();
            $this->info("📊 Total sessions before cleanup: {$totalBefore}");
            
            // 1. Delete sessions older than 2 hours (expired sessions)
            $expiredTime = now()->subHours(2)->timestamp;
            $deletedExpired = DB::table('sessions')
                ->where('last_activity', '<', $expiredTime)
                ->delete();
            
            $this->info("🗑️  Deleted {$deletedExpired} expired sessions (older than 2 hours)");
            
            // 2. Find and remove duplicate sessions for same IP address
            $duplicateQuery = "
                DELETE s1 FROM sessions s1
                INNER JOIN sessions s2 
                WHERE s1.id < s2.id 
                AND s1.ip_address = s2.ip_address 
                AND s1.user_id IS NULL 
                AND s2.user_id IS NULL
            ";
            
            $deletedDuplicates = DB::delete($duplicateQuery);
            $this->info("🔄 Deleted {$deletedDuplicates} duplicate guest sessions");
            
            // 3. For authenticated users, keep only the most recent session per user
            $userDuplicateQuery = "
                DELETE s1 FROM sessions s1
                INNER JOIN sessions s2 
                WHERE s1.id < s2.id 
                AND s1.user_id = s2.user_id 
                AND s1.user_id IS NOT NULL
            ";
            
            $deletedUserDuplicates = DB::delete($userDuplicateQuery);
            $this->info("👤 Deleted {$deletedUserDuplicates} duplicate user sessions");
            
            // 4. Delete sessions with malformed data
            $deletedMalformed = DB::table('sessions')
                ->whereNull('id')
                ->orWhere('id', '')
                ->orWhereNull('payload')
                ->orWhere('payload', '')
                ->delete();
            
            if ($deletedMalformed > 0) {
                $this->info("⚠️  Deleted {$deletedMalformed} malformed sessions");
            }
            
            // Get statistics after cleanup
            $totalAfter = DB::table('sessions')->count();
            $totalDeleted = $totalBefore - $totalAfter;
            
            $this->info("📊 Total sessions after cleanup: {$totalAfter}");
            $this->info("✅ Cleanup completed! Deleted {$totalDeleted} sessions total");
            
            // Log the cleanup activity
            Log::info('Session cleanup completed', [
                'before_count' => $totalBefore,
                'after_count' => $totalAfter,
                'deleted_expired' => $deletedExpired,
                'deleted_duplicates' => $deletedDuplicates,
                'deleted_user_duplicates' => $deletedUserDuplicates,
                'deleted_malformed' => $deletedMalformed,
                'total_deleted' => $totalDeleted
            ]);
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error("❌ Session cleanup failed: " . $e->getMessage());
            
            Log::error('Session cleanup failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return Command::FAILURE;
        }
    }
}
