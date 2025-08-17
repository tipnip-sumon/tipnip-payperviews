<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserSessionNotification extends Model
{
    use HasFactory;

    protected $table = 'user_session_notifications';

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'new_login_ip',
        'new_login_device',
        'new_login_location',
        'old_session_ip',
        'old_session_duration',
        'is_read',
        'action_taken',
        'session_id'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the notification
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for recent notifications
     */
    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }

    /**
     * Get notification icon based on type
     */
    public function getIconAttribute()
    {
        return match($this->type) {
            'new_login_detected' => 'fas fa-sign-in-alt text-warning',
            'session_terminated' => 'fas fa-sign-out-alt text-danger',
            'suspicious_activity' => 'fas fa-exclamation-triangle text-danger',
            'multiple_sessions' => 'fas fa-users text-info',
            default => 'fas fa-bell text-primary'
        };
    }

    /**
     * Get notification color class
     */
    public function getColorClassAttribute()
    {
        return match($this->type) {
            'new_login_detected' => 'border-warning',
            'session_terminated' => 'border-danger',
            'suspicious_activity' => 'border-danger',
            'multiple_sessions' => 'border-info',
            default => 'border-primary'
        };
    }

    /**
     * Get time ago text
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}
