<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralCommission extends Model
{
    protected $fillable = [
        'earner_user_id',
        'referrer_user_id',
        'daily_video_assignment_id',
        'level',
        'original_earning',
        'commission_percentage',
        'commission_amount',
        'commission_type',
        'earning_type',
        'earning_date',
        'distributed_at'
    ];

    protected $casts = [
        'original_earning' => 'decimal:6',
        'commission_percentage' => 'decimal:2',
        'commission_amount' => 'decimal:6',
        'earning_date' => 'date',
        'distributed_at' => 'datetime'
    ];

    /**
     * Get the user who earned the original amount
     */
    public function earner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'earner_user_id');
    }

    /**
     * Get the user who receives the commission
     */
    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_user_id');
    }

    /**
     * Get the daily video assignment that generated this commission
     */
    public function dailyVideoAssignment(): BelongsTo
    {
        return $this->belongsTo(DailyVideoAssignment::class);
    }

    /**
     * Scope for a specific referral level
     */
    public function scopeForLevel($query, int $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Scope for a specific commission type
     */
    public function scopeForType($query, string $type)
    {
        return $query->where('commission_type', $type);
    }

    /**
     * Scope for commissions earned by a user (as referrer)
     */
    public function scopeForReferrer($query, int $userId)
    {
        return $query->where('referrer_user_id', $userId);
    }

    /**
     * Scope for commissions generated from a user's earnings
     */
    public function scopeFromEarner($query, int $userId)
    {
        return $query->where('earner_user_id', $userId);
    }
}
