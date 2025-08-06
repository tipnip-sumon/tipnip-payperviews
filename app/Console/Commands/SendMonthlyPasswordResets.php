<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Jobs\SendMonthlyPasswordResetJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendMonthlyPasswordResets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:monthly-password-resets {--limit=100 : Number of users to process} {--force : Force password reset regardless of last reset date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send monthly password reset emails to active users for enhanced security';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = $this->option('limit');
        $force = $this->option('force');
        
        $this->info('🔒 Processing monthly password resets for enhanced security...');
        
        // Build query for users who need password reset
        $query = User::where('status', 1) // Active users only
            ->whereNotNull('email_verified_at') // Email verified users only
            ->whereNotNull('email');

        if (!$force) {
            // Only reset for users whose password hasn't been reset in the last 30 days
            $oneMonthAgo = Carbon::now()->subDays(30);
            $query->where(function($q) use ($oneMonthAgo) {
                $q->whereNull('password_changed_at')
                  ->orWhere('password_changed_at', '<', $oneMonthAgo);
            });
        }

        $users = $query->limit($limit)->get();

        if ($users->isEmpty()) {
            $this->info('✅ No users found that need monthly password reset.');
            return Command::SUCCESS;
        }

        $this->info("🔐 Processing {$users->count()} users for monthly password reset...");
        
        if (!$force) {
            $this->warn('⚠️  This will reset passwords for active users!');
            if (!$this->confirm('Do you want to continue?')) {
                $this->info('🚫 Password reset cancelled.');
                return Command::SUCCESS;
            }
        }

        $successCount = 0;
        $failureCount = 0;

        foreach ($users as $user) {
            try {
                // Dispatch the job to queue
                SendMonthlyPasswordResetJob::dispatch($user)->onQueue('emails');
                
                $this->line("✅ Queued password reset for: {$user->username} ({$user->email})");
                $successCount++;
                
            } catch (\Exception $e) {
                $this->error("❌ Failed to queue password reset for {$user->username}: {$e->getMessage()}");
                $failureCount++;
                
                Log::error('Failed to queue monthly password reset', [
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
        
        $this->info("🚀 All monthly password reset emails have been queued for processing.");
        $this->info("🔒 Users will receive new passwords and be required to change them on login.");
        $this->info("💡 Tip: Monitor the queue with: php artisan queue:work");

        return Command::SUCCESS;
    }
}
