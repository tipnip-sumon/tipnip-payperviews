<?php

namespace App\Services;

use App\Models\User;
use App\Models\Transaction;
use App\Models\DailyVideoAssignment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DailyVideoEarningService
{
    /**
     * Add earning to daily accumulation and update user wallet
     */
    public function addEarning(User $user, float $earningAmount, ?Carbon $date = null): array
    {
        $date = $date ?? today();
        
        try {
            DB::beginTransaction();
            
            // Add to user's interest wallet immediately for real-time balance update
            DB::table('users')->where('id', $user->id)->increment('interest_wallet', $earningAmount);
            $currentBalance = DB::table('users')->where('id', $user->id)->value('interest_wallet');
            
            // Update or create today's daily earning transaction
            $this->updateDailyTransaction($user, $earningAmount, $date, $currentBalance);
            
            DB::commit();
            
            return [
                'success' => true,
                'current_balance' => $currentBalance,
                'daily_earning_added' => $earningAmount
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding daily video earning', [
                'user_id' => $user->id,
                'amount' => $earningAmount,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Update or create the daily video earning transaction
     */
    private function updateDailyTransaction(User $user, float $additionalAmount, Carbon $date, float $currentBalance): void
    {
        $dateString = $date->toDateString();
        
        // Check if daily transaction already exists
        $existingTransaction = Transaction::where('user_id', $user->id)
            ->where('wallet_type', 'interest_wallet')
            ->where('remark', 'daily_video_earning_summary')
            ->whereDate('created_at', $date)
            ->first();
            
        if ($existingTransaction) {
            // Update existing transaction
            $newAmount = $existingTransaction->amount + $additionalAmount;
            $videosWatched = $this->getTodaysWatchedCount($user, $date);
            
            $existingTransaction->amount = $newAmount;
            $existingTransaction->post_balance = $currentBalance;
            $existingTransaction->details = $this->generateTransactionDetails($user, $newAmount, $videosWatched, $date);
            $existingTransaction->updated_at = now();
            $existingTransaction->save();
            
            Log::info('Updated daily video earning transaction', [
                'user_id' => $user->id,
                'transaction_id' => $existingTransaction->id,
                'new_total_amount' => $newAmount,
                'added_amount' => $additionalAmount,
                'videos_watched' => $videosWatched
            ]);
            
        } else {
            // Create new daily transaction
            $videosWatched = $this->getTodaysWatchedCount($user, $date);
            
            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->amount = $additionalAmount;
            $transaction->charge = 0;
            $transaction->trx_type = '+';
            $transaction->trx = getTrx();
            $transaction->wallet_type = 'interest_wallet';
            $transaction->remark = 'daily_video_earning_summary';
            $transaction->details = $this->generateTransactionDetails($user, $additionalAmount, $videosWatched, $date);
            $transaction->post_balance = $currentBalance;
            $transaction->save();
            
            Log::info('Created daily video earning transaction', [
                'user_id' => $user->id,
                'transaction_id' => $transaction->id,
                'amount' => $additionalAmount,
                'videos_watched' => $videosWatched
            ]);
        }
    }
    
    /**
     * Get today's watched video count using optimized structure
     */
    private function getTodaysWatchedCount(User $user, Carbon $date): int
    {
        $assignment = DailyVideoAssignment::forUser($user->id)
            ->forDate($date)
            ->first();
            
        return $assignment ? ($assignment->watched_count ?? 0) : 0;
    }
    
    /**
     * Generate detailed transaction description
     */
    private function generateTransactionDetails(User $user, float $totalAmount, int $videosWatched, Carbon $date): string
    {
        $planName = 'Free Plan';
        $earningRate = 0.0001;
        
        // Get user's active plan
        $activeInvest = \App\Models\Invest::where('user_id', $user->id)
            ->where('status', 1)
            ->with('plan')
            ->first();
            
        if ($activeInvest && $activeInvest->plan) {
            $planName = $activeInvest->plan->name;
            $earningRate = $activeInvest->plan->video_earning_rate ?? 0.0001;
        }
        
        return sprintf(
            'Daily video earnings for %s: %d videos watched, $%s per video (Plan: %s)',
            $date->format('M d, Y'),
            $videosWatched,
            number_format($earningRate, 4),
            $planName
        );
    }
    
    /**
     * Get today's total video earnings for a user
     */
    public function getTodaysTotalEarnings(User $user, ?Carbon $date = null): float
    {
        $date = $date ?? today();
        
        $transaction = Transaction::where('user_id', $user->id)
            ->where('wallet_type', 'interest_wallet')
            ->where('remark', 'daily_video_earning_summary')
            ->whereDate('created_at', $date)
            ->first();
            
        return $transaction ? $transaction->amount : 0;
    }
    
    /**
     * Get summary of daily earnings for a date range
     */
    public function getEarningsSummary(User $user, Carbon $startDate, Carbon $endDate): array
    {
        $earnings = Transaction::where('user_id', $user->id)
            ->where('wallet_type', 'interest_wallet')
            ->where('remark', 'daily_video_earning_summary')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return [
            'total_days' => $earnings->count(),
            'total_earnings' => $earnings->sum('amount'),
            'average_per_day' => $earnings->count() > 0 ? $earnings->avg('amount') : 0,
            'highest_day' => $earnings->max('amount') ?? 0,
            'daily_breakdown' => $earnings->toArray()
        ];
    }
    
    /**
     * Get total video earnings for a user (all time)
     */
    public function getTotalEarnings($userId): float
    {
        return Transaction::where('user_id', $userId)
            ->where('wallet_type', 'interest_wallet')
            ->where('remark', 'daily_video_earning_summary')
            ->sum('amount');
    }
}
