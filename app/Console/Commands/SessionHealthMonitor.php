<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SessionHealthMonitor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'session:health-monitor {--alert-threshold=100 : Alert if more than this many sessions exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor session health and alert if there are too many active sessions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $alertThreshold = $this->option('alert-threshold');
        $currentTime = time();
        
        try {
            // Get session statistics
            $totalSessions = DB::table('sessions')->count();
            $guestSessions = DB::table('sessions')->whereNull('user_id')->count();
            $userSessions = DB::table('sessions')->whereNotNull('user_id')->count();
            
            // Check for very old sessions (older than 12 hours)
            $oldSessions = DB::table('sessions')
                ->where('last_activity', '<', $currentTime - (12 * 3600))
                ->count();
            
            // Check for extremely old sessions (older than 24 hours)
            $veryOldSessions = DB::table('sessions')
                ->where('last_activity', '<', $currentTime - (24 * 3600))
                ->count();
            
            $this->info("=== Session Health Monitor ===");
            $this->info("Total sessions: {$totalSessions}");
            $this->info("Guest sessions: {$guestSessions}");
            $this->info("User sessions: {$userSessions}");
            $this->info("Old sessions (12h+): {$oldSessions}");
            $this->info("Very old sessions (24h+): {$veryOldSessions}");
            
            // Log the statistics
            Log::info('Session health check', [
                'total_sessions' => $totalSessions,
                'guest_sessions' => $guestSessions,
                'user_sessions' => $userSessions,
                'old_sessions_12h' => $oldSessions,
                'very_old_sessions_24h' => $veryOldSessions,
                'alert_threshold' => $alertThreshold
            ]);
            
            // Alert if too many sessions
            if ($totalSessions > $alertThreshold) {
                $this->warn("⚠️  ALERT: {$totalSessions} sessions detected (threshold: {$alertThreshold})");
                Log::warning('High session count detected', [
                    'total_sessions' => $totalSessions,
                    'threshold' => $alertThreshold,
                    'old_sessions' => $oldSessions,
                    'very_old_sessions' => $veryOldSessions
                ]);
            }
            
            // Alert if too many old sessions
            if ($veryOldSessions > 10) {
                $this->warn("⚠️  ALERT: {$veryOldSessions} very old sessions (24h+) detected");
                Log::warning('Very old sessions detected', [
                    'very_old_sessions' => $veryOldSessions,
                    'recommendation' => 'Consider running session:cleanup'
                ]);
            }
            
            $this->info("✅ Session health monitoring completed");
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error("❌ Session health monitoring failed: " . $e->getMessage());
            Log::error('Session health monitoring failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }
    }
}
