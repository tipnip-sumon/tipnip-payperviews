<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    protected $fillable = [
        'user_id',
        'method_id',
        'amount',
        'charge',
        'final_amount',
        'currency',
        'rate',
        'trx',
        'withdraw_information',
        'withdraw_type',
        'status'
    ];
    
    protected $casts = [
        'withdraw_information' => 'object'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function method()
    {
        return $this->belongsTo(WithdrawMethod::class, 'method_id');
    }
    
    public function withdrawMethod()
    {
        return $this->belongsTo(WithdrawMethod::class, 'method_id');
    }

    

    public function scopePending()
    {
        return $this->where('status', 2);
    }

    public function scopeApproved()
    {
        return $this->where('status', 1);
    }

    public function scopeRejected()
    {
        return $this->where('status', 3);
    }

    public function scopeInitiated()
    {
        return $this->where('status', 0);
    }
}
