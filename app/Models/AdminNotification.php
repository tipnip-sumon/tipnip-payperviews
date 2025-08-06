<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class AdminNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'title',
        'message',
        'type',
        'priority',
        'icon',
        'read',
        'read_at',
        'action_url',
        'action_text',
        'expires_at',
        'metadata',
    ];

    protected $casts = [
        'read' => 'boolean',
        'read_at' => 'datetime',
        'expires_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the admin that owns the notification
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Get the user (for backward compatibility)
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
     * Scope for notifications that haven't expired
     */
    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Scope for expired notifications
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('expires_at')
                    ->where('expires_at', '<=', now());
    }

    /**
     * Scope for specific priority
     */
    public function scopePriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope for specific type
     */
    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get time ago attribute
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get formatted time attribute
     */
    public function getFormattedTimeAttribute()
    {
        return $this->created_at->format('M d, Y \a\t h:i A');
    }

    /**
     * Check if notification is urgent
     */
    public function getIsUrgentAttribute()
    {
        return $this->priority === 'urgent';
    }

    /**
     * Check if notification has expired
     */
    public function getIsExpiredAttribute()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Get CSS class for notification type
     */
    public function getTypeClassAttribute()
    {
        $classes = [
            'info' => 'text-info',
            'success' => 'text-success',
            'warning' => 'text-warning',
            'danger' => 'text-danger',
            'primary' => 'text-primary',
        ];

        return $classes[$this->type] ?? 'text-info';
    }

    /**
     * Get background class for notification priority
     */
    public function getPriorityClassAttribute()
    {
        $classes = [
            'low' => 'bg-light',
            'normal' => '',
            'high' => 'bg-warning-subtle',
            'urgent' => 'bg-danger-subtle',
        ];

        return $classes[$this->priority] ?? '';
    }

    /**
     * Mark as read
     */
    public function markAsRead()
    {
        $this->update([
            'read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Mark as unread
     */
    public function markAsUnread()
    {
        $this->update([
            'read' => false,
            'read_at' => null,
        ]);
    }

    /**
     * Get notification summary for dashboard
     */
    public static function getSummary($adminId = null)
    {
        $query = static::query();
        
        if ($adminId) {
            $query->where(function ($q) use ($adminId) {
                $q->where('admin_id', $adminId)
                  ->orWhereNull('admin_id'); // Include global notifications
            });
        }

        return [
            'total' => $query->count(),
            'unread' => $query->unread()->count(),
            'urgent' => $query->where('priority', 'urgent')->unread()->count(),
            'today' => $query->whereDate('created_at', today())->count(),
            'this_week' => $query->whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
        ];
    }

    /**
     * Create notification for new user registration
     */
    public static function newUserRegistered($user)
    {
        return static::create([
            'title' => 'New User Registered',
            'message' => "New user {$user->username} ({$user->email}) has registered.",
            'type' => 'success',
            'priority' => 'normal',
            'icon' => 'fas fa-user-plus',
            'action_url' => '#',
            'action_text' => 'View User',
            'metadata' => [
                'user_id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
            ],
        ]);
    }

    /**
     * Create notification for new investment
     */
    public static function newInvestment($investment)
    {
        return static::create([
            'title' => 'New Investment',
            'message' => "User {$investment->user->username} invested {$investment->amount}.",
            'type' => 'info',
            'priority' => 'normal',
            'icon' => 'fas fa-chart-line',
            'action_url' => '#',
            'action_text' => 'View Investment',
            'metadata' => [
                'investment_id' => $investment->id,
                'user_id' => $investment->user_id,
                'amount' => $investment->amount,
            ],
        ]);
    }

    /**
     * Create notification for withdrawal request
     */
    public static function withdrawalRequest($withdrawal)
    {
        return static::create([
            'title' => 'Withdrawal Request',
            'message' => "User {$withdrawal->user->username} requested withdrawal of {$withdrawal->amount}.",
            'type' => 'warning',
            'priority' => 'high',
            'icon' => 'fas fa-money-bill-wave',
            'action_url' => '#',
            'action_text' => 'Review Request',
            'metadata' => [
                'withdrawal_id' => $withdrawal->id,
                'user_id' => $withdrawal->user_id,
                'amount' => $withdrawal->amount,
            ],
        ]);
    }
}
