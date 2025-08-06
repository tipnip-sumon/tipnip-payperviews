<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Jobs\SendKycPendingReminderJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendKycPendingReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:kyc-pending-reminders {--limit=50 : Number of users to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send KYC pending reminder emails to users who haven\'t completed KYC verification';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = $this->option('limit');
        
        $this->info('🔍 Finding users with pending KYC verification...');
        
        // Get users who need KYC verification
        $users = User::where('kv', 0) // KYC not verified
            ->where('status', 1) // Active users only
            ->whereNotNull('email_verified_at') // Email verified users only
            ->whereNotNull('email')
            ->limit($limit)
            ->get();

        if ($users->isEmpty()) {
            $this->info('✅ No users found with pending KYC verification.');
            return Command::SUCCESS;
        }

        $this->info("📧 Processing {$users->count()} users for KYC reminder emails...");
        
        $successCount = 0;
        $failureCount = 0;

        foreach ($users as $user) {
            try {
                // Dispatch the job to queue
                SendKycPendingReminderJob::dispatch($user)->onQueue('emails');
                
                $this->line("✅ Queued KYC reminder for: {$user->username} ({$user->email})");
                $successCount++;
                
            } catch (\Exception $e) {
                $this->error("❌ Failed to queue KYC reminder for {$user->username}: {$e->getMessage()}");
                $failureCount++;
                
                Log::error('Failed to queue KYC reminder', [
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
        
        $this->info("🚀 All KYC reminder emails have been queued for processing.");
        $this->info("💡 Tip: Monitor the queue with: php artisan queue:work");

        return Command::SUCCESS;
    }
}
