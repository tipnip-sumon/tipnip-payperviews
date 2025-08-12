<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanGuestSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'session:clean-guests {--hours=1 : Hours after which guest sessions expire}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up guest sessions (sessions without user_id) older than specified hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = (int) $this->option('hours');
        $cutoffTime = time() - ($hours * 3600);
        
        $this->info("Cleaning guest sessions older than {$hours} hour(s)...");
        
        // Get count before deletion
        $totalGuestSessions = DB::table('sessions')
            ->where('user_id', null)
            ->count();
            
        $oldGuestSessions = DB::table('sessions')
            ->where('user_id', null)
            ->where('last_activity', '<', $cutoffTime)
            ->count();
        
        // Delete old guest sessions
        $deleted = DB::table('sessions')
            ->where('user_id', null)
            ->where('last_activity', '<', $cutoffTime)
            ->delete();
        
        $remaining = $totalGuestSessions - $deleted;
        
        $this->info("Found {$totalGuestSessions} total guest sessions");
        $this->info("Deleted {$deleted} old guest sessions");
        $this->info("Remaining guest sessions: {$remaining}");
        
        // Show current session summary
        $userSessions = DB::table('sessions')
            ->whereNotNull('user_id')
            ->count();
            
        $this->info("Current session summary:");
        $this->info("- User sessions: {$userSessions}");
        $this->info("- Guest sessions: {$remaining}");
        $this->info("- Total sessions: " . ($userSessions + $remaining));
        
        return 0;
    }
}
