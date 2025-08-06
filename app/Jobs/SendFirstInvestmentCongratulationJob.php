<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Invest;
use App\Mail\FirstInvestmentCongratulationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendFirstInvestmentCongratulationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $investment;

    /**
     * Job timeout in seconds
     */
    public $timeout = 60;

    /**
     * Number of times the job may be attempted
     */
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, Invest $investment)
    {
        $this->user = $user;
        $this->investment = $investment;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Double check this is their first investment
            $firstInvestment = Invest::where('user_id', $this->user->id)
                ->where('status', 1)
                ->orderBy('id', 'asc')
                ->first();

            if ($firstInvestment && $firstInvestment->id === $this->investment->id) {
                Mail::to($this->user->email)->send(new FirstInvestmentCongratulationMail($this->user, $this->investment));
                
                Log::info('First investment congratulation email sent', [
                    'user_id' => $this->user->id,
                    'email' => $this->user->email,
                    'username' => $this->user->username,
                    'investment_id' => $this->investment->id,
                    'amount' => $this->investment->amount,
                    'plan' => $this->investment->plan->name ?? 'Unknown'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send first investment congratulation email', [
                'user_id' => $this->user->id,
                'investment_id' => $this->investment->id,
                'error' => $e->getMessage()
            ]);
            
            throw $e; // Re-throw to trigger job retry
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('First investment congratulation job failed permanently', [
            'user_id' => $this->user->id,
            'investment_id' => $this->investment->id,
            'error' => $exception->getMessage()
        ]);
    }
}
