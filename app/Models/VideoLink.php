<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class VideoLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'video_url',
        'duration',
        'ads_type',
        'category',
        'country',
        'source_platform',
        'views_count',
        'clicks_count',
        'cost_per_click',
        'status'
    ];

    protected $casts = [
        'cost_per_click' => 'decimal:4',
        'duration' => 'integer',
        'views_count' => 'integer',
        'clicks_count' => 'integer'
    ];

    public function views()
    {
        return $this->hasMany(VideoView::class);
    }

    public function userViews()
    {
        return $this->hasMany(VideoView::class)->where('user_id', Auth::id());
    }

    // Extract YouTube video ID from URL
    public function getYoutubeIdAttribute()
    {
        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $this->video_url, $matches);
        return $matches[1] ?? null;
    }

    // Get embed URL
    public function getEmbedUrlAttribute()
    {
        if ($this->youtube_id) {
            return "https://www.youtube.com/embed/{$this->youtube_id}";
        }
        return $this->video_url;
    }

    // Get thumbnail URL
    public function getThumbnailAttribute()
    {
        if ($this->thumbnail_url) {
            return $this->thumbnail_url;
        }
        
        if ($this->youtube_id) {
            return "https://img.youtube.com/vi/{$this->youtube_id}/maxresdefault.jpg";
        }
        
        return asset('assets/images/default-video-thumbnail.jpg');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getIsActiveAttribute()
    {
        return $this->status === 'active';
    }
}
