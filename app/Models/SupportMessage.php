<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'supportticket_id',
        'admin_id',
        'message',
        'attachments'
    ];

    protected $casts = [
        'attachments' => 'array'
    ];

    /**
     * Get the support ticket that owns the message
     */
    public function supportTicket()
    {
        return $this->belongsTo(SupportTicket::class, 'supportticket_id');
    }

    /**
     * Get the admin that sent the message
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Scope for admin messages
     */
    public function scopeFromAdmin($query)
    {
        return $query->whereNotNull('admin_id');
    }

    /**
     * Scope for user messages
     */
    public function scopeFromUser($query)
    {
        return $query->whereNull('admin_id');
    }
}
