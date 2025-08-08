<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FirstPurchaseCommission extends Model
{
    protected $fillable = [
        'referral_user_id',
        'sponsor_user_id',
        'plan_id',
        'purchase_amount',
        'commission_amount',
        'commission_paid',
        'special_ticket_issued',
        'special_ticket_id',
        'processed_at',
        'transaction_reference',
    ];

    protected $casts = [
        'purchase_amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'commission_paid' => 'boolean',
        'special_ticket_issued' => 'boolean',
        'processed_at' => 'datetime',
    ];

    /**
     * Get the referral user who made first purchase
     */
    public function referralUser()
    {
        return $this->belongsTo(User::class, 'referral_user_id');
    }

    /**
     * Get the sponsor user who gets commission
     */
    public function sponsorUser()
    {
        return $this->belongsTo(User::class, 'sponsor_user_id');
    }

    /**
     * Get the plan that was purchased
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get the special lottery ticket issued
     */
    public function specialTicket()
    {
        return $this->belongsTo(SpecialLotteryTicket::class, 'special_ticket_id');
    }

    /**
     * Process first purchase commission
     */
    public static function processFirstPurchase($referralUserId, $sponsorUserId, $planId, $purchaseAmount)
    {
        // Check if this user already has a first purchase commission
        if (self::where('referral_user_id', $referralUserId)->exists()) {
            return null; // Not a first purchase
        }

        $sponsor = User::find($sponsorUserId);
        if (!$sponsor) {
            return null;
        }

        $trx = getTrx();
        $commissionAmount = 0.00; // $25 commission

        // Create commission record
        $commission = self::create([
            'referral_user_id' => $referralUserId,
            'sponsor_user_id' => $sponsorUserId,
            'plan_id' => $planId,
            'purchase_amount' => $purchaseAmount,
            'commission_amount' => $commissionAmount,
            'commission_paid' => false,
            'special_ticket_issued' => false,
            'processed_at' => now(),
            'transaction_reference' => $trx,
        ]);

        // Instead of creating separate commission tickets, create only special lottery tickets
        // Special tickets serve as both commission reward and lottery participation
        // 1 special ticket per $25 deposited, each ticket worth $2 and serves as commission
        $numberOfTickets = floor($purchaseAmount / 25);
        \Illuminate\Support\Facades\Log::info("Creating {$numberOfTickets} special tickets for sponsor {$sponsorUserId} from first purchase by user {$referralUserId} (amount: \${$purchaseAmount})");
        
        $specialTokens = SpecialLotteryTicket::createForSponsorWithBaseNumber($sponsorUserId, $referralUserId, $purchaseAmount, null);

        // Extract base number for record keeping
        $baseTicketNumber = null;
        if (is_array($specialTokens) && !empty($specialTokens)) {
            $baseTicketNumber = $specialTokens[0]->ticket_number;
        } else if ($specialTokens) {
            $baseTicketNumber = $specialTokens->ticket_number;
        }
        
        // Handle both array and single ticket return types
        $firstSpecialTokenId = null;
        if (is_array($specialTokens)) {
            $firstSpecialTokenId = !empty($specialTokens) ? $specialTokens[0]->id : null;
        } else if ($specialTokens) {
            $firstSpecialTokenId = $specialTokens->id;
        }

        // Update commission record
        $commission->update([
            'commission_paid' => true,
            'special_ticket_issued' => true,
            'special_ticket_id' => $firstSpecialTokenId,
        ]);

        // Send notification to sponsor about received tickets
        if ($numberOfTickets > 0) {
            notifySponsorTicketReceived($sponsorUserId, $numberOfTickets, $referralUserId, $purchaseAmount);
            
            \Illuminate\Support\Facades\Log::info("Sponsor ticket notification sent to user {$sponsorUserId} for {$numberOfTickets} tickets");
        }

        return $commission;
    }

    /**
     * Check if user is making their first purchase
     */
    public static function isFirstPurchase($userId)
    {
        return !self::where('referral_user_id', $userId)->exists();
    }

        /**
     * Create lottery tickets for commission
     */
    public static function createCommissionTickets($sponsorUserId, $referralUserId, $purchaseAmount)
    {
        // Calculate number of commission tickets (1 per $25 deposited)
        $numberOfTickets = floor($purchaseAmount / 25);
        
        if ($numberOfTickets <= 0) {
            return null; // No tickets if purchase amount is less than $25
        }
        
        $createdTickets = [];
        
        // Generate a shared base ticket number for coordination with special tokens
        $baseTicketNumber = \App\Models\LotteryTicket::generateTicketNumber();
        
        // Create multiple lottery tickets for commission (1 per $25 deposited)
        for ($i = 1; $i <= $numberOfTickets; $i++) {
            // Use the same ticket number format as regular lottery tickets
            $ticketNumber = \App\Models\LotteryTicket::generateTicketNumber();
            
            $ticket = \App\Models\LotteryTicket::create([
                'ticket_number' => $ticketNumber,
                'user_id' => $sponsorUserId,
                'current_owner_id' => $sponsorUserId,
                'original_owner_id' => $sponsorUserId,
                'lottery_draw_id' => \App\Models\LotteryDraw::getCurrentDraw()->id,
                'ticket_price' => 2.00,
                'status' => 'active',
                'refund_amount' => 1.00,
                'purchased_at' => now(),
                'transaction_reference' => 'COMM_REF_' . time() . '_' . $referralUserId . '_' . $i,
                'is_transferable' => true,
                'transfer_count' => 0,
            ]);
            
            $createdTickets[] = $ticket;
        }
        
        // Don't create special tickets here - they should be created separately
        // This prevents double creation of special tickets
        
        // Return only commission tickets for reference
        return [
            'commission_tickets' => $createdTickets,
            'base_ticket_number' => $baseTicketNumber
        ];
    }

    /**
     * Generate unique commission ticket number
     */
    public static function generateCommissionTicketNumber()
    {
        do {
            $ticketNumber = 'COMM-' . strtoupper(substr(md5(time() . rand()), 0, 4)) . '-' . strtoupper(substr(md5(time() . rand()), 4, 4));
        } while (LotteryTicket::where('ticket_number', $ticketNumber)->exists());

        return $ticketNumber;
    }

    /**
     * Get commission statistics for sponsor
     */
    public static function getSponsorStats($sponsorUserId)
    {
        $commissions = self::where('sponsor_user_id', $sponsorUserId);
        $totalCommissions = $commissions->count();
        $totalAmount = $commissions->where('commission_paid', true)->sum('commission_amount');
        
        return [
            'total_commissions' => $totalCommissions,
            'total_commission_amount' => $totalAmount,
            'avg_commission' => $totalCommissions > 0 ? $totalAmount / $totalCommissions : 0,
            'total_amount' => $totalAmount, // Keep for backward compatibility
            'pending_amount' => $commissions->where('commission_paid', false)->sum('commission_amount'),
            'special_tickets_issued' => $commissions->where('special_ticket_issued', true)->count(),
        ];
    }
}
