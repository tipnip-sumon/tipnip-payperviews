<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_user_id',
        'to_user_id',
        'subject',
        'message',
        'priority',
        'category',
        'is_read',
        'is_starred',
        'read_at',
        'reply_to_id',
        'attachment_path',
        'attachments',
        'metadata',
        'message_type',
        'status'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_starred' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who sent the message
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    /**
     * Get the user who received the message
     */
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    /**
     * Get the original message if this is a reply
     */
    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'reply_to_id');
    }

    /**
     * Get all replies to this message
     */
    public function replies()
    {
        return $this->hasMany(Message::class, 'reply_to_id');
    }

    /**
     * Mark message as read
     */
    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        }
    }

    /**
     * Mark message as unread
     */
    public function markAsUnread(): void
    {
        $this->update([
            'is_read' => false,
            'read_at' => null
        ]);
    }

    /**
     * Toggle starred status
     */
    public function toggleStar(): void
    {
        $this->update(['is_starred' => !$this->is_starred]);
    }

    /**
     * Scope for unread messages
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for read messages
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope for starred messages
     */
    public function scopeStarred($query)
    {
        return $query->where('is_starred', true);
    }

    /**
     * Scope for messages sent by a specific user
     */
    public function scopeSentBy($query, $userId)
    {
        return $query->where('from_user_id', $userId);
    }

    /**
     * Scope for messages received by a specific user
     */
    public function scopeReceivedBy($query, $userId)
    {
        return $query->where('to_user_id', $userId);
    }

    /**
     * Scope for messages between two users
     */
    public function scopeBetweenUsers($query, $user1Id, $user2Id)
    {
        return $query->where(function ($q) use ($user1Id, $user2Id) {
            $q->where('from_user_id', $user1Id)->where('to_user_id', $user2Id);
        })->orWhere(function ($q) use ($user1Id, $user2Id) {
            $q->where('from_user_id', $user2Id)->where('to_user_id', $user1Id);
        });
    }

    /**
     * Scope for high priority messages
     */
    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['high', 'urgent']);
    }

    /**
     * Scope for messages by priority
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Get priority badge class for UI
     */
    public function getPriorityBadgeAttribute(): string
    {
        return match($this->priority) {
            'urgent' => 'bg-danger',
            'high' => 'bg-warning',
            'normal' => 'bg-info',
            'low' => 'bg-success',
            default => 'bg-secondary'
        };
    }

    /**
     * Get priority display text
     */
    public function getPriorityTextAttribute(): string
    {
        return match($this->priority) {
            'urgent' => 'Urgent',
            'high' => 'High',
            'normal' => 'Normal',
            'low' => 'Low',
            default => 'Normal'
        };
    }

    /**
     * Check if message is a reply
     */
    public function getIsReplyAttribute(): bool
    {
        return !is_null($this->reply_to_id);
    }

    /**
     * Get formatted created date
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->created_at->format('M d, Y h:i A');
    }

    /**
     * Get short message preview
     */
    public function getPreviewAttribute(): string
    {
        return Str::limit($this->message, 100);
    }
}
