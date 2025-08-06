<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LotteryTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        // Existing fields
        'ticket_number',
        'user_id',
        'lottery_draw_id',
        'ticket_price',
        'purchased_at',
        'status',
        'prize_amount',
        'claimed_at',
        'payment_method',
        'transaction_reference',
        'is_invalidated',
        'invalidated_at',
        'invalidation_reason',
        'is_virtual',
        'virtual_user_type',
        'virtual_metadata',
        // New unified fields
        'token_type',
        'sponsor_user_id',
        'referral_user_id',
        'current_owner_id',
        'original_owner_id',
        'is_valid_token',
        'is_transferable',
        'transfer_count',
        'last_transferred_at',
        'token_discount_amount',
        'used_for_plan_id',
        'early_usage_bonus',
        'token_expires_at',
        'refund_amount',
        'used_as_token_at',
    ];

    protected $casts = [
        'purchased_at' => 'datetime',
        'claimed_at' => 'datetime',
        'invalidated_at' => 'datetime',
        'last_transferred_at' => 'datetime',
        'token_expires_at' => 'datetime',
        'used_as_token_at' => 'datetime',
        'ticket_price' => 'decimal:2',
        'prize_amount' => 'decimal:2',
        'token_discount_amount' => 'decimal:2',
        'early_usage_bonus' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'is_invalidated' => 'boolean',
        'is_virtual' => 'boolean',
        'is_valid_token' => 'boolean',
        'is_transferable' => 'boolean',
        'virtual_metadata' => 'array',
    ];

    /**
     * Get the user who owns this ticket
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the sponsor user for special tokens
     */
    public function sponsor()
    {
        return $this->belongsTo(User::class, 'sponsor_user_id');
    }

    /**
     * Get the referral user for special tokens
     */
    public function referralUser()
    {
        return $this->belongsTo(User::class, 'referral_user_id');
    }

    /**
     * Get the current owner of the token
     */
    public function currentOwner()
    {
        return $this->belongsTo(User::class, 'current_owner_id');
    }

    /**
     * Get the original owner of the token
     */
    public function originalOwner()
    {
        return $this->belongsTo(User::class, 'original_owner_id');
    }

    /**
     * Get the plan this token was used for
     */
    public function usedForPlan()
    {
        return $this->belongsTo(Plan::class, 'used_for_plan_id');
    }

    // ========== SCOPES FOR DIFFERENT TOKEN TYPES ==========

    /**
     * Scope to get only lottery tickets
     */
    public function scopeLotteryTickets($query)
    {
        return $query->where('token_type', 'lottery');
    }

    /**
     * Scope to get only special tokens
     */
    public function scopeSpecialTokens($query)
    {
        return $query->where('token_type', 'special');
    }

    /**
     * Scope to get only sponsor tickets
     */
    public function scopeSponsorTickets($query)
    {
        return $query->where('token_type', 'sponsor');
    }

    /**
     * Scope to get valid tokens that can be used
     */
    public function scopeValidTokens($query)
    {
        return $query->where('is_valid_token', true)
                    ->where('status', 'active')
                    ->whereNull('used_as_token_at')
                    ->where(function($q) {
                        $q->whereNull('token_expires_at')
                          ->orWhere('token_expires_at', '>', now());
                    });
    }

    /**
     * Get the lottery draw this ticket belongs to
     */
    public function lotteryDraw()
    {
        return $this->belongsTo(LotteryDraw::class);
    }

    /**
     * Alias for lotteryDraw relationship (for backward compatibility)
     */
    public function draw()
    {
        return $this->lotteryDraw();
    }

    /**
     * Get winner record if this ticket won
     */
    public function winner()
    {
        return $this->hasOne(LotteryWinner::class);
    }

    /**
     * Get the special lottery ticket derived from this lottery ticket
     */
    public function specialLotteryTicket()
    {
        return $this->hasOne(SpecialLotteryTicket::class, 'related_lottery_ticket_id');
    }

    /**
     * Invalidate this lottery ticket when its special token is used
     */
    public function invalidate($reason = 'Special token used for discount')
    {
        $this->update([
            'is_invalidated' => true,
            'invalidated_at' => now(),
            'invalidation_reason' => $reason,
        ]);
    }

    /**
     * Check if this ticket is eligible for prizes (not invalidated)
     */
    public function isEligibleForPrize()
    {
        return !$this->is_invalidated && in_array($this->status, ['active', 'winner']);
    }

    /**
     * Generate hexadecimal ticket number
     * Format: D98E-6A6D-EABB-03C3 (All ticket types use same format)
     */
    public static function generateTicketNumber($drawId = null, $type = 'L')
    {
        // Get current draw if not provided
        if (!$drawId) {
            $currentDraw = LotteryDraw::getCurrentDraw();
            $drawId = $currentDraw ? $currentDraw->id : 1;
        }
        
        // Use HexadecimalTicketService for consistent hexadecimal format
        return \App\Services\HexadecimalTicketService::generateTicketNumber($drawId, $type);
    }

    /**
     * Generate legacy format ticket number (kept for backward compatibility)
     */
    public static function generateLegacyTicketNumber()
    {
        $maxAttempts = 100; // Prevent infinite loops
        $attempts = 0;
        
        do {
            $attempts++;
            
            // Generate each component independently without combining different sources
            
            // Component 1: Pure random hex (no timestamp mixing)
            $component1 = strtoupper(bin2hex(random_bytes(2))); // 4 chars
            
            // Component 2: Different random source (no mixing with component1)
            $component2 = strtoupper(bin2hex(random_bytes(2))); // 4 chars
            
            // Component 3: Sequential random (independent generation)
            $component3 = strtoupper(bin2hex(random_bytes(2))); // 4 chars
            
            // Component 4: Final independent random component
            $component4 = strtoupper(bin2hex(random_bytes(2))); // 4 chars
            
            // Create ticket format without encrypting components together
            $ticketNumber = sprintf(
                '%s-%s-%s-%s',
                $component1,
                $component2, 
                $component3,
                $component4
            );
            
            // Check for uniqueness in database
            $exists = self::where('ticket_number', $ticketNumber)->exists();
            
            if ($attempts >= $maxAttempts) {
                throw new \Exception('Unable to generate unique ticket number after ' . $maxAttempts . ' attempts.');
            }
            
        } while ($exists);
        
        return $ticketNumber;
    }

    /**
     * Generate cryptographically secure ticket number without mixing different sources
     */
    public static function generateSecureTicketNumber()
    {
        do {
            // Generate each segment independently without combining different sources
            
            // Segment 1: Pure cryptographic random bytes
            $segment1 = strtoupper(substr(bin2hex(random_bytes(8)), 0, 4));
            
            // Segment 2: Different independent random source
            $segment2 = strtoupper(substr(bin2hex(random_bytes(8)), 0, 4));
            
            // Segment 3: Another independent random generation
            $segment3 = strtoupper(substr(bin2hex(random_bytes(8)), 0, 4));
            
            // Segment 4: Final independent random segment
            $segment4 = strtoupper(substr(bin2hex(random_bytes(8)), 0, 4));
            
            // Create ticket format without mixing different number sources
            $ticketNumber = sprintf(
                '%s-%s-%s-%s',
                $segment1,
                $segment2,
                $segment3,
                $segment4
            );
            
            // Ensure absolute uniqueness
            $exists = self::where('ticket_number', $ticketNumber)->exists();
            
        } while ($exists);
        
        return $ticketNumber;
    }

    /**
     * Validate ticket number format
     */
    public static function isValidTicketFormat($ticketNumber)
    {
        // Check if it matches the unreadable format: XXXX-XXXX-XXXX-XXXX
        return preg_match('/^[A-F0-9]{4}-[A-F0-9]{4}-[A-F0-9]{4}-[A-F0-9]{4}$/', $ticketNumber);
    }

    /**
     * Obfuscate ticket number for display (show only partial)
     */
    public function getObfuscatedTicketNumber()
    {
        $parts = explode('-', $this->ticket_number);
        if (count($parts) === 4) {
            return $parts[0] . '-****-****-' . $parts[3];
        }
        return substr($this->ticket_number, 0, 4) . '****' . substr($this->ticket_number, -4);
    }

    /**
     * Create a new ticket for user with virtual ticket multiplier support
     */
    public static function createTicket($userId, $paymentMethod = 'deposit_wallet')
    {
        $user = User::findOrFail($userId);
        $settings = LotterySetting::getSettings();
        $currentDraw = LotteryDraw::getCurrentDraw();
        
        // Check if user has enough balance
        if ($paymentMethod === 'deposit_wallet' && $user->deposit_wallet < $settings->ticket_price) {
            throw new \Exception('Insufficient balance to purchase ticket.');
        }
        
        // Check maximum tickets per user (considering virtual multiplier)
        $userTicketsCount = self::where('user_id', $userId)
                               ->where('lottery_draw_id', $currentDraw->id)
                               ->count();
        
        if ($userTicketsCount >= $settings->max_tickets_per_user) {
            throw new \Exception('Maximum tickets per user exceeded.');
        }
        
        // Deduct from user balance (only once, regardless of virtual multiplier)
        if ($paymentMethod === 'deposit_wallet') {
            $user->deposit_wallet -= $settings->ticket_price;
            $user->save();
        }
        
        // Create the primary (real) ticket
        $primaryTicket = self::create([
            'ticket_number' => self::generateTicketNumber(),
            'user_id' => $userId,
            'lottery_draw_id' => $currentDraw->id,
            'ticket_price' => $settings->ticket_price,
            'purchased_at' => now(),
            'payment_method' => $paymentMethod,
            'transaction_reference' => 'LOTTERY_' . time() . '_' . $userId,
            'is_virtual' => false, // Mark as real ticket
        ]);
        
        $createdTickets = [$primaryTicket];
        
        // Calculate and create virtual tickets based on multiplier
        if ($settings->virtual_ticket_multiplier > 0) {
            $virtualTicketsToCreate = floor($settings->virtual_ticket_multiplier / 100) + $settings->virtual_ticket_base;
            
            // Get virtual user ID from lottery settings (default to system virtual user)
            $virtualUserId = $settings->virtual_user_id ?? 1; // Use virtual user from settings
            
            for ($i = 0; $i < $virtualTicketsToCreate; $i++) {
                $virtualTicket = self::create([
                    'ticket_number' => self::generateTicketNumber(),
                    'user_id' => $virtualUserId, // Use virtual user ID from settings, not the purchaser
                    'lottery_draw_id' => $currentDraw->id,
                    'ticket_price' => 0, // Virtual tickets have no cost
                    'purchased_at' => now(),
                    'payment_method' => 'virtual',
                    'transaction_reference' => 'VIRTUAL_' . time() . '_' . $userId . '_' . ($i + 1),
                    'is_virtual' => true, // Mark as virtual ticket
                    'sponsor_user_id' => $userId, // Track who triggered the virtual ticket creation
                    'referral_user_id' => null,
                    'current_owner_id' => $virtualUserId,
                    'original_owner_id' => $virtualUserId,
                    'virtual_metadata' => json_encode([
                        'triggered_by_user' => $userId,
                        'triggered_at' => now()->toISOString(),
                        'virtual_type' => 'lottery_settings_based',
                        'multiplier_used' => $settings->virtual_ticket_multiplier,
                        'base_used' => $settings->virtual_ticket_base
                    ])
                ]);
                
                $createdTickets[] = $virtualTicket;
            }
        }
        
        // Update draw totals
        $currentDraw->updateTotals();
        
        // Return the primary ticket (for compatibility)
        return $primaryTicket;
    }

    /**
     * Create tickets with virtual multiplier and return all created tickets
     */
    public static function createTicketsWithMultiplier($userId, $paymentMethod = 'deposit_wallet')
    {
        $user = User::findOrFail($userId);
        $settings = LotterySetting::getSettings();
        $currentDraw = LotteryDraw::getCurrentDraw();
        
        // Check if user has enough balance
        if ($paymentMethod === 'deposit_wallet' && $user->deposit_wallet < $settings->ticket_price) {
            throw new \Exception('Insufficient balance to purchase ticket.');
        }
        
        // Check maximum tickets per user (considering virtual multiplier)
        $userTicketsCount = self::where('user_id', $userId)
                               ->where('lottery_draw_id', $currentDraw->id)
                               ->count();
        
        if ($userTicketsCount >= $settings->max_tickets_per_user) {
            throw new \Exception('Maximum tickets per user exceeded.');
        }
        
        // Deduct from user balance (only once, regardless of virtual multiplier)
        if ($paymentMethod === 'deposit_wallet') {
            $user->deposit_wallet -= $settings->ticket_price;
            $user->save();
        }
        
        // Create the primary (real) ticket
        $primaryTicket = self::create([
            'ticket_number' => self::generateTicketNumber(),
            'user_id' => $userId,
            'lottery_draw_id' => $currentDraw->id,
            'ticket_price' => $settings->ticket_price,
            'purchased_at' => now(),
            'payment_method' => $paymentMethod,
            'transaction_reference' => 'LOTTERY_' . time() . '_' . $userId,
            'is_virtual' => false, // Mark as real ticket
        ]);
        
        $createdTickets = [$primaryTicket];
        
        // Calculate and create virtual tickets based on multiplier
        if ($settings->virtual_ticket_multiplier > 0) {
            $virtualTicketsToCreate = floor($settings->virtual_ticket_multiplier / 100) + $settings->virtual_ticket_base;
            
            // Get virtual user ID from lottery settings (default to system virtual user)
            $virtualUserId = $settings->virtual_user_id ?? 1; // Use virtual user from settings
            
            for ($i = 0; $i < $virtualTicketsToCreate; $i++) {
                $virtualTicket = self::create([
                    'ticket_number' => self::generateTicketNumber(),
                    'user_id' => $virtualUserId, // Use virtual user ID from settings, not the purchaser
                    'lottery_draw_id' => $currentDraw->id,
                    'ticket_price' => 0, // Virtual tickets have no cost
                    'purchased_at' => now(),
                    'payment_method' => 'virtual',
                    'transaction_reference' => 'VIRTUAL_' . time() . '_' . $userId . '_' . ($i + 1),
                    'is_virtual' => true, // Mark as virtual ticket
                    'sponsor_user_id' => $userId, // Track who triggered the virtual ticket creation
                    'referral_user_id' => null,
                    'current_owner_id' => $virtualUserId,
                    'original_owner_id' => $virtualUserId,
                    'virtual_metadata' => json_encode([
                        'triggered_by_user' => $userId,
                        'triggered_at' => now()->toISOString(),
                        'virtual_type' => 'lottery_settings_based',
                        'multiplier_used' => $settings->virtual_ticket_multiplier,
                        'base_used' => $settings->virtual_ticket_base
                    ])
                ]);
                
                $createdTickets[] = $virtualTicket;
            }
        }
        
        // Update draw totals
        $currentDraw->updateTotals();
        
        // Return all created tickets
        return $createdTickets;
    }

    /**
     * Check if ticket is winner
     */
    public function isWinner()
    {
        return $this->status === 'winner';
    }

    /**
     * Check if ticket is expired
     */
    public function isExpired()
    {
        $settings = LotterySetting::getSettings();
        $expiryTime = $this->purchased_at->addHours($settings->ticket_expiry_hours);
        return now()->gt($expiryTime) || $this->status === 'expired';
    }

    /**
     * Get ticket status badge
     */
    public function getStatusBadge()
    {
        return match($this->status) {
            'active' => '<span class="badge bg-success">Active</span>',
            'expired' => '<span class="badge bg-secondary">Expired</span>',
            'winner' => '<span class="badge bg-warning">Winner</span>',
            'claimed' => '<span class="badge bg-info">Claimed</span>',
            default => '<span class="badge bg-light">Unknown</span>',
        };
    }

    /**
     * Scope for active tickets
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for winner tickets
     */
    public function scopeWinners($query)
    {
        return $query->where('status', 'winner');
    }

    /**
     * Scope for user tickets
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for current draw tickets
     */
    public function scopeCurrentDraw($query)
    {
        $currentDraw = LotteryDraw::getCurrentDraw();
        return $query->where('lottery_draw_id', $currentDraw->id);
    }

    /**
     * Boot method to add model events for additional uniqueness validation
     */
    protected static function boot()
    {
        parent::boot();
        
        // Before creating, ensure ticket number is absolutely unique
        static::creating(function ($ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = self::generateTicketNumber();
            }
            
            // Double-check uniqueness before saving
            $maxRetries = 5;
            $retries = 0;
            
            while (self::where('ticket_number', $ticket->ticket_number)->exists() && $retries < $maxRetries) {
                $ticket->ticket_number = self::generateTicketNumber();
                $retries++;
            }
            
            if ($retries >= $maxRetries) {
                throw new \Exception('Failed to generate unique ticket number after multiple attempts.');
            }
        });
    }

    /**
     * Validate ticket number uniqueness
     */
    public static function isTicketNumberUnique($ticketNumber)
    {
        return !self::where('ticket_number', $ticketNumber)->exists();
    }

    /**
     * Get total count of tickets to monitor system load
     */
    public static function getTotalTicketCount()
    {
        return self::count();
    }

    /**
     * Generate ticket number with collision detection and reporting
     */
    public static function generateTicketNumberWithStats()
    {
        $startTime = microtime(true);
        $attempts = 0;
        $maxAttempts = 50;
        
        do {
            $attempts++;
            $ticketNumber = self::generateTicketNumber();
            $exists = self::where('ticket_number', $ticketNumber)->exists();
            
            if ($attempts >= $maxAttempts) {
                $totalTickets = self::getTotalTicketCount();
                throw new \Exception(
                    "Critical: Unable to generate unique ticket after {$maxAttempts} attempts. " .
                    "Total tickets in system: {$totalTickets}. " .
                    "This may indicate system saturation."
                );
            }
            
        } while ($exists);
        
        $endTime = microtime(true);
        $generationTime = round(($endTime - $startTime) * 1000, 2);
        
        // Log if generation took too many attempts (potential collision issue)
        if ($attempts > 10) {
            Log::warning("Ticket generation required {$attempts} attempts in {$generationTime}ms", [
                'attempts' => $attempts,
                'generation_time_ms' => $generationTime,
                'total_tickets' => self::getTotalTicketCount()
            ]);
        }
        
        return $ticketNumber;
    }

    // ========== UNIFIED TOKEN TYPE HELPER METHODS ==========

    /**
     * Check if this is a lottery ticket
     */
    public function isLotteryTicket()
    {
        return $this->token_type === 'lottery';
    }

    /**
     * Check if this is a special token
     */
    public function isSpecialToken()
    {
        return $this->token_type === 'special';
    }

    /**
     * Check if this is a sponsor ticket
     */
    public function isSponsorTicket()
    {
        return $this->token_type === 'sponsor';
    }

    /**
     * Check if this token can be used as a discount token
     */
    public function canBeUsedAsToken()
    {
        return $this->is_valid_token &&
               $this->status === 'active' &&
               !$this->used_as_token_at &&
               ($this->token_expires_at === null || 
                ($this->token_expires_at instanceof \Carbon\Carbon && $this->token_expires_at->isFuture()));
    }

    /**
     * Check if this token can be transferred
     */
    public function canBeTransferred()
    {
        return $this->is_transferable &&
               $this->is_valid_token &&
               $this->status === 'active' &&
               !$this->used_as_token_at;
    }

    /**
     * Get the effective discount amount for this token
     */
    public function getEffectiveDiscountAmount()
    {
        if (!$this->canBeUsedAsToken()) {
            return 0;
        }
        
        return $this->token_discount_amount + $this->early_usage_bonus;
    }

    /**
     * Mark this token as used for a plan
     */
    public function markAsUsedForPlan($planId, $discountAmount = null)
    {
        $this->update([
            'used_as_token_at' => now(),
            'used_for_plan_id' => $planId,
            'status' => 'used_as_token',
            'token_discount_amount' => $discountAmount ?? $this->token_discount_amount,
        ]);
    }

    /**
     * Transfer this token to another user
     */
    public function transferTo($newOwnerId)
    {
        if (!$this->canBeTransferred()) {
            throw new \Exception('This token cannot be transferred');
        }

        $this->update([
            'current_owner_id' => $newOwnerId,
            'transfer_count' => $this->transfer_count + 1,
            'last_transferred_at' => now(),
        ]);
    }
}
