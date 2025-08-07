<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class SpecialLotteryTicket extends Model
{
    use HasFactory;

    /**
     * DEPRECATED: This model is deprecated. 
     * All special tickets should be created in the unified lottery_tickets table.
     * Prevent direct creation in this table.
     */
    public static function create(array $attributes = [])
    {
        throw new \Exception('Direct creation in special_lottery_tickets table is deprecated. Use LotteryTicket model with token_type="special" instead.');
    }

    /**
     * DEPRECATED: Prevent mass assignment to old table
     */
    public static function insert(array $values)
    {
        throw new \Exception('Insertion into special_lottery_tickets table is deprecated. Use LotteryTicket model with token_type="special" instead.');
    }
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'sponsor_user_id',
        'referral_user_id',
        'current_owner_id',
        'original_owner_id',
        'lottery_draw_id',
        'ticket_price',
        'status',
        'prize_amount',
        'refund_amount',
        'purchased_at',
        'claimed_at',
        'used_as_token_at',
        'token_discount_amount',
        'used_for_plan_id',
        'related_lottery_ticket_id',
        'early_usage_bonus',
        'is_valid_token',
        'token_expires_at',
        'transaction_reference',
        'is_transferable',
        'transfer_count',
        'last_transferred_at',
    ];

    protected $casts = [
        'purchased_at' => 'datetime',
        'claimed_at' => 'datetime',
        'used_as_token_at' => 'datetime',
        'token_expires_at' => 'datetime',
        'last_transferred_at' => 'datetime',
        'ticket_price' => 'decimal:2',
        'prize_amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'token_discount_amount' => 'decimal:2',
        'early_usage_bonus' => 'decimal:2',
        'is_valid_token' => 'boolean',
        'is_transferable' => 'boolean',
    ];

    /**
     * Get the sponsor user who received this ticket
     */
    public function sponsor()
    {
        return $this->belongsTo(User::class, 'sponsor_user_id');
    }

    /**
     * Get the referral user who made first purchase
     */
    public function referral()
    {
        return $this->belongsTo(User::class, 'referral_user_id');
    }

    /**
     * Alias for referral relationship (for compatibility)
     */
    public function referralUser()
    {
        return $this->belongsTo(User::class, 'referral_user_id');
    }

    /**
     * Get the lottery draw this ticket belongs to
     */
    public function lotteryDraw()
    {
        return $this->belongsTo(LotteryDraw::class);
    }

    /**
     * Get the plan where this token was used
     */
    public function usedForPlan()
    {
        return $this->belongsTo(Plan::class, 'used_for_plan_id');
    }

    /**
     * Get the related lottery ticket that this special ticket is derived from
     */
    public function relatedLotteryTicket()
    {
        return $this->belongsTo(LotteryTicket::class, 'related_lottery_ticket_id');
    }

    /**
     * Get winner record if this ticket won
     */
    public function winner()
    {
        return $this->hasOne(LotteryWinner::class, 'lottery_ticket_id');
    }

    /**
     * Generate unique ticket number for special tickets
     * Using same format as regular lottery tickets
     */
    public static function generateSpecialTicketNumber()
    {
        // Use the same hexadecimal format as regular lottery tickets
        return \App\Services\HexadecimalTicketService::generateTicketNumber();
    }

    /**
     * Create special lottery ticket for sponsor
     */
    public static function createForSponsor($sponsorUserId, $referralUserId, $purchaseAmount)
    {
        return self::createForSponsorWithBaseNumber($sponsorUserId, $referralUserId, $purchaseAmount, null);
    }

    /**
     * Create special lottery ticket for sponsor with specified base number
     * Now creates tickets in the unified lottery_tickets table
     */
    public static function createForSponsorWithBaseNumber($sponsorUserId, $referralUserId, $purchaseAmount, $baseTicketNumber = null)
    {
        $currentDraw = \App\Models\LotteryDraw::getCurrentDraw();
        
        // Calculate number of special tickets (1 per $25, same as commission tickets)
        $numberOfTickets = floor($purchaseAmount / 25);
        
        \Illuminate\Support\Facades\Log::info("SpecialLotteryTicket: Creating {$numberOfTickets} special tickets for sponsor {$sponsorUserId} from user {$referralUserId} investment of \${$purchaseAmount}");
        \Illuminate\Support\Facades\Log::info("SpecialLotteryTicket: Each special ticket will automatically receive \$1 refund if not winning, handled by lottery draw process");
        
        if ($numberOfTickets <= 0) {
            return null; // No tickets if purchase amount is less than $25
        }
        
        $createdTickets = [];
        
        // Create multiple special lottery tickets in the unified table (1 per $25 deposited)
        for ($i = 1; $i <= $numberOfTickets; $i++) {
            // Use hexadecimal ticket format for all tickets
            $ticketNumber = \App\Services\HexadecimalTicketService::generateTicketNumber();
            
            $ticket = \App\Models\LotteryTicket::create([
                'ticket_number' => $ticketNumber,
                'user_id' => $sponsorUserId, // Sponsor gets the tickets
                'lottery_draw_id' => $currentDraw->id,
                'ticket_price' => 2.00, // Same as regular lottery ticket
                'purchased_at' => now(),
                'status' => 'active',
                'token_type' => 'special', // Mark as special ticket
                'sponsor_user_id' => $sponsorUserId,
                'referral_user_id' => $referralUserId,
                'current_owner_id' => $sponsorUserId, // Initially owned by sponsor
                'original_owner_id' => $sponsorUserId, // Original owner is sponsor
                'is_valid_token' => true,
                'is_transferable' => true,
                'token_expires_at' => $currentDraw->draw_date, // Expires after draw
                'refund_amount' => 1.00,
                'payment_method' => 'sponsor_reward',
                'transaction_reference' => 'SPONSOR_' . time() . '_' . $sponsorUserId . '_' . $i,
                'transfer_count' => 0,
            ]);
            
            $createdTickets[] = $ticket;
        }

        // Return first ticket for backward compatibility, or collection if multiple
        return count($createdTickets) === 1 ? $createdTickets[0] : $createdTickets;
    }

    /**
     * Use ticket as discount token for plan purchase
     */
    public function useAsToken($planId, $planAmount)
    {
        if (!$this->canBeUsedAsToken()) {
            throw new \Exception('This ticket cannot be used as a discount token.');
        }

        // Calculate 5% discount + early usage bonus
        $baseDiscount = $planAmount * 0.05; // 5% of plan amount
        $earlyUsageBonus = $this->calculateEarlyUsageBonus();
        $totalDiscount = $baseDiscount + ($baseDiscount * $earlyUsageBonus / 100);

        $this->update([
            'status' => 'used_as_token',
            'used_as_token_at' => now(),
            'token_discount_amount' => $totalDiscount,
            'used_for_plan_id' => $planId,
            'early_usage_bonus' => $earlyUsageBonus,
            'is_valid_token' => false,
        ]);

        return $totalDiscount;
    }

    /**
     * Calculate early usage bonus (0-5% based on how early it's used)
     */
    public function calculateEarlyUsageBonus()
    {
        if (!$this->token_expires_at) {
            return 0;
        }

        $totalTime = $this->purchased_at->diffInDays($this->token_expires_at);
        $timeUsed = $this->purchased_at->diffInDays(now());
        
        // If used within first 20% of validity period, get 5% bonus
        // If used within first 40% of validity period, get 3% bonus
        // If used within first 60% of validity period, get 1% bonus
        // Otherwise, no bonus
        
        if ($totalTime <= 0) return 0;
        
        $usagePercentage = ($timeUsed / $totalTime) * 100;
        
        if ($usagePercentage <= 20) {
            return 5.00;
        } elseif ($usagePercentage <= 40) {
            return 3.00;
        } elseif ($usagePercentage <= 60) {
            return 1.00;
        }
        
        return 0;
    }

    /**
     * Accessor for backward compatibility - maps expires_at to token_expires_at
     */
    public function getExpiresAtAttribute()
    {
        return $this->token_expires_at;
    }

    /**
     * Check if ticket can be used as token
     */
    public function canBeUsedAsToken()
    {
        return $this->status === 'active' 
            && $this->is_valid_token 
            && $this->token_expires_at 
            && now()->lt($this->token_expires_at)
            && !$this->isUsed()
            && !$this->isDrawn();
    }

    /**
     * Check if user can use this token (prevent self-usage)
     */
    public function canBeUsedByUser($userId)
    {
        return $this->canBeUsedAsToken() && 
               $this->current_owner_id == $userId &&
               $this->original_owner_id != $userId; // Prevent original owner from using their own token
    }

    /**
     * Process after lottery draw (refund if not winner)
     */
    public function processAfterDraw()
    {
        if ($this->status !== 'active') {
            return;
        }

        // If not a winner, provide refund
        if (!$this->isWinner()) {
            $this->processRefund();
        }

        // Mark token as expired after draw
        $this->update([
            'is_valid_token' => false,
        ]);
    }

    /**
     * Process refund for non-winning ticket
     */
    public function processRefund()
    {
        $sponsor = $this->sponsor;
        if ($sponsor) {
            $sponsor->deposit_wallet += $this->refund_amount;
            $sponsor->save();

            // Create transaction record
            $transaction = new Transaction();
            $transaction->user_id = $sponsor->id;
            $transaction->amount = $this->refund_amount;
            $transaction->post_balance = $sponsor->deposit_wallet;
            $transaction->charge = 0;
            $transaction->trx_type = '+';
            $transaction->trx = getTrx();
            $transaction->wallet_type = 'deposit_wallet';
            $transaction->remark = 'special_lottery_refund';
            $transaction->details = "Refund for non-winning special lottery ticket {$this->ticket_number}";
            $transaction->save();

            $this->update([
                'status' => 'refunded',
                'claimed_at' => now(),
            ]);
        }
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
        return $this->status === 'expired' 
            || ($this->token_expires_at && now()->gt($this->token_expires_at));
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
            'lost' => '<span class="badge bg-danger">Lost</span>',
            'refunded' => '<span class="badge bg-info">Refunded</span>',
            'used_as_token' => '<span class="badge bg-primary">Used as Token</span>',
            default => '<span class="badge bg-light">Unknown</span>',
        };
    }

    /**
     * Get discount potential for this token (up to 5% of plan amount)
     */
    public function getDiscountPotential($planAmount)
    {
        if (!$this->canBeUsedAsToken()) {
            return 0;
        }

        // Maximum 5% discount of plan amount
        $maxDiscount = $planAmount * 0.05; // 5% of plan amount
        $earlyBonus = $this->calculateEarlyUsageBonus();
        
        // Base discount is 5% of plan amount (not ticket price)
        $baseDiscount = $maxDiscount;
        
        // Early usage bonus adds to the discount
        $totalDiscount = $baseDiscount + ($baseDiscount * $earlyBonus / 100);
        
        return $totalDiscount;
    }

    /**
     * Scope for active tickets
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for valid tokens
     */
    public function scopeValidTokens($query)
    {
        return $query->where('status', 'active')
                    ->where('is_valid_token', true)
                    ->where('token_expires_at', '>', now());
    }

    /**
     * Scope for user tickets (as sponsor)
     */
    public function scopeForSponsor($query, $userId)
    {
        return $query->where('sponsor_user_id', $userId);
    }

    /**
     * Scope for tickets from specific referral
     */
    public function scopeFromReferral($query, $userId)
    {
        return $query->where('referral_user_id', $userId);
    }

    /**
     * Transfer relationships
     */
    public function transfers()
    {
        return $this->hasMany(SpecialTicketTransfer::class, 'special_ticket_id');
    }

    public function currentOwner()
    {
        return $this->belongsTo(User::class, 'current_owner_id');
    }

    public function originalOwner()
    {
        return $this->belongsTo(User::class, 'original_owner_id');
    }

    /**
     * Check if ticket has been used as a discount token
     */
    public function isUsed()
    {
        return $this->status === 'used_as_token' || !is_null($this->used_as_token_at);
    }

    /**
     * Check if lottery has been drawn for this ticket
     */
    public function isDrawn()
    {
        return in_array($this->status, ['winner', 'lost', 'expired', 'refunded']);
    }

    /**
     * Transfer methods
     */
    public function canBeTransferred()
    {
        return $this->is_transferable && 
               $this->status === 'active' && 
               !$this->isUsed() && 
               !$this->isDrawn();
    }

    public function isOwner($userId)
    {
        return $this->current_owner_id == $userId;
    }

    public function transferTo($newOwnerId, $transferType = 'gift', $amount = 0, $message = null)
    {
        if (!$this->canBeTransferred()) {
            throw new \Exception('This ticket cannot be transferred.');
        }

        $transfer = SpecialTicketTransfer::createTransfer(
            $this->id,
            $this->current_owner_id,
            $newOwnerId,
            $transferType,
            $message,
            $amount
        );

        return $transfer;
    }

    public function getTransferHistory()
    {
        return $this->transfers()
                   ->with(['fromUser', 'toUser'])
                   ->orderBy('created_at', 'desc')
                   ->get();
    }

    protected static function booted()
    {
        // Set default values for ticket creation
        static::creating(function ($ticket) {
            if (!$ticket->ticket_number) {
                $ticket->ticket_number = self::generateSpecialTicketNumber();
            }
            if (!$ticket->current_owner_id) {
                $ticket->current_owner_id = $ticket->user_id;
            }
            if (!$ticket->original_owner_id) {
                $ticket->original_owner_id = $ticket->user_id;
            }
            if (!isset($ticket->is_transferable)) {
                $ticket->is_transferable = true; // Default to transferable
            }
            if (!isset($ticket->transfer_count)) {
                $ticket->transfer_count = 0;
            }
        });
    }
}
