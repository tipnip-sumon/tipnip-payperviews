<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class UserNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'priority',
        'title',
        'message',
        'icon',
        'data',
        'metadata',
        'read',
        'read_at',
        'action_url',
        'action_text',
        'expires_at'
    ];

    protected $casts = [
        'data' => 'array',
        'metadata' => 'array',
        'read' => 'boolean',
        'read_at' => 'datetime',
        'expires_at' => 'datetime',
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
        return $query->where('read', false);
    }

    /**
     * Scope for read notifications
     */
    public function scopeRead($query)
    {
        return $query->where('read', true);
    }

    /**
     * Scope for non-expired notifications
     */
    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Scope for priority notifications
     */
    public function scopePriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope for urgent notifications
     */
    public function scopeUrgent($query)
    {
        return $query->where('priority', 'urgent');
    }

    /**
     * Scope for notifications by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for recent notifications
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead()
    {
        $this->update([
            'read' => true,
            'read_at' => now()
        ]);
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread()
    {
        $this->update([
            'read' => false,
            'read_at' => null
        ]);
    }

    /**
     * Get time ago format
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Check if notification is expired
     */
    public function getIsExpiredAttribute()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if notification is urgent
     */
    public function getIsUrgentAttribute()
    {
        return $this->priority === 'urgent';
    }

    /**
     * Get formatted time
     */
    public function getFormattedTimeAttribute()
    {
        return $this->created_at->format('M j, Y g:i A');
    }

    /**
     * Get type CSS class
     */
    public function getTypeClassAttribute()
    {
        $classes = [
            'welcome' => 'text-info',
            'investment' => 'text-success',
            'withdrawal' => 'text-warning',
            'referral' => 'text-primary',
            'lottery' => 'text-purple',
            'support' => 'text-info',
            'security' => 'text-danger',
            'system' => 'text-secondary',
            'promotion' => 'text-warning',
            'success' => 'text-success',
            'warning' => 'text-warning',
            'danger' => 'text-danger',
            'info' => 'text-info',
        ];

        return $classes[$this->type] ?? 'text-secondary';
    }

    /**
     * Get priority CSS class
     */
    public function getPriorityClassAttribute()
    {
        $classes = [
            'low' => 'border-left-secondary',
            'normal' => 'border-left-primary',
            'high' => 'border-left-warning',
            'urgent' => 'border-left-danger',
        ];

        return $classes[$this->priority ?? 'normal'] ?? 'border-left-secondary';
    }

    /**
     * Create a notification for a user
     */
    public static function createForUser($userId, $data)
    {
        return static::create([
            'user_id' => $userId,
            'type' => $data['type'] ?? 'info',
            'priority' => $data['priority'] ?? 'normal',
            'title' => $data['title'],
            'message' => $data['message'],
            'icon' => $data['icon'] ?? null,
            'data' => $data['data'] ?? null,
            'metadata' => $data['metadata'] ?? null,
            'action_url' => $data['action_url'] ?? null,
            'action_text' => $data['action_text'] ?? null,
            'expires_at' => $data['expires_at'] ?? null,
        ]);
    }

    /**
     * Create bulk notifications for multiple users
     */
    public static function createForUsers($userIds, $data)
    {
        $notifications = [];
        $now = now();

        foreach ($userIds as $userId) {
            $notifications[] = [
                'user_id' => $userId,
                'type' => $data['type'] ?? 'info',
                'priority' => $data['priority'] ?? 'normal',
                'title' => $data['title'],
                'message' => $data['message'],
                'icon' => $data['icon'] ?? null,
                'data' => json_encode($data['data'] ?? null),
                'metadata' => json_encode($data['metadata'] ?? null),
                'action_url' => $data['action_url'] ?? null,
                'action_text' => $data['action_text'] ?? null,
                'expires_at' => $data['expires_at'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        return static::insert($notifications);
    }

    /**
     * Get notifications for a user
     */
    public static function getForUser($userId, $limit = 20)
    {
        return static::where('user_id', $userId)
            ->notExpired()
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get unread count for a user
     */
    public static function getUnreadCountForUser($userId)
    {
        return static::where('user_id', $userId)
            ->unread()
            ->notExpired()
            ->count();
    }

    /**
     * Mark all notifications as read for a user
     */
    public static function markAllAsReadForUser($userId)
    {
        return static::where('user_id', $userId)
            ->unread()
            ->update([
                'read' => true,
                'read_at' => now()
            ]);
    }

    /**
     * Clean up expired notifications
     */
    public static function cleanupExpired()
    {
        return static::where('expires_at', '<', now())->delete();
    }
}
