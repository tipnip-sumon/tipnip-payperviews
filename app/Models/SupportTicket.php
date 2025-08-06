<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'ticket',
        'subject',
        'status',
        'priority',
        'last_reply',
        'admin_id'
    ];

    protected $casts = [
        'last_reply' => 'datetime'
    ];

    const STATUS_OPEN = 0;
    const STATUS_ANSWERED = 1;
    const STATUS_REPLIED = 2;
    const STATUS_CLOSED = 3;

    const PRIORITY_LOW = 1;
    const PRIORITY_MEDIUM = 2;
    const PRIORITY_HIGH = 3;

    /**
     * Get the user that owns the support ticket
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin assigned to the ticket
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Get all messages for this ticket
     */
    public function messages()
    {
        return $this->hasMany(SupportMessage::class, 'supportticket_id');
    }

    /**
     * Scope for open tickets
     */
    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    /**
     * Scope for answered tickets
     */
    public function scopeAnswered($query)
    {
        return $query->where('status', self::STATUS_ANSWERED);
    }

    /**
     * Scope for replied tickets
     */
    public function scopeReplied($query)
    {
        return $query->where('status', self::STATUS_REPLIED);
    }

    /**
     * Scope for closed tickets
     */
    public function scopeClosed($query)
    {
        return $query->where('status', self::STATUS_CLOSED);
    }

    /**
     * Scope for high priority tickets
     */
    public function scopeHighPriority($query)
    {
        return $query->where('priority', self::PRIORITY_HIGH);
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute()
    {
        switch ($this->status) {
            case self::STATUS_OPEN:
                return 'Open';
            case self::STATUS_ANSWERED:
                return 'Answered';
            case self::STATUS_REPLIED:
                return 'Customer Reply';
            case self::STATUS_CLOSED:
                return 'Closed';
            default:
                return 'Unknown';
        }
    }

    /**
     * Get priority text
     */
    public function getPriorityTextAttribute()
    {
        switch ($this->priority) {
            case self::PRIORITY_LOW:
                return 'Low';
            case self::PRIORITY_MEDIUM:
                return 'Medium';
            case self::PRIORITY_HIGH:
                return 'High';
            default:
                return 'Unknown';
        }
    }

    /**
     * Get status color
     */
    public function getStatusColorAttribute()
    {
        switch ($this->status) {
            case self::STATUS_OPEN:
                return 'primary';
            case self::STATUS_ANSWERED:
                return 'success';
            case self::STATUS_REPLIED:
                return 'warning';
            case self::STATUS_CLOSED:
                return 'secondary';
            default:
                return 'dark';
        }
    }

    /**
     * Get priority color
     */
    public function getPriorityColorAttribute()
    {
        switch ($this->priority) {
            case self::PRIORITY_LOW:
                return 'success';
            case self::PRIORITY_MEDIUM:
                return 'warning';
            case self::PRIORITY_HIGH:
                return 'danger';
            default:
                return 'secondary';
        }
    }
}
