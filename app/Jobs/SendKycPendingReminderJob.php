<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendKycPendingReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;
    
    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->onQueue('emails');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Check if user still needs KYC
            if ($this->user->kv == 0 && $this->user->status == 1) {
                // Send KYC reminder email
                Mail::send('emails.kyc-pending-reminder', [
                    'user' => $this->user,
                    'settings' => (object)['site_name' => config('app.name')],
                    'kycUrl' => route('user.kyc.create'),
                    'loginUrl' => route('login'),
                    'supportEmail' => config('mail.from.address', 'support@' . config('app.url')),
                ], function ($message) {
                    $message->to($this->user->email, $this->user->firstname . ' ' . $this->user->lastname)
                            ->subject('Complete Your KYC Verification - ' . config('app.name'))
                            ->from(config('mail.from.address'), config('mail.from.name'));
                });

                Log::info('KYC reminder email sent successfully', [
                    'user_id' => $this->user->id,
                    'email' => $this->user->email,
                    'timestamp' => now()
                ]);
            } else {
                Log::info('KYC reminder skipped - user no longer eligible', [
                    'user_id' => $this->user->id,
                    'kv_status' => $this->user->kv,
                    'user_status' => $this->user->status
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send KYC reminder email', [
                'user_id' => $this->user->id,
                'email' => $this->user->email,
                'error' => $e->getMessage(),
                'timestamp' => now()
            ]);
            
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('KYC reminder job failed permanently', [
            'user_id' => $this->user->id,
            'email' => $this->user->email,
            'error' => $exception->getMessage(),
            'timestamp' => now()
        ]);
    }
}
