<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithdrawMethod extends Model
{
    protected $fillable = [
        'name',
        'method_key',
        'status',
        'min_amount',
        'max_amount',
        'daily_limit',
        'charge_type',
        'charge',
        'description',
        'icon',
        'processing_time',
        'currency',
        'instructions',
        'sort_order',
        'user_data'
    ];

    protected $casts = [
        'user_data' => 'object',
        'status' => 'boolean',
        'min_amount' => 'decimal:8',
        'max_amount' => 'decimal:8',
        'daily_limit' => 'decimal:8',
        'charge' => 'decimal:8',
        'sort_order' => 'integer'
    ];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    /**
     * Get active withdrawal methods
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Get withdrawal methods ordered by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get withdrawal method by key
     */
    public function scopeByKey($query, $key)
    {
        return $query->where('method_key', $key);
    }

    /**
     * Calculate charge for given amount
     */
    public function calculateCharge($amount)
    {
        if ($this->charge_type === 'percent') {
            return ($amount * $this->charge) / 100;
        }
        return $this->charge;
    }

    /**
     * Get final amount after charge
     */
    public function getFinalAmount($amount)
    {
        $charge = $this->calculateCharge($amount);
        return $amount - $charge;
    }

    /**
     * Check if amount is within limits
     */
    public function isAmountValid($amount)
    {
        return $amount >= $this->min_amount && $amount <= $this->max_amount;
    }

    /**
     * Get formatted charge display
     */
    public function getChargeDisplayAttribute()
    {
        if ($this->charge_type === 'percent') {
            return $this->charge . '%';
        }
        return '$' . number_format($this->charge, 2);
    }

    /**
     * Get icon HTML
     */
    public function getIconHtmlAttribute()
    {
        if ($this->icon) {
            return '<i class="' . $this->icon . '"></i>';
        }
        return '<i class="fe fe-credit-card"></i>';
    }
}
