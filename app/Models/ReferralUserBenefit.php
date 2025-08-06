<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class ReferralUserBenefit extends Model
{
    protected $fillable = [
        'user_id',
        'qualified_referrals_count',
        'transfer_bonus_percentage',
        'balance_receive_bonus_percentage', 
        'withdraw_charge_reduction_percentage',
        'is_qualified',
        'qualified_at',
        'last_updated_at'
    ];

    protected $casts = [
        'qualified_referrals_count' => 'integer',
        'transfer_bonus_percentage' => 'decimal:2',
        'balance_receive_bonus_percentage' => 'decimal:2',
        'withdraw_charge_reduction_percentage' => 'decimal:2',
        'is_qualified' => 'boolean',
        'qualified_at' => 'datetime',
        'last_updated_at' => 'datetime'
    ];

    /**
     * Get the user that owns the benefits
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all bonus transactions for this user benefit
     */
    public function bonusTransactions()
    {
        return $this->hasMany(ReferralBonusTransaction::class, 'user_benefit_id');
    }

    /**
     * Check if user qualifies for referral benefits
     */
    public static function checkAndUpdateQualification($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return false;
        }

        $settings = GeneralSetting::getSetting('referral_benefits_settings', [
            'enabled' => true,
            'minimum_referrals' => 15,
            'minimum_investment_per_referral' => 50,
            'transfer_bonus_min' => 1,
            'transfer_bonus_max' => 5,
            'receive_bonus_min' => 1,
            'receive_bonus_max' => 5,
            'withdraw_reduction_min' => 1,
            'withdraw_reduction_max' => 5
        ]);

        if (!$settings['enabled']) {
            return false;
        }

        // Count qualified referrals
        $qualifiedReferrals = User::where('ref_by', $userId)
            ->whereHas('invests', function($query) use ($settings) {
                $query->where('status', 1)
                      ->where('amount', '>=', $settings['minimum_investment_per_referral']);
            })
            ->count();

        // Get or create benefits record
        $benefits = self::firstOrCreate(
            ['user_id' => $userId],
            [
                'qualified_referrals_count' => 0,
                'is_qualified' => false
            ]
        );

        $benefits->qualified_referrals_count = $qualifiedReferrals;
        $benefits->last_updated_at = now();

        // Check if qualifies for benefits
        if ($qualifiedReferrals >= $settings['minimum_referrals'] && !$benefits->is_qualified) {
            $benefits->is_qualified = true;
            $benefits->qualified_at = now();
            
            // Set dynamic bonus percentages (you can make these admin-configurable)
            $benefits->transfer_bonus_percentage = rand($settings['transfer_bonus_min'], $settings['transfer_bonus_max']);
            $benefits->balance_receive_bonus_percentage = rand($settings['receive_bonus_min'], $settings['receive_bonus_max']);
            $benefits->withdraw_charge_reduction_percentage = rand($settings['withdraw_reduction_min'], $settings['withdraw_reduction_max']);
            
            $benefits->save();

            // Send notification
            try {
                // notifyReferralBenefitsQualified($userId, $benefits);
            } catch (\Exception $e) {
                Log::error('Failed to send referral benefits notification: ' . $e->getMessage());
            }

            return true;
        } elseif ($qualifiedReferrals < $settings['minimum_referrals'] && $benefits->is_qualified) {
            // Lost qualification
            $benefits->is_qualified = false;
            $benefits->transfer_bonus_percentage = 0;
            $benefits->balance_receive_bonus_percentage = 0;
            $benefits->withdraw_charge_reduction_percentage = 0;
            $benefits->save();

            // Send notification about losing benefits
            try {
                // notifyReferralBenefitsLost($userId);
            } catch (\Exception $e) {
                Log::error('Failed to send referral benefits lost notification: ' . $e->getMessage());
            }
        } else {
            $benefits->save();
        }

        return $benefits->is_qualified;
    }

    /**
     * Get user's current benefits
     */
    public static function getUserBenefits($userId)
    {
        return self::where('user_id', $userId)->first();
    }

    /**
     * Check if user has specific benefit
     */
    public static function hasTransferBonus($userId)
    {
        $benefits = self::getUserBenefits($userId);
        return $benefits && $benefits->is_qualified && $benefits->transfer_bonus_percentage > 0;
    }

    public static function hasReceiveBonus($userId)
    {
        $benefits = self::getUserBenefits($userId);
        return $benefits && $benefits->is_qualified && $benefits->balance_receive_bonus_percentage > 0;
    }

    public static function hasWithdrawReduction($userId)
    {
        $benefits = self::getUserBenefits($userId);
        return $benefits && $benefits->is_qualified && $benefits->withdraw_charge_reduction_percentage > 0;
    }

    /**
     * Get transfer bonus amount
     */
    public static function calculateTransferBonus($userId, $amount)
    {
        $benefits = self::getUserBenefits($userId);
        if (!$benefits || !$benefits->is_qualified) {
            return 0;
        }

        return ($amount * $benefits->transfer_bonus_percentage) / 100;
    }

    /**
     * Get receive bonus amount
     */
    public static function calculateReceiveBonus($userId, $amount)
    {
        $benefits = self::getUserBenefits($userId);
        if (!$benefits || !$benefits->is_qualified) {
            return 0;
        }

        return ($amount * $benefits->balance_receive_bonus_percentage) / 100;
    }

    /**
     * Get withdraw charge reduction
     */
    public static function calculateWithdrawReduction($userId, $chargeAmount)
    {
        $benefits = self::getUserBenefits($userId);
        if (!$benefits || !$benefits->is_qualified) {
            return 0;
        }

        return ($chargeAmount * $benefits->withdraw_charge_reduction_percentage) / 100;
    }
}
