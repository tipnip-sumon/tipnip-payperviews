<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommissionLevelSetting extends Model
{
    use HasFactory;

    protected $fillable = [ 
        'level',
        'percentage',
        'is_active',
        'description'
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
        'is_active' => 'boolean',
        'level' => 'integer'
    ];

    /**
     * Get active commission levels in order
     */
    public static function getActiveLevels(): array
    {
        return self::where('is_active', true)
            ->orderBy('level')
            ->pluck('percentage', 'level')
            ->toArray();
    }

    /**
     * Get default commission levels (fallback)
     */
    public static function getDefaultLevels(): array
    {
        return [
            1 => 13.0,   // Level 1: 13%
            2 => 6.0,   // Level 2: 6%
            3 => 3.0,   // Level 3: 3%
            4 => 1.5,   // Level 4: 1.5%
            5 => 0.75,   // Level 5: 0.75%
        ];
    }

    /**
     * Initialize default commission levels
     */
    public static function initializeDefaults(): void
    {
        $defaults = self::getDefaultLevels();
        
        foreach ($defaults as $level => $percentage) {
            self::updateOrCreate(
                ['level' => $level],
                [
                    'percentage' => $percentage,
                    'is_active' => true,
                    'description' => "Level {$level} referral commission"
                ]
            );
        }
    }

    /**
     * Validate that total percentage doesn't exceed the cap
     */
    public static function validateTotalPercentage(float $maxTotal = 100.0): bool
    {
        $total = self::where('is_active', true)->sum('percentage');
        return $total <= $maxTotal;
    }

    /**
     * Get total active commission percentage
     */
    public static function getTotalActivePercentage(): float
    {
        return self::where('is_active', true)->sum('percentage');
    }
    /**
     * Create a new commission level with validation
     */
    public static function createCommissionLevel(array $data): self
    {
        // Validate total percentage does not exceed 100%
        $currentTotal = self::getTotalActivePercentage();
        $newTotal = $currentTotal + ($data['is_active'] ? $data['percentage'] : 0);

        if ($newTotal > 100.0) {
            throw new \InvalidArgumentException("Total active commission percentage cannot exceed 100%. Current total: {$currentTotal}%");
        }

        return self::create($data);
    }
}