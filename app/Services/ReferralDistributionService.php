<?php

namespace App\Services;

use App\Models\User;
use App\Models\DailyVideoAssignment;
use App\Models\ReferralCommission;
use App\Models\CommissionLevelSetting;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ReferralDistributionService
{
    /**
     * Get commission level percentages from database settings
     */
    private function getLevelPercentages(): array
    {
        $activeLevels = CommissionLevelSetting::getActiveLevels();
        
        // If no active levels found, use defaults
        if (empty($activeLevels)) {
            Log::warning('No active commission levels found, using defaults');
            return CommissionLevelSetting::getDefaultLevels();
        }
        
        return $activeLevels;
    }

    /**
     * Distribute referral commissions for a video earning
     */
    public function distributeCommissions(DailyVideoAssignment $assignment): array
    {
        if (!$assignment->is_watched || !$assignment->earning_amount) {
            return [
                'success' => false,
                'message' => 'Assignment must be watched with earning amount'
            ];
        }

        $earner = $assignment->user;
        $originalEarning = $assignment->earning_amount;
        $totalDistributed = 0;
        $distributedCommissions = [];

        try {
            DB::beginTransaction();

            // Find referral chain (up to 7 levels)
            $referralChain = $this->getReferralChain($earner, 7);

            foreach ($referralChain as $level => $referrer) {
                $levelPercentages = $this->getLevelPercentages();
                $commissionPercentage = $levelPercentages[$level] ?? 0;
                $commissionAmount = ($originalEarning * $commissionPercentage) / 100;

                if ($commissionAmount > 0) {
                    // Create commission record
                    $commission = ReferralCommission::create([
                        'earner_user_id' => $earner->id,
                        'referrer_user_id' => $referrer->id,
                        'daily_video_assignment_id' => $assignment->id,
                        'level' => $level,
                        'original_earning' => $originalEarning,
                        'commission_percentage' => $commissionPercentage,
                        'commission_amount' => $commissionAmount,
                        'commission_type' => 'video_earning',
                        'distributed_at' => now()
                    ]);

                    // Add commission to referrer's wallet
                    $this->addCommissionToWallet($referrer, $commissionAmount, $commission, $earner);

                    $totalDistributed += $commissionAmount;
                    $distributedCommissions[] = [
                        'level' => $level,
                        'referrer' => $referrer->username,
                        'percentage' => $commissionPercentage,
                        'amount' => $commissionAmount
                    ];

                    Log::info('Referral commission distributed', [
                        'earner_id' => $earner->id,
                        'referrer_id' => $referrer->id,
                        'level' => $level,
                        'amount' => $commissionAmount,
                        'assignment_id' => $assignment->id
                    ]);
                }
            }

            DB::commit();

            return [
                'success' => true,
                'total_distributed' => $totalDistributed,
                'commissions' => $distributedCommissions,
                'levels_processed' => count($referralChain)
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Referral distribution failed', [
                'assignment_id' => $assignment->id,
                'earner_id' => $earner->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to distribute commissions: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Process daily commissions for multiple assignments at once
     */
    public function processDailyCommissions(\Carbon\Carbon $date, bool $force = false): array
    {
        $query = DailyVideoAssignment::where('assignment_date', $date)
            ->where('is_watched', true)
            ->whereNotNull('earning_amount')
            ->where('earning_amount', '>', 0);
            
        if (!$force) {
            // Only process assignments that haven't had commissions distributed yet
            $query->whereDoesntHave('referralCommissions');
        }
        
        $assignments = $query->with('user')->get();
        
        $results = [
            'total_assignments' => $assignments->count(),
            'processed' => 0,
            'errors' => 0,
            'total_distributed' => 0,
            'details' => []
        ];
        
        foreach ($assignments as $assignment) {
            try {
                // Skip if already processed and not forcing
                if (!$force && $assignment->referralCommissions()->exists()) {
                    continue;
                }
                
                // Delete existing commissions if forcing reprocessing
                if ($force) {
                    $assignment->referralCommissions()->delete();
                }
                
                $result = $this->distributeCommissions($assignment);
                
                if ($result['success']) {
                    $results['processed']++;
                    $results['total_distributed'] += $result['total_distributed'];
                    $results['details'][] = [
                        'assignment_id' => $assignment->id,
                        'user' => $assignment->user->username,
                        'earning' => $assignment->earning_amount,
                        'distributed' => $result['total_distributed'],
                        'levels' => $result['levels_processed']
                    ];
                } else {
                    $results['errors']++;
                }
                
            } catch (\Exception $e) {
                $results['errors']++;
                Log::error('Daily commission processing error', [
                    'assignment_id' => $assignment->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        return $results;
    }

    /**
     * Get referral chain up to specified levels
     */
    private function getReferralChain(User $user, int $maxLevels): array
    {
        $chain = [];
        $currentUser = $user;
        $level = 1;

        while ($level <= $maxLevels && $currentUser->ref_by) {
            $referrer = User::find($currentUser->ref_by);
            if (!$referrer) {
                break;
            }

            $chain[$level] = $referrer;
            $currentUser = $referrer;
            $level++;
        }

        return $chain;
    }

    /**
     * Add commission to referrer's wallet and create transaction
     */
    private function addCommissionToWallet(User $referrer, float $amount, ReferralCommission $commission, User $earner, string $earningType = 'video_earning'): void
    {
        // Create transaction record with appropriate details
        $details = match($earningType) {
            'daily_video_total' => "L{$commission->level} referral commission from {$earner->username}'s daily video earnings",
            default => "L{$commission->level} referral commission from {$earner->username}'s video earning"
        };

        // Check for duplicate transaction to prevent double processing
        $existingTransaction = Transaction::where('user_id', $referrer->id)
            ->where('amount', $amount)
            ->where('remark', 'referral_commission')
            ->where('details', $details)
            ->whereDate('created_at', now()->toDateString())
            ->first();

        // If duplicate transaction exists, skip wallet update
        if ($existingTransaction) {
            Log::warning('Duplicate commission transaction prevented', [
                'referrer_id' => $referrer->id,
                'amount' => $amount,
                'commission_id' => $commission->id,
                'existing_transaction_id' => $existingTransaction->id
            ]);
            return;
        }

        // Add to interest wallet
        DB::table('users')->where('id', $referrer->id)->increment('interest_wallet', $amount);

        // Get updated balance
        $newBalance = DB::table('users')->where('id', $referrer->id)->value('interest_wallet');

        $transaction = new Transaction();
        $transaction->user_id = $referrer->id;
        $transaction->amount = $amount;
        $transaction->charge = 0;
        $transaction->trx_type = '+';
        $transaction->trx = getTrx();
        $transaction->wallet_type = 'interest_wallet';
        $transaction->remark = 'referral_commission';
        $transaction->details = $details;
        $transaction->post_balance = $newBalance;
        $transaction->save();
    }

    /**
     * Get referral commission statistics for a user
     */
    public function getReferralStats(User $user): array
    {
        $totalEarned = ReferralCommission::forReferrer($user->id)->sum('commission_amount');
        $todayEarned = ReferralCommission::forReferrer($user->id)
            ->whereDate('distributed_at', today())
            ->sum('commission_amount');
        
        $levelPercentages = $this->getLevelPercentages();
        $levelStats = [];
        for ($level = 1; $level <= 7; $level++) {
            $levelStats[$level] = [
                'total_amount' => ReferralCommission::forReferrer($user->id)
                    ->forLevel($level)
                    ->sum('commission_amount'),
                'total_count' => ReferralCommission::forReferrer($user->id)
                    ->forLevel($level)
                    ->count(),
                'percentage' => $levelPercentages[$level] ?? 0
            ];
        }

        return [
            'total_earned' => $totalEarned,
            'today_earned' => $todayEarned,
            'level_stats' => $levelStats,
            'total_referrals' => $user->referrals()->count(),
            'active_levels' => collect($levelStats)->where('total_count', '>', 0)->count()
        ];
    }

    /**
     * Get commission breakdown for a specific assignment
     */
    public function getAssignmentCommissions(DailyVideoAssignment $assignment): array
    {
        $commissions = ReferralCommission::where('daily_video_assignment_id', $assignment->id)
            ->with(['referrer'])
            ->orderBy('level')
            ->get();

        return $commissions->map(function ($commission) {
            return [
                'level' => $commission->level,
                'referrer' => $commission->referrer->username,
                'percentage' => $commission->commission_percentage,
                'amount' => $commission->commission_amount,
                'distributed_at' => $commission->distributed_at
            ];
        })->toArray();
    }

    /**
     * Get level percentages configuration
     */
    public function getLevelPercentagesPublic(): array
    {
        return $this->getLevelPercentages();
    }

    /**
     * Calculate total commission percentage
     */
    public function getTotalCommissionPercentage(): float
    {
        return array_sum($this->getLevelPercentages());
    }

    /**
     * Distribute referral commissions for daily total earnings
     */
    public function distributeDailyCommissions($dailyEarning): array
    {
        if (!$dailyEarning->earning_amount || $dailyEarning->earning_amount <= 0) {
            return [
                'success' => false,
                'message' => 'Daily earning amount must be greater than 0'
            ];
        }

        $earner = $dailyEarning->user;
        $originalEarning = $dailyEarning->earning_amount;
        
        // Check if this user already has daily commissions for this date
        $existingCommissions = ReferralCommission::where('earner_user_id', $earner->id)
            ->whereDate('distributed_at', $dailyEarning->assignment_date)
            ->where(function($query) {
                $query->where('earning_type', 'daily_video_total')
                      ->orWhere(function($subQuery) {
                          $subQuery->whereNull('daily_video_assignment_id')
                                   ->where('commission_type', 'daily_video_total');
                      });
            })
            ->exists();
            
        if ($existingCommissions) {
            return [
                'success' => false,
                'message' => 'Daily commissions already distributed for this user on this date',
                'total_distributed' => 0,
                'commissions' => [],
                'levels_processed' => 0
            ];
        }
        
        $totalDistributed = 0;
        $distributedCommissions = [];

        try {
            DB::beginTransaction();

            // Find referral chain (up to 7 levels)
            $referralChain = $this->getReferralChain($earner, 7);

            foreach ($referralChain as $level => $referrer) {
                $levelPercentages = $this->getLevelPercentages();
                $commissionPercentage = $levelPercentages[$level] ?? 0;
                $commissionAmount = ($originalEarning * $commissionPercentage) / 100;

                if ($commissionAmount > 0) {
                    // Create commission record for daily total
                    $commission = ReferralCommission::create([
                        'earner_user_id' => $earner->id,
                        'referrer_user_id' => $referrer->id,
                        'daily_video_assignment_id' => null, // Allow null for daily totals
                        'level' => $level,
                        'original_earning' => $originalEarning,
                        'commission_percentage' => $commissionPercentage,
                        'commission_amount' => $commissionAmount,
                        'commission_type' => 'daily_video_total',
                        'earning_type' => 'daily_video_total',
                        'earning_date' => $dailyEarning->assignment_date,
                        'distributed_at' => $dailyEarning->assignment_date // Use assignment date, not current time
                    ]);

                    // Add commission to referrer's wallet
                    $this->addCommissionToWallet($referrer, $commissionAmount, $commission, $earner, 'daily_video_total');

                    $totalDistributed += $commissionAmount;
                    $distributedCommissions[] = [
                        'level' => $level,
                        'referrer' => $referrer->username,
                        'percentage' => $commissionPercentage,
                        'amount' => $commissionAmount
                    ];

                    Log::info('Daily referral commission distributed', [
                        'earner_id' => $earner->id,
                        'referrer_id' => $referrer->id,
                        'level' => $level,
                        'amount' => $commissionAmount,
                        'earning_date' => $dailyEarning->assignment_date->format('Y-m-d'),
                        'total_daily_earning' => $originalEarning
                    ]);
                }
            }

            DB::commit();

            return [
                'success' => true,
                'total_distributed' => $totalDistributed,
                'commissions' => $distributedCommissions,
                'levels_processed' => count($referralChain)
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Daily referral distribution failed', [
                'earner_id' => $earner->id,
                'earning_date' => $dailyEarning->assignment_date->format('Y-m-d'),
                'total_earning' => $originalEarning,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to distribute daily commissions: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Check if commissions have been distributed for a specific date
     */
    public function hasProcessedDate(\Carbon\Carbon $date): bool
    {
        return DB::table('daily_commission_summaries')
            ->where('date', $date->format('Y-m-d'))
            ->exists();
    }

    /**
     * Get daily commission summary
     */
    public function getDailySummary(\Carbon\Carbon $date): ?array
    {
        $summary = DB::table('daily_commission_summaries')
            ->where('date', $date->format('Y-m-d'))
            ->first();
            
        return $summary ? (array) $summary : null;
    }
}
