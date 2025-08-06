<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralBonusTransaction extends Model
{
    protected $fillable = [
        'user_benefit_id',
        'type',
        'original_amount',
        'percentage_used',
        'amount',
        'related_transaction_id',
        'description'
    ];

    protected $casts = [
        'original_amount' => 'decimal:8',
        'percentage_used' => 'decimal:2',
        'amount' => 'decimal:8'
    ];

    /**
     * Get the user benefit this transaction belongs to
     */
    public function userBenefit(): BelongsTo
    {
        return $this->belongsTo(ReferralUserBenefit::class, 'user_benefit_id');
    }

    /**
     * Get the user through the user benefit relationship
     */
    public function user()
    {
        return $this->userBenefit->user ?? null;
    }

    /**
     * Record a transfer bonus transaction
     */
    public static function recordTransferBonus($userBenefitId, $amount, $bonusPercentage, $bonusAmount, $trx = null)
    {
        return self::create([
            'user_benefit_id' => $userBenefitId,
            'type' => 'transfer_bonus',
            'original_amount' => $amount,
            'percentage_used' => $bonusPercentage,
            'amount' => $bonusAmount,
            'related_transaction_id' => $trx,
            'description' => "Transfer bonus ({$bonusPercentage}%) on amount $" . number_format($amount, 2)
        ]);
    }

    /**
     * Record a receive bonus transaction
     */
    public static function recordReceiveBonus($userBenefitId, $amount, $bonusPercentage, $bonusAmount, $trx = null)
    {
        return self::create([
            'user_benefit_id' => $userBenefitId,
            'type' => 'receive_bonus',
            'original_amount' => $amount,
            'percentage_used' => $bonusPercentage,
            'amount' => $bonusAmount,
            'related_transaction_id' => $trx,
            'description' => "Receive bonus ({$bonusPercentage}%) on amount $" . number_format($amount, 2)
        ]);
    }

    /**
     * Record a withdraw charge reduction transaction
     */
    public static function recordWithdrawReduction($userBenefitId, $originalCharge, $reductionPercentage, $reductionAmount, $trx = null)
    {
        return self::create([
            'user_benefit_id' => $userBenefitId,
            'type' => 'withdraw_reduction',
            'original_amount' => $originalCharge,
            'percentage_used' => $reductionPercentage,
            'amount' => $reductionAmount,
            'related_transaction_id' => $trx,
            'description' => "Withdraw charge reduction ({$reductionPercentage}%) on charge $" . number_format($originalCharge, 2)
        ]);
    }

    /**
     * Get user's bonus history
     */
    public static function getUserBonusHistory($userId, $type = null)
    {
        $query = self::whereHas('userBenefit', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })->orderBy('created_at', 'desc');
        
        if ($type) {
            $query->where('type', $type);
        }
        
        return $query->get();
    }

    /**
     * Get total bonuses for user
     */
    public static function getTotalBonuses($userId, $type = null)
    {
        $query = self::whereHas('userBenefit', function($q) use ($userId) {
            $q->where('user_id', $userId);
        });
        
        if ($type) {
            $query->where('type', $type);
        }
        
        return $query->sum('amount');
    }
}
