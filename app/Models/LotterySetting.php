<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

class LotterySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_price',
        'draw_day',
        'draw_time',
        'is_active',
        'prize_structure',
        'max_tickets_per_user',
        'min_tickets_for_draw',
        'admin_commission_percentage',
        'auto_draw',
        'auto_prize_distribution',
        'ticket_expiry_hours',
        'auto_claim_days',
        'auto_refund_cancelled',
        'prize_claim_deadline',
        'allow_multiple_winners_per_place',
        'prize_distribution_type',
        'manual_winner_selection',
        'show_virtual_tickets',
        'virtual_ticket_multiplier',
        'virtual_ticket_base',
        'virtual_user_id', // Dynamic virtual user ID for virtual tickets
        // Auto-generation fields
        'auto_generate_draws',
        'auto_generation_frequency',
        'auto_generation_schedule',
        'enable_virtual_tickets',
        'min_virtual_tickets',
        'max_virtual_tickets',
        'virtual_ticket_percentage',
        'enable_manual_winner_selection',
        'default_winner_pool',
        'auto_execute_draws',
        'auto_execute_delay_minutes',
        'next_auto_draw',
    ];

    protected $casts = [
        'ticket_price' => 'decimal:2',
        'draw_time' => 'datetime',
        'is_active' => 'boolean',
        'prize_structure' => 'array',
        'auto_draw' => 'boolean',
        'auto_prize_distribution' => 'boolean',
        'auto_refund_cancelled' => 'boolean',
        'allow_multiple_winners_per_place' => 'boolean',
        'manual_winner_selection' => 'boolean',
        'show_virtual_tickets' => 'boolean',
        'admin_commission_percentage' => 'decimal:2',
        // Auto-generation casts
        'auto_generate_draws' => 'boolean',
        'auto_generation_schedule' => 'array',
        'enable_virtual_tickets' => 'boolean',
        'virtual_ticket_percentage' => 'decimal:2',
        'enable_manual_winner_selection' => 'boolean',
        'default_winner_pool' => 'array',
        'auto_execute_draws' => 'boolean',
        'next_auto_draw' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Clear cache when settings are updated
        static::saved(function () {
            Cache::forget('lottery_settings');
        });

        static::deleted(function () {
            Cache::forget('lottery_settings');
        });
    }

    /**
     * Get lottery settings
     */
    public static function getDefaultSettings()
    {
        return Cache::remember('lottery_settings', 3600, function () {
            $settings = self::first();
            
            if (!$settings) {
                // Ensure we have a virtual user before creating settings
                $virtualUser = self::ensureVirtualUser();
                
                $settings = self::create([
                    'ticket_price' => 2.00,
                    'draw_day' => 0, // Sunday
                    'draw_time' => '20:00:00',
                    'is_active' => true,
                    'prize_structure' => [
                        1 => ['name' => 'First Prize', 'percentage' => 50],
                        2 => ['name' => 'Second Prize', 'percentage' => 30],
                        3 => ['name' => 'Third Prize', 'percentage' => 20],
                    ],
                    'max_tickets_per_user' => 100,
                    'min_tickets_for_draw' => 10,
                    'admin_commission_percentage' => 10.00,
                    'auto_draw' => true,
                    'auto_prize_distribution' => true,
                    'ticket_expiry_hours' => 168, // 1 week
                    'virtual_user_id' => $virtualUser->id,
                ]);
            }
            
            return $settings;
        });
    }

    /**
     * Ensure virtual user exists, create if not
     */
    private static function ensureVirtualUser()
    {
        // Try to find existing virtual user
        $virtualUser = \App\Models\User::where('username', 'lottery_virtual_user')
                                      ->orWhere('email', 'virtual@lottery.system')
                                      ->first();
        
        if (!$virtualUser) {
            // Create virtual user
            $virtualUser = \App\Models\User::create([
                'firstname' => 'Lottery',
                'lastname' => 'Virtual System',
                'username' => 'lottery_virtual_user',
                'email' => 'virtual@lottery.system',
                'email_verified_at' => now(),
                'password' => bcrypt('random_secure_password_' . time()),
                'country_code' => 'XX',
                'phone' => '+0000000000',
                'balance' => 0,
                'status' => 0, // Inactive
                'verified' => 0, // Not verified
                'kyc_verified' => 0,
            ]);
        }
        
        return $virtualUser;
    }

    /**
     * Update lottery settings
     */
    public static function updateSettings($data)
    {
        $settings = self::first();
        
        if ($settings) {
            $settings->update($data);
        } else {
            $settings = self::create($data);
        }

        Cache::forget('lottery_settings');
        return $settings;
    }

    /**
     * Get current lottery settings
     */
    public static function getSettings()
    {
        return Cache::remember('lottery_settings', 3600, function () {
            $settings = self::first();
            
            if (!$settings) {
                $settings = self::getDefaultSettings();
            }
            
            return $settings;
        });
    }

    /**
     * Get draw day name
     */
    public function getDrawDayName()
    {
        $days = [
            0 => 'Sunday',
            1 => 'Monday', 
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday'
        ];

        return $days[$this->draw_day] ?? 'Unknown';
    }

    /**
     * Check if lottery is active
     */
    public function isActive()
    {
        return $this->is_active;
    }

    /**
     * Get formatted ticket price
     */
    public function getFormattedTicketPrice()
    {
        $currency = GeneralSetting::getCurrencySymbol();
        return $currency . number_format($this->ticket_price, 2);
    }

    /**
     * Get total prize percentage (should be 100%)
     */
    public function getTotalPrizePercentage()
    {
        $total = 0;
        if (is_array($this->prize_structure)) {
            foreach ($this->prize_structure as $prize) {
                $total += $prize['percentage'] ?? 0;
            }
        }
        return $total;
    }

    /**
     * Validate prize structure
     */
    public function validatePrizeStructure()
    {
        $total = $this->getTotalPrizePercentage();
        
        if ($total !== 100) {
            throw new \Exception("Prize structure percentages must total 100%. Current total: {$total}%");
        }

        return true;
    }

    /**
     * Get next draw date and time
     */
    public function getNextDrawDateTime()
    {
        $now = now();
        $drawDay = $this->draw_day;
        $drawTime = $this->draw_time;
        
        // Find next occurrence of the draw day
        $nextDraw = $now->copy();
        
        // If today is the draw day but time hasn't passed
        if ($now->dayOfWeek === $drawDay && $now->format('H:i:s') < $drawTime->format('H:i:s')) {
            $nextDraw = $now->copy()->setTimeFromTimeString($drawTime->format('H:i:s'));
        } else {
            // Find next occurrence of draw day
            $daysUntilDraw = ($drawDay - $now->dayOfWeek + 7) % 7;
            if ($daysUntilDraw === 0) {
                $daysUntilDraw = 7; // Next week
            }
            
            $nextDraw = $now->copy()
                          ->addDays($daysUntilDraw)
                          ->setTimeFromTimeString($drawTime->format('H:i:s'));
        }
        
        return $nextDraw;
    }

    /**
     * Get time until next draw
     */
    public function getTimeUntilNextDraw()
    {
        $nextDraw = $this->getNextDrawDateTime();
        return now()->diffForHumans($nextDraw, true);
    }

    /**
     * Check if it's time for draw
     */
    public function isDrawTime()
    {
        $now = now();
        $nextDraw = $this->getNextDrawDateTime();
        
        // Allow 5 minute window for draw
        return $now->gte($nextDraw) && $now->lte($nextDraw->copy()->addMinutes(5));
    }

    /**
     * Get virtual/display ticket count for a draw
     */
    public function getDisplayTicketCount($realTicketCount)
    {
        if (!$this->show_virtual_tickets) {
            return $realTicketCount;
        }

        // Calculate virtual tickets: (real_tickets * multiplier) + base
        $virtualCount = ($realTicketCount * $this->virtual_ticket_multiplier / 100) + $this->virtual_ticket_base;
        
        return max($realTicketCount, $virtualCount); // Never show less than real tickets
    }

    /**
     * Get prize structure with support for fixed amounts
     */
    public function getPrizeStructureForDisplay($totalPrizePool = null)
    {
        $structure = $this->prize_structure ?? [];
        
        if ($this->prize_distribution_type === 'fixed_amount') {
            return $structure; // Return as-is for fixed amounts
        }
        
        // For percentage-based, calculate amounts if prize pool provided
        if ($totalPrizePool) {
            foreach ($structure as $position => &$prize) {
                $prize['calculated_amount'] = ($totalPrizePool * $prize['percentage']) / 100;
            }
        }
        
        return $structure;
    }

    /**
     * Validate prize structure based on distribution type
     */
    public function validatePrizeStructureEnhanced($totalPrizePool = null)
    {
        if ($this->prize_distribution_type === 'percentage') {
            return $this->validatePrizeStructure(); // Use existing method
        }
        
        // For fixed amounts, validate that total doesn't exceed prize pool
        if ($totalPrizePool && $this->prize_distribution_type === 'fixed_amount') {
            $totalFixed = 0;
            foreach ($this->prize_structure as $prize) {
                $totalFixed += $prize['amount'] ?? 0;
            }
            
            if ($totalFixed > $totalPrizePool) {
                throw new \Exception("Total fixed prize amounts ($totalFixed) exceed available prize pool ($totalPrizePool)");
            }
        }
        
        return true;
    }

    /**
     * Get total prize allocation amount
     */
    public function getTotalPrizeAllocation()
    {
        if (!$this->prize_structure || !is_array($this->prize_structure)) {
            return 0;
        }
        
        if ($this->prize_distribution_type === 'fixed_amount') {
            $total = 0;
            foreach ($this->prize_structure as $prize) {
                $total += isset($prize['amount']) ? (float)$prize['amount'] : 0;
            }
            return $total;
        }
        
        // For percentage-based, we can't calculate without knowing the pool
        // Return 0 or throw exception
        return 0;
    }

    /**
     * Check if manual winner selection is enabled
     */
    public function allowsManualWinnerSelection()
    {
        return $this->manual_winner_selection;
    }

    /**
     * Check if multiple winners per place are allowed
     */
    public function allowsMultipleWinnersPerPlace()
    {
        return $this->allow_multiple_winners_per_place;
    }
}
