<?php

namespace App\Models;

// Enhanced LotteryDraw performDraw method
// This code shows how to handle remaining prizes after manual selection

class LotteryDrawEnhanced extends LotteryDraw
{
    /**
     * Enhanced performDraw method that preserves manual winners
     * and auto-fills remaining prize positions
     */
    public function performDrawEnhanced()
    {
        if ($this->status !== 'pending') {
            throw new \Exception('Draw has already been performed or is not ready.');
        }

        if (!$this->isReadyForDraw()) {
            throw new \Exception('Not enough tickets sold for draw.');
        }

        // Get existing manual winners (if any)
        $existingWinners = $this->winners()->where('is_manual_selection', true)->get();
        $manualPositions = $existingWinners->pluck('prize_position')->toArray();
        $manualTicketIds = $existingWinners->pluck('lottery_ticket_id')->toArray();

        Log::info("Draw {$this->id}: Found " . count($existingWinners) . " manual winners for positions: " . implode(', ', $manualPositions));

        // Get prize structure from settings
        $settings = LotterySetting::getSettings();
        $prizeStructure = $settings->prize_structure ?? $this->getDefaultPrizeStructure();
        
        if (is_string($prizeStructure)) {
            $prizeStructure = json_decode($prizeStructure, true) ?? [];
        }

        // Get all available tickets (excluding manual winner tickets and virtual tickets)
        $availableTickets = $this->tickets()
            ->where('status', 'active')
            ->where(function($query) {
                $query->where('is_virtual', false)->orWhereNull('is_virtual');
            })
            ->whereNotIn('id', $manualTicketIds)
            ->get()
            ->shuffle();

        Log::info("Draw {$this->id}: Found " . $availableTickets->count() . " available tickets for auto-selection");

        // Determine which positions need auto-selection
        $allPositions = array_keys($prizeStructure);
        $remainingPositions = array_diff($allPositions, $manualPositions);
        
        Log::info("Draw {$this->id}: Positions needing auto-selection: " . implode(', ', $remainingPositions));

        $newWinners = [];
        $winnerTickets = [];

        // Auto-select winners for remaining positions
        foreach ($remainingPositions as $position) {
            if (!isset($prizeStructure[$position])) {
                Log::warning("Draw {$this->id}: No prize structure found for position {$position}");
                continue;
            }

            $prizeData = $prizeStructure[$position];
            
            // Determine how many winners for this position
            $numWinners = 1;
            if (isset($prizeData['multiple_winners']) && is_array($prizeData['multiple_winners'])) {
                $numWinners = count($prizeData['multiple_winners']);
            }
            
            // Select winners for this position
            for ($i = 0; $i < $numWinners; $i++) {
                if ($availableTickets->isEmpty()) {
                    Log::warning("Draw {$this->id}: No more available tickets for position {$position}");
                    break;
                }

                $winningTicket = $availableTickets->shift(); // Remove from collection
                
                // Calculate prize amount
                if (isset($prizeData['type']) && $prizeData['type'] === 'fixed_amount') {
                    if (isset($prizeData['multiple_winners'][$i]['amount'])) {
                        $prizeAmount = (float) $prizeData['multiple_winners'][$i]['amount'];
                    } else {
                        $prizeAmount = (float) ($prizeData['amount'] ?? 0);
                    }
                } else {
                    // Percentage-based calculation
                    $percentage = $prizeData['percentage'] ?? 0;
                    $prizeAmount = $this->calculatePrizeAmount($percentage);
                }
                
                // Create winner record for auto-selected winner
                $winner = LotteryWinner::create([
                    'lottery_draw_id' => $this->id,
                    'lottery_ticket_id' => $winningTicket->id,
                    'user_id' => $winningTicket->user_id,
                    'prize_position' => $position,
                    'prize_name' => $prizeData['name'] ?? "Position {$position}",
                    'prize_amount' => $prizeAmount,
                    'claim_status' => 'pending',
                    'is_manual_selection' => false, // This is auto-selected
                    'selected_at' => now()
                ]);
                
                // Update ticket status
                $winningTicket->update([
                    'status' => 'winner',
                    'prize_amount' => $prizeAmount
                ]);
                
                $newWinners[] = $winningTicket->id;
                $winnerTickets[] = $winningTicket;
                
                Log::info("Draw {$this->id}: Auto-selected ticket {$winningTicket->ticket_number} for position {$position} with prize ${$prizeAmount}");
            }
        }

        // Get all winner ticket IDs (manual + auto)
        $allWinnerIds = array_merge($manualTicketIds, $newWinners);

        // Update draw status
        $this->update([
            'status' => 'drawn',
            'winning_numbers' => $allWinnerIds,
            'prize_distribution' => $prizeStructure,
            'has_mixed_selection' => count($manualPositions) > 0 && count($remainingPositions) > 0
        ]);

        // Update all non-winning tickets to 'lost' status
        $losingTickets = $this->tickets()
            ->where('status', 'active')
            ->whereNotIn('id', $allWinnerIds)
            ->get();
            
        foreach ($losingTickets as $ticket) {
            $ticket->update(['status' => 'lost']);
            
            // Handle commission and sponsor ticket refunds if applicable
            if ($ticket->payment_method === 'commission_reward' || $ticket->payment_method === 'sponsor_reward') {
                $this->processCommissionTicketRefund($ticket);
            }
        }

        // Auto-distribute prizes if enabled
        if ($settings->auto_prize_distribution) {
            $this->distributePrizes();
        }

        // Return summary
        $totalWinners = count($existingWinners) + count($newWinners);
        $summary = [
            'manual_winners' => count($existingWinners),
            'auto_winners' => count($newWinners),
            'total_winners' => $totalWinners,
            'manual_positions' => $manualPositions,
            'auto_positions' => $remainingPositions
        ];

        Log::info("Draw {$this->id} completed with mixed selection: " . json_encode($summary));

        return $summary;
    }

    /**
     * Check if draw can be completed (has tickets available for remaining positions)
     */
    public function canCompleteDrawWithRemainingPositions()
    {
        $existingWinners = $this->winners()->where('is_manual_selection', true)->get();
        $manualTicketIds = $existingWinners->pluck('lottery_ticket_id')->toArray();
        
        $availableTickets = $this->tickets()
            ->where('status', 'active')
            ->where(function($query) {
                $query->where('is_virtual', false)->orWhereNull('is_virtual');
            })
            ->whereNotIn('id', $manualTicketIds)
            ->count();

        $settings = LotterySetting::getSettings();
        $prizeStructure = $settings->prize_structure ?? [];
        
        if (is_string($prizeStructure)) {
            $prizeStructure = json_decode($prizeStructure, true) ?? [];
        }

        $totalPositions = count($prizeStructure);
        $manualPositions = $existingWinners->count();
        $remainingPositions = $totalPositions - $manualPositions;

        return [
            'can_complete' => $availableTickets >= $remainingPositions,
            'available_tickets' => $availableTickets,
            'remaining_positions' => $remainingPositions,
            'total_positions' => $totalPositions,
            'manual_selections' => $manualPositions
        ];
    }

    /**
     * Get summary of draw completion status
     */
    public function getDrawCompletionSummary()
    {
        $existingWinners = $this->winners()->get();
        $manualWinners = $existingWinners->where('is_manual_selection', true);
        $autoWinners = $existingWinners->where('is_manual_selection', false);

        $settings = LotterySetting::getSettings();
        $prizeStructure = $settings->prize_structure ?? [];
        
        if (is_string($prizeStructure)) {
            $prizeStructure = json_decode($prizeStructure, true) ?? [];
        }

        return [
            'total_prize_positions' => count($prizeStructure),
            'filled_positions' => $existingWinners->count(),
            'manual_winners' => $manualWinners->count(),
            'auto_winners' => $autoWinners->count(),
            'remaining_positions' => count($prizeStructure) - $existingWinners->count(),
            'is_complete' => $existingWinners->count() >= count($prizeStructure),
            'has_mixed_selection' => $manualWinners->count() > 0 && $autoWinners->count() > 0
        ];
    }
}
