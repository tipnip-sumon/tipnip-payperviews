<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoView extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'video_link_id',
        'earned_amount',
        'viewed_at',
        'ip_address',
        'user_agent',
        'device_info',
        // New optimized fields
        'view_date',
        'view_type',
        'video_data',
        'total_earned',
        'total_videos'
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
        'view_date' => 'date',
        'earned_amount' => 'decimal:8',
        'total_earned' => 'decimal:8',
        'video_data' => 'array'
    ];

    /**
     * Scope for daily summary records
     */
    public function scopeDailySummary($query)
    {
        return $query->where('view_type', 'daily_summary');
    }

    /**
     * Scope for specific user and date
     */
    public function scopeForUserAndDate($query, $userId, $date)
    {
        return $query->where('user_id', $userId)
                    ->where('view_date', $date);
    }

    /**
     * Get videos watched from JSON data
     */
    public function getVideosWatchedAttribute()
    {
        if ($this->view_type === 'daily_summary' && $this->video_data) {
            return array_values($this->video_data);
        }
        
        return [];
    }

    /**
     * Check if specific video was watched
     */
    public function hasWatchedVideo($videoId)
    {
        if ($this->view_type === 'daily_summary' && $this->video_data) {
            return isset($this->video_data[(string) $videoId]);
        }
        
        return false;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function videoLink()
    {
        return $this->belongsTo(VideoLink::class);
    }
}
