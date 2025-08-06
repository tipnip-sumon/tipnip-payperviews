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

class SendInactiveUserReminderJob implements ShouldQueue
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
            // Check if user is still inactive
            $isInactive = $this->user->status == 1 &&
                         $this->user->deposits()->exists() &&
                         !$this->user->invests()->where('status', 1)->exists() &&
                         ($this->user->last_login_at === null || $this->user->last_login_at < now()->subDays(15));

            if ($isInactive) {
                // Send inactive user reminder email
                Mail::send('emails.inactive-user-reminder', [
                    'user' => $this->user,
                    'site_name' => config('app.name'),
                    'login_url' => route('login'),
                    'invest_url' => route('user.plans'),
                    'days_inactive' => $this->user->last_login_at ? 
                        now()->diffInDays($this->user->last_login_at) : 'many',
                ], function ($message) {
                    $message->to($this->user->email, $this->user->firstname . ' ' . $this->user->lastname)
                            ->subject('We Miss You! Return to ' . config('app.name'))
                            ->from(config('mail.from.address'), config('mail.from.name'));
                });

                Log::info('Inactive user reminder email sent successfully', [
                    'user_id' => $this->user->id,
                    'email' => $this->user->email,
                    'last_login' => $this->user->last_login_at,
                    'timestamp' => now()
                ]);
            } else {
                Log::info('Inactive user reminder skipped - user no longer eligible', [
                    'user_id' => $this->user->id,
                    'user_status' => $this->user->status,
                    'has_deposits' => $this->user->deposits()->exists(),
                    'has_active_invests' => $this->user->invests()->where('status', 1)->exists(),
                    'last_login' => $this->user->last_login_at
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send inactive user reminder email', [
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
        Log::error('Inactive user reminder job failed permanently', [
            'user_id' => $this->user->id,
            'email' => $this->user->email,
            'error' => $exception->getMessage(),
            'timestamp' => now()
        ]);
    }
}
