<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    protected $casts = [
        'detail' => 'object'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function gateway()
    {
        return $this->belongsTo(Gateway::class, 'method_code', 'code');
    }

    // scope
    public function scopeGatewayCurrency()
    {
        return GatewayCurrency::where('method_code', $this->method_code)->where('currency', $this->method_currency)->first();
    }

    public function scopeBaseCurrency()
    {
        return @$this->gateway->crypto == 1 ? 'USD' : $this->method_currency;
    }

    public function scopePending()
    {
        return $this->where('status', 2);
        // return $this->where('method_code','>=',1000)->where('status', 2);
    }

    public function scopeRejected()
    {
        return $this->where('status', 3);
    }

    public function scopeApproved()
    {
        return $this->where('status', 1);
    }

    public function scopeSuccessful()
    {
        return $this->where('status', 1);
    }

    public function scopeInitiated()
    {
        return $this->where('status', 0);
    }
}
