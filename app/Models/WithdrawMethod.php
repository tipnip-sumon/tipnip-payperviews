<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithdrawMethod extends Model
{
    protected $fillable = [
        'form_id',
        'name',
        'method_key',
        'min_limit',
        'max_limit',
        'fixed_charge',
        'rate',
        'percent_charge',
        'currency',
        'instructions',
        'sort_order',
        'description',
        'icon',
        'processing_time',
        'status',
        'min_amount',
        'max_amount',
        'daily_limit',
        'charge_type',
        'charge',
        'user_data'
    ];

    protected $casts = [
        'user_data' => 'object',
        'status' => 'boolean',
        'form_id' => 'integer',
        'min_limit' => 'decimal:8',
        'max_limit' => 'decimal:8',
        'fixed_charge' => 'decimal:8',
        'rate' => 'decimal:8',
        'percent_charge' => 'decimal:2',
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
        // Use modern fields if available, otherwise fall back to legacy
        $fixedCharge = $this->fixed_charge ?? 0;
        $percentCharge = $this->percent_charge ?? 0;
        
        // If modern fields are not set, use legacy fields
        if ($fixedCharge == 0 && $percentCharge == 0 && $this->charge > 0) {
            if ($this->charge_type === 'percent') {
                return ($amount * $this->charge) / 100;
            }
            return $this->charge;
        }
        
        // Calculate using modern fields (both fixed and percent can be applied)
        $totalCharge = $fixedCharge + (($amount * $percentCharge) / 100);
        return $totalCharge;
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
        // Use modern fields if available, otherwise fall back to legacy
        $minAmount = $this->min_amount ?? $this->min_limit ?? 0;
        $maxAmount = $this->max_amount ?? $this->max_limit ?? PHP_FLOAT_MAX;
        
        return $amount >= $minAmount && $amount <= $maxAmount;
    }

    /**
     * Get minimum amount (prefer modern field over legacy)
     */
    public function getMinimumAmountAttribute()
    {
        return $this->min_amount ?? $this->min_limit ?? 0;
    }

    /**
     * Get maximum amount (prefer modern field over legacy)
     */
    public function getMaximumAmountAttribute()
    {
        return $this->max_amount ?? $this->max_limit ?? PHP_FLOAT_MAX;
    }

    /**
     * Get formatted charge display
     */
    public function getChargeDisplayAttribute()
    {
        $fixedCharge = $this->fixed_charge ?? 0;
        $percentCharge = $this->percent_charge ?? 0;
        
        // If modern fields are not set, use legacy fields
        if ($fixedCharge == 0 && $percentCharge == 0 && $this->charge > 0) {
            if ($this->charge_type === 'percent') {
                return $this->charge . '%';
            }
            return '$' . number_format($this->charge, 2);
        }
        
        // Format modern fields
        $display = [];
        if ($fixedCharge > 0) {
            $display[] = '$' . number_format($fixedCharge, 2) . ' fixed';
        }
        if ($percentCharge > 0) {
            $display[] = $percentCharge . '% of amount';
        }
        
        return !empty($display) ? implode(' + ', $display) : 'Free';
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
