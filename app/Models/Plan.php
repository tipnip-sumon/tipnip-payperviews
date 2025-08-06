<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Plan extends Model
{
    use HasFactory;
    // public $timestamps = false;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'plans';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'minimum',
        'maximum',
        'fixed_amount',
        'interest',
        'interest_type',
        'time',
        'time_name',
        'status',
        'featured',
        'capital_back',
        'lifetime',
        'repeat_time',
        'daily_video_limit',
        'description',
        'video_earning_rate',
        'video_access_enabled',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'minimum' => 'decimal:8',
        'maximum' => 'decimal:8',
        'fixed_amount' => 'integer',
        'interest' => 'float',
        'time' => 'integer',
        'featured' => 'boolean',
        'capital_back' => 'boolean',
        'lifetime' => 'boolean',
        'repeat_time' => 'integer',
        'daily_video_limit' => 'integer',
        'video_earning_rate' => 'float',
        'video_access_enabled' => 'boolean'
    ];

    public function invests()
    {
        return $this->hasMany(Invest::class);
    }

    /**
     * Get the interest type as a string
     */
    public function getInterestTypeTextAttribute()
    {
        $types = [
            0 => 'currency',
            1 => 'percentage'
        ];
        
        return $types[$this->interest_type] ?? 'currency';
    }

    /**
     * Set interest type from string
     */
    public function setInterestTypeAttribute($value)
    {
        if (is_string($value)) {
            $typeMap = [
                'currency' => 0,
                'percentage' => 1
            ];
            $this->attributes['interest_type'] = $typeMap[$value] ?? 0;
        } else {
            $this->attributes['interest_type'] = $value;
        }
    }
}
