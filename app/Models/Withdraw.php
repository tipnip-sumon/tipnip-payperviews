<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdraw extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'method_id',
        'amount',
        'charge',
        'rate',
        'final_amount',
        'after_charge',
        'withdraw_information',
        'trx',
        'status',
        'admin_feedback',
        'processing_date',
        'approved_date',
        'rejected_date'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'charge' => 'decimal:2',
        'rate' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'after_charge' => 'decimal:2',
        'withdraw_information' => 'array',
        'processing_date' => 'datetime',
        'approved_date' => 'datetime',
        'rejected_date' => 'datetime'
    ];

    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_REJECTED = 2;
    const STATUS_PROCESSING = 3;

    /**
     * Get the user that owns the withdrawal
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the withdrawal method
     */
    public function method()
    {
        return $this->belongsTo(WithdrawMethod::class);
    }

    /**
     * Scope for pending withdrawals
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for approved withdrawals
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope for rejected withdrawals
     */
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Scope for processing withdrawals
     */
    public function scopeProcessing($query)
    {
        return $query->where('status', self::STATUS_PROCESSING);
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute()
    {
        switch ($this->status) {
            case self::STATUS_PENDING:
                return 'Pending';
            case self::STATUS_APPROVED:
                return 'Approved';
            case self::STATUS_REJECTED:
                return 'Rejected';
            case self::STATUS_PROCESSING:
                return 'Processing';
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
            case self::STATUS_PENDING:
                return 'warning';
            case self::STATUS_APPROVED:
                return 'success';
            case self::STATUS_REJECTED:
                return 'danger';
            case self::STATUS_PROCESSING:
                return 'info';
            default:
                return 'secondary';
        }
    }
}
