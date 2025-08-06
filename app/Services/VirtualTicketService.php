<?php

namespace App\Services;

use App\Models\LotteryTicket;
use App\Models\LotteryDraw;
use App\Models\LotterySetting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class VirtualTicketService
{
    /**
     * Generate virtual tickets for a lottery draw based on settings
     */
    public function generateVirtualTickets($drawId, $realTicketCount = null)
    {
        $draw = LotteryDraw::find($drawId);
        if (!$draw) {
            throw new \Exception("Lottery draw not found");
        }
        
        $settings = LotterySetting::getSettings();
        
        // Check if virtual tickets are enabled
        if (!$settings->enable_virtual_tickets) {
            return ['generated' => 0, 'message' => 'Virtual tickets are disabled'];
        }
        
        // Get current real ticket count if not provided
        if ($realTicketCount === null) {
            $realTicketCount = LotteryTicket::where('lottery_draw_id', $drawId)
                ->where('is_virtual', false)
                ->count();
        }
        
        // Calculate how many virtual tickets we need
        $targetVirtualCount = $this->calculateTargetVirtualCount($realTicketCount, $settings);
        
        // Get current virtual ticket count
        $currentVirtualCount = LotteryTicket::where('lottery_draw_id', $drawId)
            ->where('is_virtual', true)
            ->count();
        
        $ticketsToGenerate = max(0, $targetVirtualCount - $currentVirtualCount);
        
        if ($ticketsToGenerate <= 0) {
            return [
                'generated' => 0, 
                'message' => 'Virtual ticket count is already optimal',
                'current' => $currentVirtualCount,
                'target' => $targetVirtualCount
            ];
        }
        
        // Get virtual user ID
        $virtualUserId = $settings->virtual_user_id ?? 1;
        
        // Ensure virtual user exists
        $virtualUser = User::find($virtualUserId);
        if (!$virtualUser) {
            throw new \Exception("Virtual user (ID: {$virtualUserId}) not found");
        }
        
        $generated = 0;
        $batchSize = 50; // Generate in batches to avoid memory issues
        
        DB::beginTransaction();
        try {
            for ($i = 0; $i < $ticketsToGenerate; $i += $batchSize) {
                $batchCount = min($batchSize, $ticketsToGenerate - $i);
                $tickets = [];
                
                for ($j = 0; $j < $batchCount; $j++) {
                    // Generate virtual ticket in same format as real tickets: XXXX-XXXX-XXXX-XXXX_VT1
                    $baseNumber = $this->generateLegacyTicketNumber();
                    $ticketNumber = $baseNumber . '_VT' . ($generated + $j + 1);
                    
                    $tickets[] = [
                        'ticket_number' => $ticketNumber,
                        'user_id' => $virtualUserId,
                        'lottery_draw_id' => $drawId,
                        'ticket_price' => $draw->ticket_price ?? 2.00,
                        'purchased_at' => now(),
                        'status' => 'active',
                        'token_type' => 'lottery',
                        'sponsor_user_id' => null,
                        'referral_user_id' => null,
                        'current_owner_id' => $virtualUserId,
                        'original_owner_id' => $virtualUserId,
                        'is_valid_token' => true,
                        'is_transferable' => false,
                        'transfer_count' => 0,
                        'last_transferred_at' => null,
                        'token_discount_amount' => 0.00,
                        'used_for_plan_id' => null,
                        'early_usage_bonus' => 0.00,
                        'token_expires_at' => null, // Virtual tickets don't expire by default
                        'refund_amount' => 0.00,
                        'used_as_token_at' => null, // Will be set when ticket is actually used
                        'prize_amount' => 0.00,
                        'claimed_at' => null, // Will be set when prize is claimed
                        'payment_method' => 'virtual',
                        'transaction_reference' => 'VIRTUAL_' . Str::random(10),
                        'is_virtual' => true,
                        'virtual_user_type' => 'system_generated',
                        'virtual_metadata' => json_encode([
                            'generated_at' => now()->toISOString(),
                            'draw_number' => $draw->draw_number,
                            'real_tickets_at_generation' => $realTicketCount,
                            'target_percentage' => $settings->virtual_ticket_percentage,
                            'batch_number' => ceil(($i + 1) / $batchSize),
                            'format' => 'legacy'
                        ]),
                        'is_invalidated' => false,
                        'invalidated_at' => null,
                        'invalidation_reason' => null,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
                
                LotteryTicket::insert($tickets);
                $generated += $batchCount;
            }
            
            DB::commit();
            
            return [
                'generated' => $generated,
                'message' => "Successfully generated {$generated} virtual tickets",
                'current' => $currentVirtualCount + $generated,
                'target' => $targetVirtualCount,
                'real_tickets' => $realTicketCount
            ];
            
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception("Failed to generate virtual tickets: " . $e->getMessage());
        }
    }
    
    /**
     * Calculate target virtual ticket count based on settings
     */
    private function calculateTargetVirtualCount($realTicketCount, $settings)
    {
        $percentage = $settings->virtual_ticket_percentage / 100;
        $minVirtual = $settings->min_virtual_tickets ?? 0;
        $maxVirtual = $settings->max_virtual_tickets ?? 1000;
        
        // Calculate based on percentage of real tickets
        $targetCount = ceil($realTicketCount * $percentage);
        
        // Apply min/max constraints
        $targetCount = max($minVirtual, $targetCount);
        $targetCount = min($maxVirtual, $targetCount);
        
        return $targetCount;
    }
    
    /**
     * Generate a virtual ticket number in hexadecimal format
     */
    private function generateVirtualTicketNumber($drawNumber, $sequenceNumber)
    {
        // Use HexadecimalTicketService for consistent format across all tickets
        return \App\Services\HexadecimalTicketService::generateVirtualTicketNumber($drawNumber);
    }
    
    /**
     * Clean up old virtual tickets (for maintenance)
     */
    public function cleanupOldVirtualTickets($daysOld = 30)
    {
        $cutoffDate = now()->subDays($daysOld);
        
        $deleted = LotteryTicket::where('is_virtual', true)
            ->where('created_at', '<', $cutoffDate)
            ->whereHas('lotteryDraw', function($query) {
                $query->where('status', 'completed');
            })
            ->delete();
            
        return $deleted;
    }
    
    /**
     * Get virtual ticket statistics for a draw
     */
    public function getVirtualTicketStats($drawId)
    {
        $virtualTickets = LotteryTicket::where('lottery_draw_id', $drawId)
            ->where('is_virtual', true);
            
        $realTickets = LotteryTicket::where('lottery_draw_id', $drawId)
            ->where('is_virtual', false);
            
        $virtualCount = $virtualTickets->count();
        $realCount = $realTickets->count();
        $totalCount = $virtualCount + $realCount;
        
        $virtualPercentage = $totalCount > 0 ? round(($virtualCount / $totalCount) * 100, 2) : 0;
        
        return [
            'virtual_count' => $virtualCount,
            'real_count' => $realCount,
            'total_count' => $totalCount,
            'virtual_percentage' => $virtualPercentage
        ];
    }
    
    /**
     * Auto-generate virtual tickets when real tickets are purchased
     */
    public function autoGenerateOnPurchase($drawId)
    {
        $settings = LotterySetting::getSettings();
        
        if (!$settings->enable_virtual_tickets || !$settings->auto_generate_virtual) {
            return null;
        }
        
        // Get current real ticket count
        $realTicketCount = LotteryTicket::where('lottery_draw_id', $drawId)
            ->where('is_virtual', false)
            ->count();
            
        // Only auto-generate if we have enough real tickets to justify it
        if ($realTicketCount >= 5) {
            return $this->generateVirtualTickets($drawId, $realTicketCount);
        }
        
        return null;
    }
    
    /**
     * Generate virtual ticket number in legacy format (XXXX-XXXX-XXXX-XXXX)
     */
    private function generateLegacyTicketNumber()
    {
        do {
            // Generate each component independently
            $component1 = strtoupper(bin2hex(random_bytes(2))); // 4 chars
            $component2 = strtoupper(bin2hex(random_bytes(2))); // 4 chars
            $component3 = strtoupper(bin2hex(random_bytes(2))); // 4 chars
            $component4 = strtoupper(bin2hex(random_bytes(2))); // 4 chars
            
            // Create ticket format
            $ticketNumber = sprintf(
                '%s-%s-%s-%s',
                $component1,
                $component2, 
                $component3,
                $component4
            );
            
            // Check for uniqueness in database (check base format without suffix)
            $exists = LotteryTicket::where('ticket_number', 'LIKE', $ticketNumber . '%')->exists();
            
        } while ($exists);
        
        return $ticketNumber;
    }
    
    /**
     * Mark a ticket as used (for token usage)
     */
    public function markTicketAsUsed($ticketId, $planId = null)
    {
        $ticket = LotteryTicket::find($ticketId);
        if (!$ticket) {
            throw new \Exception("Ticket not found");
        }
        
        $updateData = [
            'used_as_token_at' => now(),
            'updated_at' => now()
        ];
        
        if ($planId) {
            $updateData['used_for_plan_id'] = $planId;
        }
        
        // Keep status as 'active' since 'used' is not a valid enum value
        // The used_as_token_at timestamp indicates the ticket was used
        
        $ticket->update($updateData);
        
        return $ticket;
    }
    
    /**
     * Mark a ticket as winner and set prize amount
     */
    public function markTicketAsWinner($ticketId, $prizeAmount)
    {
        $ticket = LotteryTicket::find($ticketId);
        if (!$ticket) {
            throw new \Exception("Ticket not found");
        }
        
        $ticket->update([
            'status' => 'winner',
            'prize_amount' => $prizeAmount,
            'updated_at' => now()
        ]);
        
        return $ticket;
    }
    
    /**
     * Mark a prize as claimed
     */
    public function claimPrize($ticketId)
    {
        $ticket = LotteryTicket::find($ticketId);
        if (!$ticket) {
            throw new \Exception("Ticket not found");
        }
        
        if ($ticket->status !== 'winner') {
            throw new \Exception("Only winning tickets can have prizes claimed");
        }
        
        $ticket->update([
            'status' => 'claimed',
            'claimed_at' => now(),
            'updated_at' => now()
        ]);
        
        return $ticket;
    }
    
    /**
     * Transfer a ticket to another user
     */
    public function transferTicket($ticketId, $newOwnerId)
    {
        $ticket = LotteryTicket::find($ticketId);
        if (!$ticket) {
            throw new \Exception("Ticket not found");
        }
        
        if (!$ticket->is_transferable) {
            throw new \Exception("This ticket is not transferable");
        }
        
        if ($ticket->is_virtual) {
            throw new \Exception("Virtual tickets cannot be transferred");
        }
        
        $ticket->update([
            'current_owner_id' => $newOwnerId,
            'transfer_count' => $ticket->transfer_count + 1,
            'last_transferred_at' => now(),
            'updated_at' => now()
        ]);
        
        return $ticket;
    }
    
    /**
     * Invalidate a ticket
     */
    public function invalidateTicket($ticketId, $reason = null)
    {
        $ticket = LotteryTicket::find($ticketId);
        if (!$ticket) {
            throw new \Exception("Ticket not found");
        }
        
        $ticket->update([
            'is_invalidated' => true,
            'invalidated_at' => now(),
            'invalidation_reason' => $reason,
            'status' => 'expired',
            'updated_at' => now()
        ]);
        
        return $ticket;
    }
    
    /**
     * Set ticket expiration time
     */
    public function setTicketExpiration($ticketId, $expiresAt)
    {
        $ticket = LotteryTicket::find($ticketId);
        if (!$ticket) {
            throw new \Exception("Ticket not found");
        }
        
        $ticket->update([
            'token_expires_at' => $expiresAt,
            'updated_at' => now()
        ]);
        
        return $ticket;
    }
    
    /**
     * Get ticket lifecycle timeline
     */
    public function getTicketTimeline($ticketId)
    {
        $ticket = LotteryTicket::find($ticketId);
        if (!$ticket) {
            throw new \Exception("Ticket not found");
        }
        
        $timeline = [];
        
        if ($ticket->created_at) {
            $timeline[] = [
                'event' => 'created',
                'timestamp' => $ticket->created_at,
                'description' => 'Ticket record created'
            ];
        }
        
        if ($ticket->purchased_at) {
            $timeline[] = [
                'event' => 'purchased',
                'timestamp' => $ticket->purchased_at,
                'description' => 'Ticket purchased by user'
            ];
        }
        
        if ($ticket->last_transferred_at) {
            $timeline[] = [
                'event' => 'transferred',
                'timestamp' => $ticket->last_transferred_at,
                'description' => "Ticket transferred (total transfers: {$ticket->transfer_count})"
            ];
        }
        
        if ($ticket->used_as_token_at) {
            $timeline[] = [
                'event' => 'used_as_token',
                'timestamp' => $ticket->used_as_token_at,
                'description' => 'Ticket used as token'
            ];
        }
        
        if ($ticket->status === 'winner' && $ticket->prize_amount > 0) {
            $timeline[] = [
                'event' => 'won_prize',
                'timestamp' => $ticket->updated_at, // Approximation since we don't have won_at
                'description' => "Won prize of $" . number_format($ticket->prize_amount, 2)
            ];
        }
        
        if ($ticket->claimed_at) {
            $timeline[] = [
                'event' => 'prize_claimed',
                'timestamp' => $ticket->claimed_at,
                'description' => 'Prize claimed'
            ];
        }
        
        if ($ticket->invalidated_at) {
            $timeline[] = [
                'event' => 'invalidated',
                'timestamp' => $ticket->invalidated_at,
                'description' => 'Ticket invalidated' . ($ticket->invalidation_reason ? ": {$ticket->invalidation_reason}" : '')
            ];
        }
        
        if ($ticket->token_expires_at) {
            $timeline[] = [
                'event' => 'expires',
                'timestamp' => $ticket->token_expires_at,
                'description' => 'Token expires',
                'future' => $ticket->token_expires_at > now()
            ];
        }
        
        // Sort by timestamp
        usort($timeline, function($a, $b) {
            return strtotime($a['timestamp']) - strtotime($b['timestamp']);
        });
        
        return $timeline;
    }
    
    /**
     * Update ticket information comprehensively
     */
    public function updateTicketInfo($ticketId, $updateData = [])
    {
        $ticket = LotteryTicket::find($ticketId);
        if (!$ticket) {
            throw new \Exception("Ticket not found");
        }
        
        // Always update the updated_at timestamp
        $updateData['updated_at'] = now();
        
        // Log the update for debugging
        Log::info("Updating ticket {$ticketId}", [
            'ticket_number' => $ticket->ticket_number,
            'old_data' => $ticket->toArray(),
            'update_data' => $updateData
        ]);
        
        $ticket->update($updateData);
        
        return $ticket->fresh(); // Return fresh instance from database
    }
    
    /**
     * Force refresh ticket data from database
     */
    public function refreshTicketData($ticketId)
    {
        $ticket = LotteryTicket::find($ticketId);
        if (!$ticket) {
            throw new \Exception("Ticket not found");
        }
        
        // Touch the record to update the timestamp
        $ticket->touch();
        
        return $ticket->fresh();
    }
    
    /**
     * Get comprehensive ticket status
     */
    public function getTicketStatus($ticketNumber)
    {
        $ticket = LotteryTicket::where('ticket_number', $ticketNumber)->first();
        
        if (!$ticket) {
            return [
                'found' => false,
                'message' => 'Ticket not found'
            ];
        }
        
        return [
            'found' => true,
            'ticket' => $ticket,
            'timeline' => $this->getTicketTimeline($ticket->id),
            'status_info' => [
                'is_active' => $ticket->status === 'active',
                'is_expired' => $ticket->status === 'expired',
                'is_winner' => $ticket->status === 'winner',
                'is_claimed' => $ticket->status === 'claimed',
                'has_been_used' => !is_null($ticket->used_as_token_at),
                'has_been_transferred' => $ticket->transfer_count > 0,
                'is_invalidated' => $ticket->is_invalidated,
                'expires_soon' => $ticket->token_expires_at && $ticket->token_expires_at <= now()->addDays(7),
                'last_activity' => $ticket->updated_at
            ]
        ];
    }
    
    /**
     * Batch update multiple tickets
     */
    public function batchUpdateTickets($ticketIds, $updateData)
    {
        $updateData['updated_at'] = now();
        
        $updated = LotteryTicket::whereIn('id', $ticketIds)->update($updateData);
        
        Log::info("Batch updated {$updated} tickets", [
            'ticket_ids' => $ticketIds,
            'update_data' => $updateData
        ]);
        
        return $updated;
    }
    
    /**
     * Sync ticket data - ensure all required fields are properly set
     */
    public function syncTicketData($ticketId)
    {
        $ticket = LotteryTicket::find($ticketId);
        if (!$ticket) {
            throw new \Exception("Ticket not found");
        }
        
        $updates = [];
        
        // Ensure ownership fields are set
        if (!$ticket->current_owner_id) {
            $updates['current_owner_id'] = $ticket->user_id;
        }
        
        if (!$ticket->original_owner_id) {
            $updates['original_owner_id'] = $ticket->user_id;
        }
        
        // Ensure timestamps are set
        if (!$ticket->purchased_at) {
            $updates['purchased_at'] = $ticket->created_at ?? now();
        }
        
        // Ensure token fields are consistent
        if ($ticket->is_valid_token && !$ticket->token_type) {
            $updates['token_type'] = 'lottery';
        }
        
        // Ensure financial fields are set
        if (!$ticket->ticket_price || $ticket->ticket_price <= 0) {
            $updates['ticket_price'] = 2.00; // Default price
        }
        
        // Ensure payment method is set
        if (!$ticket->payment_method) {
            $updates['payment_method'] = $ticket->is_virtual ? 'virtual' : 'balance';
        }
        
        // Ensure transaction reference is set
        if (!$ticket->transaction_reference) {
            $prefix = $ticket->is_virtual ? 'VIRTUAL_' : 'MANUAL_';
            $updates['transaction_reference'] = $prefix . uniqid();
        }
        
        // Always update the timestamp to track when sync was performed
        $updates['updated_at'] = now();
        
        if (!empty($updates)) {
            $ticket->update($updates);
            
            Log::info("Synced ticket data", [
                'ticket_id' => $ticketId,
                'ticket_number' => $ticket->ticket_number,
                'updates' => $updates
            ]);
        }
        
        return $ticket->fresh();
    }
    
    /**
     * Force update specific ticket by ticket number
     */
    public function forceUpdateTicketByNumber($ticketNumber, $updateData = [])
    {
        $ticket = LotteryTicket::where('ticket_number', $ticketNumber)->first();
        if (!$ticket) {
            throw new \Exception("Ticket {$ticketNumber} not found");
        }
        
        // Always include updated timestamp
        $updateData['updated_at'] = now();
        
        // Log the forced update
        Log::info("Force updating ticket by number", [
            'ticket_number' => $ticketNumber,
            'ticket_id' => $ticket->id,
            'update_data' => $updateData
        ]);
        
        $ticket->update($updateData);
        
        return $ticket->fresh();
    }
    
    /**
     * Validate and fix ticket data integrity
     */
    public function validateTicketIntegrity($ticketId)
    {
        $ticket = LotteryTicket::find($ticketId);
        if (!$ticket) {
            throw new \Exception("Ticket not found");
        }
        
        $issues = [];
        $fixes = [];
        
        // Check ownership consistency
        if ($ticket->current_owner_id != $ticket->user_id && $ticket->transfer_count == 0) {
            $issues[] = "Current owner doesn't match user ID but no transfers recorded";
            $fixes['current_owner_id'] = $ticket->user_id;
        }
        
        // Check timestamp consistency
        if ($ticket->purchased_at && $ticket->created_at && $ticket->purchased_at < $ticket->created_at) {
            $issues[] = "Purchase time is before creation time";
            $fixes['purchased_at'] = $ticket->created_at;
        }
        
        // Check token expiration logic
        if ($ticket->token_expires_at && $ticket->token_expires_at < $ticket->created_at) {
            $issues[] = "Token expires before creation";
            $fixes['token_expires_at'] = null;
        }
        
        // Check financial consistency
        if ($ticket->prize_amount > 0 && $ticket->status != 'winner' && $ticket->status != 'claimed') {
            $issues[] = "Has prize amount but status is not winner/claimed";
            $fixes['status'] = 'winner';
        }
        
        // Check virtual ticket consistency
        if ($ticket->is_virtual && !$ticket->virtual_user_type) {
            $issues[] = "Virtual ticket missing virtual_user_type";
            $fixes['virtual_user_type'] = 'system_generated';
        }
        
        // Apply fixes if any issues found
        if (!empty($fixes)) {
            $fixes['updated_at'] = now();
            $ticket->update($fixes);
            
            Log::warning("Fixed ticket integrity issues", [
                'ticket_id' => $ticketId,
                'ticket_number' => $ticket->ticket_number,
                'issues' => $issues,
                'fixes' => $fixes
            ]);
        }
        
        return [
            'ticket' => $ticket->fresh(),
            'issues_found' => $issues,
            'fixes_applied' => $fixes,
            'is_valid' => empty($issues)
        ];
    }
}
