<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyVideoAssignment extends Model 
{
    protected $fillable = [
        'user_id',
        'video_link_id',
        'assignment_date',
        'is_watched',
        'watched_at',
        'earning_amount',
        'video_ids',
        'watched_video_ids',
        'total_videos',
        'watched_count'
    ];

    protected $casts = [
        'assignment_date' => 'date',
        'is_watched' => 'boolean',
        'watched_at' => 'datetime',
        'earning_amount' => 'decimal:6',
        'video_ids' => 'array',
        'watched_video_ids' => 'array',
        'total_videos' => 'integer',
        'watched_count' => 'integer'
    ];

    /**
     * Get the user that owns the assignment
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the video link for this assignment
     */
    public function videoLink(): BelongsTo
    {
        return $this->belongsTo(VideoLink::class);
    }

    /**
     * Check if assignment is for today
     */
    public function isToday(): bool
    {
        return $this->assignment_date->isToday();
    }

    /**
     * Mark assignment as watched
     */
    public function markAsWatched(float $earningAmount): bool
    {
        return $this->update([
            'is_watched' => true,
            'watched_at' => now(),
            'earning_amount' => $earningAmount
        ]);
    }

    /**
     * Get the referral commissions generated from this assignment
     */
    public function referralCommissions(): HasMany
    {
        return $this->hasMany(ReferralCommission::class);
    }

    /**
     * Mark assignment as watched and distribute referral commissions
     */
    public function markAsWatchedWithCommissions(float $earningAmount): bool
    {
        // Mark as watched first
        $success = $this->markAsWatched($earningAmount);
        
        if ($success) {
            // Distribute referral commissions
            $distributionService = new \App\Services\ReferralDistributionService();
            $result = $distributionService->distributeCommissions($this);
            
            if ($result['success']) {
                Log::info('Referral commissions distributed for assignment', [
                    'assignment_id' => $this->id,
                    'total_distributed' => $result['total_distributed'],
                    'levels_processed' => $result['levels_processed']
                ]);
            }
        }
        
        return $success;
    }

    /**
     * Get commission distribution summary for this assignment
     */
    public function getCommissionSummary(): array
    {
        $distributionService = new \App\Services\ReferralDistributionService();
        return $distributionService->getAssignmentCommissions($this);
    }

    /**
     * Scope for today's assignments
     */
    public function scopeForDate($query, $date = null)
    {
        $date = $date ?? today();
        return $query->where('assignment_date', $date);
    }

    /**
     * Scope for user's assignments
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for unwatched assignments
     */
    public function scopeUnwatched($query)
    {
        return $query->where('is_watched', false);
    }

    /**
     * Scope for watched assignments
     */
    public function scopeWatched($query)
    {
        return $query->where('is_watched', true);
    }
}
