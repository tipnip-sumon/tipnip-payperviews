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

class SendMonthlyPasswordResetJob implements ShouldQueue
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
            // Check if user needs password reset reminder
            $needsPasswordReset = $this->user->status == 1 &&
                                 ($this->user->password_changed_at === null || 
                                  $this->user->password_changed_at < now()->subDays(30));

            if ($needsPasswordReset) {
                // Send monthly password reset reminder email
                Mail::send('emails.monthly-password-reset', [
                    'user' => $this->user,
                    'site_name' => config('app.name'),
                    'login_url' => route('login'),
                    'profile_url' => route('user.profile'),
                    'days_since_change' => $this->user->password_changed_at ? 
                        now()->diffInDays($this->user->password_changed_at) : 'many',
                ], function ($message) {
                    $message->to($this->user->email, $this->user->firstname . ' ' . $this->user->lastname)
                            ->subject('Security Reminder: Update Your Password - ' . config('app.name'))
                            ->from(config('mail.from.address'), config('mail.from.name'));
                });

                Log::info('Monthly password reset reminder email sent successfully', [
                    'user_id' => $this->user->id,
                    'email' => $this->user->email,
                    'password_changed_at' => $this->user->password_changed_at,
                    'timestamp' => now()
                ]);
            } else {
                Log::info('Monthly password reset reminder skipped - user no longer eligible', [
                    'user_id' => $this->user->id,
                    'user_status' => $this->user->status,
                    'password_changed_at' => $this->user->password_changed_at
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send monthly password reset reminder email', [
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
        Log::error('Monthly password reset reminder job failed permanently', [
            'user_id' => $this->user->id,
            'email' => $this->user->email,
            'error' => $exception->getMessage(),
            'timestamp' => now()
        ]);
    }
}
