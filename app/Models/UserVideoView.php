<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserVideoView extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'video_link_id',
        'earning_amount',
        'view_duration',
        'ip_address',
        'user_agent',
        'is_completed'
    ];

    protected $casts = [
        'earning_amount' => 'decimal:2',
        'view_duration' => 'integer',
        'is_completed' => 'boolean',
    ];

    /**
     * Get the user that viewed the video
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the video that was viewed
     */
    public function videoLink()
    {
        return $this->belongsTo(VideoLink::class);
    }

    /**
     * Scope for completed views
     */
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    /**
     * Scope for today's views
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope for views by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
