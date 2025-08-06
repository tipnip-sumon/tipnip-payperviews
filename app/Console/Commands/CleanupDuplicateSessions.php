<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanupDuplicateSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sessions:cleanup-duplicates {--force : Force cleanup without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up duplicate sessions to enforce single session per user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Scanning for duplicate user sessions...');
        
        // Get all users with active sessions
        $usersWithSessions = User::whereNotNull('current_session_id')->get();
        
        $this->info("Found {$usersWithSessions->count()} users with active sessions");
        
        $cleanedSessions = 0;
        $cleanedDbSessions = 0;
        
        foreach ($usersWithSessions as $user) {
            // Check for duplicate database sessions
            if (config('session.driver') === 'database') {
                $userSessions = DB::table('sessions')
                    ->where('user_id', $user->id)
                    ->orderBy('last_activity', 'desc')
                    ->get();
                
                if ($userSessions->count() > 1) {
                    $this->warn("User {$user->username} has {$userSessions->count()} database sessions");
                    
                    // Keep only the most recent session
                    $sessionsToDelete = $userSessions->skip(1);
                    
                    foreach ($sessionsToDelete as $session) {
                        if ($this->option('force') || $this->confirm("Delete session {$session->id} for user {$user->username}?")) {
                            DB::table('sessions')->where('id', $session->id)->delete();
                            $cleanedDbSessions++;
                            $this->info("  ✅ Deleted session {$session->id}");
                        }
                    }
                }
            }
            
            // Validate current session tracking
            if ($user->current_session_id) {
                $sessionExists = false;
                
                if (config('session.driver') === 'database') {
                    $sessionExists = DB::table('sessions')
                        ->where('user_id', $user->id)
                        ->exists();
                }
                
                if (!$sessionExists && ($this->option('force') || $this->confirm("Clear orphaned session tracking for user {$user->username}?"))) {
                    $user->invalidateSession();
                    $cleanedSessions++;
                    $this->info("  ✅ Cleared orphaned session tracking for {$user->username}");
                }
            }
        }
        
        // Additional cleanup: Find sessions with same user in payload
        if (config('session.driver') === 'database') {
            $this->info('🔍 Checking for payload-based session duplicates...');
            
            $allSessions = DB::table('sessions')->get();
            $userSessionCounts = [];
            
            foreach ($allSessions as $session) {
                try {
                    $payload = base64_decode($session->payload);
                    
                    // Extract user ID from Laravel session payload
                    if (preg_match('/login_web_[a-zA-Z0-9_]+.*?i:(\d+)/', $payload, $matches)) {
                        $userId = $matches[1];
                        
                        if (!isset($userSessionCounts[$userId])) {
                            $userSessionCounts[$userId] = [];
                        }
                        
                        $userSessionCounts[$userId][] = [
                            'id' => $session->id,
                            'last_activity' => $session->last_activity,
                            'user_id_field' => $session->user_id
                        ];
                    }
                } catch (\Exception $e) {
                    // Skip invalid payloads
                    continue;
                }
            }
            
            foreach ($userSessionCounts as $userId => $sessions) {
                if (count($sessions) > 1) {
                    $user = User::find($userId);
                    $username = $user ? $user->username : "ID:{$userId}";
                    
                    $this->warn("User {$username} has " . count($sessions) . " payload sessions");
                    
                    // Sort by last activity, keep most recent
                    usort($sessions, function($a, $b) {
                        return $b['last_activity'] <=> $a['last_activity'];
                    });
                    
                    $sessionsToDelete = array_slice($sessions, 1);
                    
                    foreach ($sessionsToDelete as $sessionData) {
                        if ($this->option('force') || $this->confirm("Delete duplicate payload session {$sessionData['id']} for user {$username}?")) {
                            DB::table('sessions')->where('id', $sessionData['id'])->delete();
                            $cleanedDbSessions++;
                            $this->info("  ✅ Deleted payload session {$sessionData['id']}");
                        }
                    }
                }
            }
        }
        
        $this->info("✅ Session cleanup completed:");
        $this->info("  - Cleaned {$cleanedSessions} user session trackings");
        $this->info("  - Cleaned {$cleanedDbSessions} database sessions");
        
        // Log the cleanup
        Log::info('Session cleanup command executed', [
            'cleaned_sessions' => $cleanedSessions,
            'cleaned_db_sessions' => $cleanedDbSessions,
            'total_users_checked' => $usersWithSessions->count(),
            'executed_by' => 'console'
        ]);
        
        if ($cleanedSessions > 0 || $cleanedDbSessions > 0) {
            $this->info("🔒 Single session enforcement strengthened!");
        } else {
            $this->info("✅ No duplicate sessions found - system is clean!");
        }
        
        return Command::SUCCESS;
    }
}
