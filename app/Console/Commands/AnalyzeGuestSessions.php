<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AnalyzeGuestSessions extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'session:analyze-guests {--detailed : Show detailed session data}';

    /**
     * The console command description.
     */
    protected $description = 'Analyze guest sessions to understand their origin and activity';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("=== GUEST SESSION ANALYSIS ===");
        $this->info("Analyzing sessions where user_id IS NULL...\n");
        
        try {
            $currentTime = time();
            
            // Get all guest sessions
            $guestSessions = DB::table('sessions')
                ->whereNull('user_id')
                ->orderBy('last_activity', 'desc')
                ->get();
                
            $this->info("Found {$guestSessions->count()} guest sessions\n");
            
            if ($guestSessions->isEmpty()) {
                $this->info("✅ No guest sessions found - all clean!");
                return Command::SUCCESS;
            }
            
            // Analyze session patterns
            $this->analyzeSessionPatterns($guestSessions, $currentTime);
            
            if ($this->option('detailed')) {
                $this->showDetailedSessions($guestSessions, $currentTime);
            }
            
            // Show recommendations
            $this->showRecommendations($guestSessions, $currentTime);
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error("❌ Analysis failed: " . $e->getMessage());
            Log::error('Guest session analysis failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }
    }
    
    private function analyzeSessionPatterns($sessions, $currentTime)
    {
        $this->info("📊 SESSION PATTERNS:");
        
        // Age analysis
        $ageGroups = [
            'active' => 0,      // < 30 minutes
            'recent' => 0,      // 30 minutes - 2 hours
            'old' => 0,         // 2 - 24 hours
            'very_old' => 0     // > 24 hours
        ];
        
        // IP address tracking
        $ipCounts = [];
        $userAgentCounts = [];
        
        foreach ($sessions as $session) {
            $age = ($currentTime - $session->last_activity) / 60; // minutes
            
            // Categorize by age
            if ($age < 30) {
                $ageGroups['active']++;
            } elseif ($age < 120) {
                $ageGroups['recent']++;
            } elseif ($age < 1440) {
                $ageGroups['old']++;
            } else {
                $ageGroups['very_old']++;
            }
            
            // Track IP addresses (extract from session data if available)
            try {
                $payload = base64_decode($session->payload);
                if ($payload && strpos($payload, 'ip_address') !== false) {
                    // Try to extract IP from session payload
                    if (preg_match('/ip_address[^:]*:s:\d+:"([^"]+)"/', $payload, $matches)) {
                        $ip = $matches[1];
                        $ipCounts[$ip] = ($ipCounts[$ip] ?? 0) + 1;
                    }
                }
                
                // Try to extract user agent
                if (preg_match('/user_agent[^:]*:s:\d+:"([^"]+)"/', $payload, $matches)) {
                    $userAgent = substr($matches[1], 0, 50); // Truncate for display
                    $userAgentCounts[$userAgent] = ($userAgentCounts[$userAgent] ?? 0) + 1;
                }
            } catch (\Exception $e) {
                // Ignore payload parsing errors
            }
        }
        
        // Display age analysis
        $this->table(['Age Group', 'Count', 'Description'], [
            ['Active', $ageGroups['active'], '< 30 minutes (current visitors)'],
            ['Recent', $ageGroups['recent'], '30 min - 2 hours (recent visitors)'],
            ['Old', $ageGroups['old'], '2 - 24 hours (should be cleaned)'],
            ['Very Old', $ageGroups['very_old'], '> 24 hours (definitely should be cleaned)']
        ]);
        
        // Show top IP addresses
        if (!empty($ipCounts)) {
            $this->info("\n🌐 TOP IP ADDRESSES:");
            arsort($ipCounts);
            $topIPs = array_slice($ipCounts, 0, 5, true);
            foreach ($topIPs as $ip => $count) {
                $this->line("   {$ip}: {$count} sessions");
            }
        }
        
        // Show user agent patterns
        if (!empty($userAgentCounts)) {
            $this->info("\n🤖 USER AGENT PATTERNS:");
            arsort($userAgentCounts);
            $topAgents = array_slice($userAgentCounts, 0, 3, true);
            foreach ($topAgents as $agent => $count) {
                $this->line("   {$count}x: " . substr($agent, 0, 60) . "...");
            }
        }
    }
    
    private function showDetailedSessions($sessions, $currentTime)
    {
        $this->info("\n📋 DETAILED SESSION DATA:");
        
        foreach ($sessions as $index => $session) {
            if ($index >= 10) { // Limit to 10 sessions for readability
                $this->info("... and " . ($sessions->count() - 10) . " more sessions");
                break;
            }
            
            $age = round(($currentTime - $session->last_activity) / 60, 1);
            $lastActivity = date('Y-m-d H:i:s', $session->last_activity);
            $sessionId = substr($session->id, 0, 12) . '...';
            
            $this->line("Session {$sessionId}: {$age}m ago ({$lastActivity})");
            
            // Try to decode session payload for more info
            try {
                $payload = base64_decode($session->payload);
                if ($payload) {
                    // Look for common session keys
                    $sessionData = [];
                    if (strpos($payload, '_token') !== false) {
                        $sessionData[] = 'CSRF Token';
                    }
                    if (strpos($payload, 'flash') !== false) {
                        $sessionData[] = 'Flash Messages';
                    }
                    if (strpos($payload, 'cart') !== false) {
                        $sessionData[] = 'Shopping Cart';
                    }
                    if (strpos($payload, 'intended') !== false) {
                        $sessionData[] = 'Intended URL';
                    }
                    
                    if (!empty($sessionData)) {
                        $this->line("   Contains: " . implode(', ', $sessionData));
                    }
                }
            } catch (\Exception $e) {
                // Ignore payload errors
            }
        }
    }
    
    private function showRecommendations($sessions, $currentTime)
    {
        $this->info("\n💡 RECOMMENDATIONS:");
        
        $veryOldCount = $sessions->filter(function ($session) use ($currentTime) {
            return ($currentTime - $session->last_activity) > 86400; // 24 hours
        })->count();
        
        $oldCount = $sessions->filter(function ($session) use ($currentTime) {
            $age = $currentTime - $session->last_activity;
            return $age > 7200 && $age <= 86400; // 2-24 hours
        })->count();
        
        if ($veryOldCount > 0) {
            $this->warn("⚠️  {$veryOldCount} sessions are older than 24 hours - run: php artisan session:clean-guests --hours=24");
        }
        
        if ($oldCount > 0) {
            $this->warn("⚠️  {$oldCount} sessions are 2-24 hours old - run: php artisan session:clean-guests --hours=2");
        }
        
        if ($sessions->count() > 10) {
            $this->warn("⚠️  High number of guest sessions detected - consider:");
            $this->line("   • Checking for bot traffic");
            $this->line("   • Reducing session lifetime");
            $this->line("   • Running cleanup more frequently");
        }
        
        $activeCount = $sessions->filter(function ($session) use ($currentTime) {
            return ($currentTime - $session->last_activity) < 1800; // 30 minutes
        })->count();
        
        if ($activeCount > 0) {
            $this->info("✅ {$activeCount} sessions are active (< 30 minutes) - these are likely real visitors");
        }
    }
}
