<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Jobs\SendInactiveUserReminderJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendInactiveUserReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:inactive-user-reminders {--days=15 : Days of inactivity before sending reminder} {--limit=50 : Number of users to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder emails to users who have deposits but no investments and are inactive for specified days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $limit = $this->option('limit');
        
        $this->info("🔍 Finding users inactive for {$days}+ days with deposits but no investments...");
        
        // Date threshold for inactivity
        $inactiveDate = Carbon::now()->subDays($days);
        
        // Get users who:
        // - Have deposits but no active investments
        // - Are inactive for specified days
        // - Are active and email verified
        $users = User::where('status', 1) // Active users
            ->whereNotNull('email_verified_at') // Email verified
            ->whereNotNull('email')
            ->whereHas('deposits') // Have made deposits
            ->whereDoesntHave('invests', function($query) {
                $query->where('status', 1); // No active investments
            })
            ->where(function($query) use ($inactiveDate) {
                $query->where('last_login_at', '<', $inactiveDate)
                      ->orWhereNull('last_login_at');
            })
            ->limit($limit)
            ->get();

        if ($users->isEmpty()) {
            $this->info("✅ No inactive users found with deposits but no investments.");
            return Command::SUCCESS;
        }

        $this->info("📧 Processing {$users->count()} inactive users for reminder emails...");
        
        $successCount = 0;
        $failureCount = 0;

        foreach ($users as $user) {
            try {
                $daysSinceLogin = $user->last_login_at 
                    ? $user->last_login_at->diffInDays(now()) 
                    : 999;
                
                // Dispatch the job to queue
                SendInactiveUserReminderJob::dispatch($user)->onQueue('emails');
                
                $this->line("✅ Queued reminder for: {$user->username} ({$user->email}) - {$daysSinceLogin} days inactive");
                $successCount++;
                
            } catch (\Exception $e) {
                $this->error("❌ Failed to queue reminder for {$user->username}: {$e->getMessage()}");
                $failureCount++;
                
                Log::error('Failed to queue inactive user reminder', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->newLine();
        $this->info("📊 Summary:");
        $this->info("   ✅ Successfully queued: {$successCount}");
        
        if ($failureCount > 0) {
            $this->warn("   ❌ Failed to queue: {$failureCount}");
        }
        
        $this->info("🚀 All inactive user reminder emails have been queued for processing.");
        $this->info("💡 Tip: Monitor the queue with: php artisan queue:work");

        return Command::SUCCESS;
    }
}
