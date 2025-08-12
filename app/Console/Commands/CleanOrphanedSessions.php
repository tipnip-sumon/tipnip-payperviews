<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanOrphanedSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'session:clean-orphaned {--days=1 : Days after which sessions expire}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up orphaned sessions and sessions for users who no longer exist';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $cutoffTime = time() - ($days * 24 * 3600);
        
        $this->info("Cleaning orphaned sessions older than {$days} day(s)...");
        
        // Clean sessions for users that no longer exist
        $orphanedSessions = DB::table('sessions')
            ->leftJoin('users', 'sessions.user_id', '=', 'users.id')
            ->whereNotNull('sessions.user_id')
            ->whereNull('users.id')
            ->count();
            
        if ($orphanedSessions > 0) {
            $deleted = DB::table('sessions')
                ->leftJoin('users', 'sessions.user_id', '=', 'users.id')
                ->whereNotNull('sessions.user_id')
                ->whereNull('users.id')
                ->delete();
                
            $this->info("Deleted {$deleted} sessions for non-existent users");
        }
        
        // Clean old sessions regardless of user
        $oldSessions = DB::table('sessions')
            ->where('last_activity', '<', $cutoffTime)
            ->count();
            
        if ($oldSessions > 0) {
            $deletedOld = DB::table('sessions')
                ->where('last_activity', '<', $cutoffTime)
                ->delete();
                
            $this->info("Deleted {$deletedOld} sessions older than {$days} day(s)");
        }
        
        // Clean sessions where users have multiple sessions (keep only the latest)
        $duplicateUserSessions = DB::select("
            SELECT user_id, COUNT(*) as session_count 
            FROM sessions 
            WHERE user_id IS NOT NULL 
            GROUP BY user_id 
            HAVING COUNT(*) > 1
        ");
        
        $totalDuplicatesRemoved = 0;
        foreach ($duplicateUserSessions as $userSession) {
            // Keep only the most recent session for each user
            $sessionsToDelete = DB::table('sessions')
                ->where('user_id', $userSession->user_id)
                ->orderBy('last_activity', 'desc')
                ->skip(1) // Skip the most recent one
                ->pluck('id');
                
            if ($sessionsToDelete->isNotEmpty()) {
                $deleted = DB::table('sessions')
                    ->whereIn('id', $sessionsToDelete)
                    ->delete();
                    
                $totalDuplicatesRemoved += $deleted;
            }
        }
        
        if ($totalDuplicatesRemoved > 0) {
            $this->info("Removed {$totalDuplicatesRemoved} duplicate user sessions (kept latest for each user)");
        }
        
        // Show final summary
        $remainingSessions = DB::table('sessions')->count();
        $userSessions = DB::table('sessions')->whereNotNull('user_id')->count();
        $guestSessions = DB::table('sessions')->whereNull('user_id')->count();
        
        $this->info("Session cleanup completed!");
        $this->info("Final session summary:");
        $this->info("- User sessions: {$userSessions}");
        $this->info("- Guest sessions: {$guestSessions}");
        $this->info("- Total sessions: {$remainingSessions}");
        
        return 0;
    }
}
