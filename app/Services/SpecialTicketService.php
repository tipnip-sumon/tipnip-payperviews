<?php

namespace App\Services;

use App\Models\User;
use App\Models\Plan;
use App\Models\SpecialLotteryTicket;
use App\Models\SpecialTicketTransfer;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SpecialTicketService
{
    /**
     * Get available special tickets for user that can be used as tokens
     * Excludes tokens originally created by the same user (prevents self-usage)
     */
    public function getAvailableTokens($userId)
    {
        return SpecialLotteryTicket::where('current_owner_id', $userId)
                                  ->where('original_owner_id', '!=', $userId) // Prevent self-usage
                                  ->validTokens()
                                  ->with(['lotteryDraw', 'referral', 'originalOwner', 'currentOwner'])
                                  ->orderBy('early_usage_bonus', 'desc') // Highest bonus first
                                  ->orderBy('purchased_at', 'asc')
                                  ->get();
    }

    /**
     * Calculate potential discount for plan purchase using special tokens
     */
    public function calculatePotentialDiscount($userId, $planAmount)
    {
        $availableTokens = $this->getAvailableTokens($userId);
        $totalDiscount = 0;
        $tokenDetails = [];

        foreach ($availableTokens as $token) {
            $discount = $token->getDiscountPotential($planAmount - $totalDiscount);
            if ($discount > 0) {
                $totalDiscount += $discount;
                $tokenDetails[] = [
                    'token' => $token,
                    'discount' => $discount,
                    'early_bonus' => $token->calculateEarlyUsageBonus()
                ];
                
                // Don't exceed plan amount
                if ($totalDiscount >= $planAmount) {
                    $totalDiscount = $planAmount;
                    break;
                }
            }
        }

        return [
            'total_discount' => $totalDiscount,
            'tokens_used' => $tokenDetails,
            'final_amount' => max(0, $planAmount - $totalDiscount)
        ];
    }

    /**
     * Apply special tokens as discount to plan purchase
     */
    public function applyTokensToPlantPurchase($userId, $planId, $planAmount, $tokenIds = null)
    {
        try {
            DB::beginTransaction();

            $user = User::findOrFail($userId);
            $plan = Plan::findOrFail($planId);

            // Get tokens to use - filter out self-created tokens to prevent self-usage
            if ($tokenIds) {
                $tokensToUse = SpecialLotteryTicket::whereIn('id', $tokenIds)
                                                 ->where('current_owner_id', $userId)
                                                 ->where('original_owner_id', '!=', $userId) // Prevent self-usage
                                                 ->validTokens()
                                                 ->take(1) // Only use one token
                                                 ->get();
            } else {
                $availableTokens = SpecialLotteryTicket::where('current_owner_id', $userId)
                                                     ->where('original_owner_id', '!=', $userId) // Prevent self-usage
                                                     ->validTokens()
                                                     ->orderBy('early_usage_bonus', 'desc') // Use highest bonus first
                                                     ->take(1)
                                                     ->get();
                $tokensToUse = $availableTokens;
            }

            if ($tokensToUse->isEmpty()) {
                throw new \Exception('No valid tokens available for discount (cannot use your own tokens)');
            }

            $token = $tokensToUse->first();
            
            // Verify token can be used by this user
            if (!$token->canBeUsedByUser($userId)) {
                throw new \Exception('Token cannot be used by this user');
            }

            // Calculate discount
            $discountAmount = $token->getDiscountPotential($planAmount);
            $finalAmount = $planAmount - $discountAmount;

            // Check if user has sufficient balance for the final amount
            if ($user->deposit_wallet < $finalAmount) {
                throw new \Exception("Insufficient balance after discount. Required: \${$finalAmount}, Available: \${$user->deposit_wallet}");
            }

            // Deduct the final amount (after discount) from user's wallet
            $user->deposit_wallet -= $finalAmount;
            $user->save();

            // Mark token as used
            $token->useAsToken($planId, $planAmount);

            // Create transaction record showing the complete purchase with discount
            $transaction = new Transaction();
            $transaction->user_id = $userId;
            $transaction->amount = $finalAmount; // Amount actually deducted from wallet
            $transaction->post_balance = $user->deposit_wallet;
            $transaction->charge = 0;
            $transaction->trx_type = '-'; // Money deducted from wallet
            $transaction->trx = getTrx();
            $transaction->wallet_type = 'deposit_wallet';
            $transaction->remark = 'plan_purchase_with_token_discount';
            $transaction->details = "Plan purchase: {$plan->name}. Original: \${$planAmount}, Discount: \${$discountAmount} (Token #{$token->ticket_number}), Paid: \${$finalAmount}";
            $transaction->save();

            DB::commit();

            return [
                'success' => true,
                'total_discount' => $discountAmount,
                'final_amount' => $finalAmount,
                'amount_deducted' => $finalAmount,
                'tokens_used' => [[
                    'token_id' => $token->id,
                    'token_number' => $token->ticket_number,
                    'discount_applied' => $discountAmount,
                    'early_bonus' => $token->early_usage_bonus,
                    'discount_percentage' => $token->discount_percentage + $token->early_usage_bonus
                ]],
                'transaction_id' => $transaction->id
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to apply special tokens: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Process special tickets after lottery draw
     */
    public function processTicketsAfterDraw($drawId)
    {
        $tickets = SpecialLotteryTicket::where('lottery_draw_id', $drawId)
                                     ->where('status', 'active')
                                     ->get();

        $processed = 0;
        $refunded = 0;

        foreach ($tickets as $ticket) {
            try {
                $ticket->processAfterDraw();
                $processed++;
                
                if ($ticket->status === 'refunded') {
                    $refunded++;
                }
            } catch (\Exception $e) {
                Log::error("Failed to process special ticket {$ticket->id}: " . $e->getMessage());
            }
        }

        return [
            'processed' => $processed,
            'refunded' => $refunded
        ];
    }

    /**
     * Get special ticket statistics for user
     */
    public function getUserTicketStats($userId)
    {
        $tickets = SpecialLotteryTicket::forSponsor($userId)->get();

        // Calculate expired tickets (either status='expired' OR token_expires_at has passed)
        $expiredTickets = $tickets->filter(function($ticket) {
            return $ticket->status === 'expired' || 
                   ($ticket->token_expires_at && now()->gt($ticket->token_expires_at));
        });

        return [
            'total_tickets' => $tickets->count(),
            'active_tickets' => $tickets->where('status', 'active')->count(),
            'active_tokens' => $tickets->where('is_valid_token', true)->where('status', 'active')->count(),
            'valid_tokens' => $tickets->where('is_valid_token', true)->where('status', 'active')->count(),
            'used_tokens' => $tickets->where('status', 'used_as_token')->count(),
            'used_as_tokens' => $tickets->where('status', 'used_as_token')->count(),
            'winning_tickets' => $tickets->where('status', 'winner')->count(),
            'refunded_tickets' => $tickets->where('status', 'refunded')->count(),
            'expired_tickets' => $expiredTickets->count(),
            'total_investment' => $tickets->sum('ticket_price'),
            'total_token_value' => $tickets->where('is_valid_token', true)->where('status', 'active')->sum('token_discount_amount'),
            'total_discount_used' => $tickets->where('status', 'used_as_token')->sum('token_discount_amount'),
            'total_bonuses' => $tickets->where('status', 'used_as_token')->sum('early_usage_bonus'),
            'total_refunds_received' => $tickets->where('status', 'refunded')->sum('refund_amount'),
        ];
    }

    /**
     * Get special tickets from referrals statistics
     */
    public function getReferralTicketStats($userId)
    {
        $tickets = SpecialLotteryTicket::fromReferral($userId)->get();
        $activeTokens = $tickets->where('is_valid_token', true)->where('status', 'active');

        return [
            'earned_tickets' => $tickets->count(),
            'active_referral_tokens' => $activeTokens->count(),
            'total_referral_value' => $activeTokens->sum('token_discount_amount'),
            'earning_rate' => $tickets->count() > 0 ? ($activeTokens->count() / $tickets->count()) * 100 : 0,
            'tickets_generated' => $tickets->count(), // Keep for backward compatibility
            'sponsors_benefited' => $tickets->pluck('sponsor_user_id')->unique()->count(),
        ];
    }

    /**
     * Check if user has valid tokens for plan purchase
     */
    public function hasValidTokensForPlan($userId, $planAmount)
    {
        $availableTokens = $this->getAvailableTokens($userId);
        $totalPotentialDiscount = 0;

        foreach ($availableTokens as $token) {
            $totalPotentialDiscount += $token->getDiscountPotential($planAmount);
            if ($totalPotentialDiscount >= $planAmount) {
                return true;
            }
        }

        return $totalPotentialDiscount > 0;
    }

    /**
     * Transfer token to another user
     */
    public function transferToken($ticketId, $fromUserId, $toUserId, $transferType = 'gift', $amount = 0, $message = null)
    {
        $ticket = SpecialLotteryTicket::findOrFail($ticketId);
        
        // Validate transfer
        if (!$ticket->isOwner($fromUserId)) {
            throw new \Exception('You do not own this ticket.');
        }

        if (!$ticket->canBeTransferred()) {
            throw new \Exception('This ticket cannot be transferred.');
        }

        // Create transfer request
        $transfer = $ticket->transferTo($toUserId, $transferType, $amount, $message);
        
        return $transfer;
    }

    /**
     * Accept incoming transfer
     */
    public function acceptTransfer($transferId, $userId)
    {
        $transfer = SpecialTicketTransfer::findOrFail($transferId);
        
        if ($transfer->to_user_id != $userId) {
            throw new \Exception('This transfer is not for you.');
        }

        $transfer->acceptTransfer();
        return $transfer;
    }

    /**
     * Get user's transfer statistics
     */
    public function getUserTransferStats($userId)
    {
        $incoming = SpecialTicketTransfer::incoming($userId)->count();
        $outgoing = SpecialTicketTransfer::outgoing($userId)->count();
        $pendingIncoming = SpecialTicketTransfer::incoming($userId)->pending()->count();
        $pendingOutgoing = SpecialTicketTransfer::outgoing($userId)->pending()->count();

        return [
            'total_received' => $incoming,
            'total_sent' => $outgoing,
            'pending_incoming' => $pendingIncoming,
            'pending_outgoing' => $pendingOutgoing,
        ];
    }
}
