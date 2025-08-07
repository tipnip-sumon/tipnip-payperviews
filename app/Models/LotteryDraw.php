<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LotteryDraw extends Model
{
    use HasFactory;

    protected $fillable = [
        'draw_number',
        'draw_date',
        'draw_time',
        'status',
        'drawn_at',
        'optimized_at',
        'cleanup_performed',
        'total_prize_pool',
        'total_tickets_sold',
        'virtual_tickets_sold',
        'display_tickets_sold',
        'manual_winner_selection_enabled',
        'manually_selected_winners',
        'winners_selected_manually',
        'prize_distribution_type',
        'allow_multiple_winners_per_place',
        'prize_distribution',
        'winning_numbers',
        'max_tickets',
        'ticket_price',
        'admin_commission_percentage',
        'auto_draw',
        'auto_prize_distribution',
    ];

    protected $casts = [
        'draw_date' => 'date',
        'draw_time' => 'datetime',
        'drawn_at' => 'datetime',
        'optimized_at' => 'datetime',
        'cleanup_performed' => 'boolean',
        'prize_distribution' => 'array',
        'winning_numbers' => 'array',
        'manually_selected_winners' => 'array',
        'manual_winner_selection_enabled' => 'boolean',
        'allow_multiple_winners_per_place' => 'boolean',
        'total_prize_pool' => 'decimal:2',
        'ticket_price' => 'decimal:2',
        'admin_commission_percentage' => 'decimal:2',
        'auto_draw' => 'boolean',
        'auto_prize_distribution' => 'boolean',
    ];

    /**
     * Get tickets for this draw
     */
    public function tickets()
    {
        return $this->hasMany(LotteryTicket::class);
    }

    /**
     * Get winners for this draw
     */
    public function winners()
    {
        return $this->hasMany(LotteryWinner::class);
    }

    /**
     * Get first prize winner
     */
    public function firstPrizeWinner()
    {
        return $this->hasOne(LotteryWinner::class)->where('prize_position', 1);
    }

    /**
     * Get second prize winner
     */
    public function secondPrizeWinner()
    {
        return $this->hasOne(LotteryWinner::class)->where('prize_position', 2);
    }

    /**
     * Get third prize winner
     */
    public function thirdPrizeWinner()
    {
        return $this->hasOne(LotteryWinner::class)->where('prize_position', 3);
    }

    /**
     * Get the current active draw
     */
    public static function getCurrentDraw()
    {
        $nextSunday = Carbon::now()->next(Carbon::SUNDAY);
        
        return self::firstOrCreate([
            'draw_date' => $nextSunday->toDateString(),
            'status' => 'pending'
        ], [
            'draw_number' => 'DRAW_' . $nextSunday->format('Y_W'),
            'draw_time' => $nextSunday->setTime(20, 0, 0),
        ]);
    }

    /**
     * Generate draw number
     */
    public static function generateDrawNumber($date = null)
    {
        $date = $date ? Carbon::parse($date) : Carbon::now();
        return 'DRAW_' . $date->format('Y_W');
    }

    /**
     * Check if draw is ready
     */
    public function isReadyForDraw()
    {
        $settings = LotterySetting::getSettings();
        return $this->total_tickets_sold >= $settings->min_tickets_for_draw;
    }

    /**
     * Calculate prize pool
     */
    public function calculatePrizePool()
    {
        $settings = LotterySetting::getSettings();
        $totalRevenue = $this->total_tickets_sold * $settings->ticket_price;
        $adminCommission = $totalRevenue * ($settings->admin_commission_percentage / 100);
        return $totalRevenue - $adminCommission;
    }

    /**
     * Update totals
     */
    public function updateTotals()
    {
        $this->total_tickets_sold = $this->tickets()->count();
        $this->total_prize_pool = $this->calculatePrizePool();
        $this->save();
    }

    /**
     * Perform the draw
     */
    public function performDraw()
    {
        if ($this->status !== 'pending') {
            throw new \Exception('Draw has already been performed or is not ready.');
        }

        if (!$this->isReadyForDraw()) {
            throw new \Exception('Not enough tickets sold for draw.');
        }

        // Get all active tickets for this draw
        $tickets = $this->tickets()->where('status', 'active')->get();
        
        if ($tickets->isEmpty()) {
            throw new \Exception('No active tickets for draw.');
        }

        // Shuffle and select winners
        $shuffledTickets = $tickets->shuffle();
        $settings = LotterySetting::getSettings();
        $prizeStructure = $settings->prize_structure ?? $this->getDefaultPrizeStructure();
        
        // Ensure prize structure is an array (handle JSON string case)
        if (is_string($prizeStructure)) {
            $prizeStructure = json_decode($prizeStructure, true) ?? $this->getDefaultPrizeStructure();
        }
        
        if (!is_array($prizeStructure)) {
            $prizeStructure = $this->getDefaultPrizeStructure();
        }
        
        // Log prize structure for debugging
        Log::info("LotteryDraw performDraw: Prize structure", [
            'draw_id' => $this->id,
            'prize_structure' => $prizeStructure,
            'prize_structure_type' => gettype($prizeStructure)
        ]);
        
        $winners = [];
        $winnerTickets = [];
        
        foreach ($prizeStructure as $position => $prizeData) {
            // Determine how many winners for this position
            $numWinners = 1;
            if (isset($prizeData['multiple_winners']) && is_array($prizeData['multiple_winners'])) {
                $numWinners = count($prizeData['multiple_winners']);
            }
            
            // Check if we have enough tickets for all winners in this position
            if (count($shuffledTickets) >= ($numWinners)) {
                for ($i = 0; $i < $numWinners; $i++) {
                    if (!empty($shuffledTickets)) {
                        $winningTicket = $shuffledTickets->shift(); // Remove from collection
                        
                        // Calculate prize amount based on structure type
                        if (isset($prizeData['type']) && $prizeData['type'] === 'fixed_amount') {
                            if (isset($prizeData['multiple_winners'][$i]['amount'])) {
                                $prizeAmount = (float) $prizeData['multiple_winners'][$i]['amount'];
                            } else {
                                $prizeAmount = (float) ($prizeData['amount'] ?? 0);
                            }
                        } else {
                            // Fallback to percentage-based calculation
                            $percentage = $prizeData['percentage'] ?? 0;
                            $prizeAmount = $this->calculatePrizeAmount($percentage);
                        }
                        
                        // Create winner record
                        LotteryWinner::create([
                            'lottery_draw_id' => $this->id,
                            'lottery_ticket_id' => $winningTicket->id,
                            'user_id' => $winningTicket->user_id,
                            'prize_position' => $position,
                            'prize_name' => $prizeData['name'],
                            'prize_amount' => $prizeAmount,
                        ]);
                        
                        // Update ticket status
                        $winningTicket->update([
                            'status' => 'winner',
                            'prize_amount' => $prizeAmount
                        ]);
                        
                        $winners[] = $winningTicket->id;
                        $winnerTickets[] = $winningTicket;
                    }
                }
            }
        }

        // Update draw status
        $this->update([
            'status' => 'drawn',
            'winning_numbers' => $winners,
            'prize_distribution' => $prizeStructure
        ]);

        // Expire all non-winning tickets and process commission ticket refunds
        $losingTickets = $this->tickets()->where('status', 'active')->whereNotIn('id', $winners)->get();
        foreach ($losingTickets as $ticket) {
            $ticket->update(['status' => 'expired']);
            
            // If this is a commission ticket or special sponsor ticket, refund $1 to user's main wallet
            if ($ticket->payment_method === 'commission_reward' || $ticket->payment_method === 'sponsor_reward') {
                $this->processCommissionTicketRefund($ticket);
            }
        }

        // Auto-distribute prizes if enabled
        if ($settings->auto_prize_distribution) {
            $this->distributePrizes();
        }

        return $winnerTickets;
    }

    /**
     * Process $1 refund for losing commission and sponsor tickets
     */
    private function processCommissionTicketRefund($ticket)
    {
        $user = $ticket->user;
        $refundAmount = 1.00; // Fixed $1 refund for commission and sponsor tickets
        
        // Add $1 to user's main wallet (deposit_wallet)
        $user->deposit_wallet += $refundAmount;
        $user->save();
        
        // Determine ticket type for logging
        $ticketType = $ticket->payment_method === 'sponsor_reward' ? 'sponsor' : 'commission';
        $remarkType = $ticket->payment_method === 'sponsor_reward' ? 'sponsor_ticket_refund' : 'commission_ticket_refund';
        
        // Create transaction record for refund
        $transaction = new \App\Models\Transaction();
        $transaction->user_id = $user->id;
        $transaction->amount = $refundAmount;
        $transaction->post_balance = $user->deposit_wallet;
        $transaction->charge = 0;
        $transaction->trx_type = '+';
        $transaction->trx = strtoupper($ticketType) . '_REFUND_' . time() . '_' . $ticket->id;
        $transaction->wallet_type = 'deposit_wallet';
        $transaction->remark = $remarkType;
        $transaction->details = ucfirst($ticketType) . " ticket refund for non-winning ticket: {$ticket->ticket_number}";
        $transaction->save();
        
        // Log the refund
        Log::info(ucfirst($ticketType) . " ticket refund processed", [
            'ticket_id' => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
            'user_id' => $user->id,
            'refund_amount' => $refundAmount,
            'ticket_type' => $ticketType,
            'payment_method' => $ticket->payment_method,
            'transaction_id' => $transaction->id
        ]);
    }

    /**
     * Distribute prizes to winners
     */
    public function distributePrizes()
    {
        $winners = $this->winners()->where('claim_status', 'pending')->get();
        
        foreach ($winners as $winner) {
            // Add prize to user balance
            $user = $winner->user;
            $user->balance += $winner->prize_amount;
            $user->save();

            // Update winner status
            $winner->update([
                'claim_status' => 'claimed',
                'claimed_at' => now(),
                'claim_method' => 'auto'
            ]);

            // Create transaction record (if you have a transactions table)
            // Transaction::create([...]);
        }

        $this->update(['status' => 'completed']);
        
        // Auto-cleanup virtual lottery data after completion
        $this->cleanupVirtualData();
    }

    /**
     * Calculate prize amount based on percentage
     */
    private function calculatePrizeAmount($percentage)
    {
        return ($this->total_prize_pool * $percentage) / 100;
    }

    /**
     * Get default prize structure
     */
    private function getDefaultPrizeStructure()
    {
        return [
            1 => ['name' => 'First Prize', 'percentage' => 50],
            2 => ['name' => 'Second Prize', 'percentage' => 30],
            3 => ['name' => 'Third Prize', 'percentage' => 20],
        ];
    }

    /**
     * Get display ticket count (real + virtual)
     */
    public function getDisplayTicketCount()
    {
        return $this->display_tickets_sold ?: $this->total_tickets_sold;
    }

    /**
     * Update virtual ticket count based on settings
     */
    public function updateVirtualTicketCount()
    {
        $settings = LotterySetting::getSettings();
        $realTickets = $this->total_tickets_sold;
        
        if ($settings->show_virtual_tickets) {
            $virtualCount = ($realTickets * $settings->virtual_ticket_multiplier / 100) + $settings->virtual_ticket_base;
            $this->virtual_tickets_sold = max(0, $virtualCount - $realTickets);
            $this->display_tickets_sold = $realTickets + $this->virtual_tickets_sold;
        } else {
            $this->virtual_tickets_sold = 0;
            $this->display_tickets_sold = $realTickets;
        }
        
        $this->save();
    }

    /**
     * Check if manual winner selection is enabled for this draw
     */
    public function hasManualWinnerSelection()
    {
        return $this->manual_winner_selection_enabled;
    }

    /**
     * Set manually selected winners
     */
    public function setManualWinners($winners)
    {
        $this->manually_selected_winners = $winners;
        $this->save();
    }

    /**
     * Get manually selected winners
     */
    public function getManualWinners()
    {
        return $this->manually_selected_winners ?: [];
    }

    /**
     * Check if this draw allows multiple winners per place
     */
    public function allowsMultipleWinnersPerPlace()
    {
        return $this->allow_multiple_winners_per_place;
    }

    /**
     * Get prize structure with calculated amounts
     */
    public function getPrizeStructureWithAmounts()
    {
        $structure = $this->prize_distribution ?: $this->getDefaultPrizeStructure();
        
        if ($this->prize_distribution_type === 'fixed_amount') {
            return $structure; // Return as-is for fixed amounts
        }
        
        // Calculate amounts for percentage-based distribution
        foreach ($structure as $position => &$prize) {
            $prize['calculated_amount'] = $this->calculatePrizeAmount($prize['percentage']);
        }
        
        return $structure;
    }

    /**
     * Get total prize amount for fixed distribution
     */
    public function getTotalFixedPrizeAmount()
    {
        if ($this->prize_distribution_type !== 'fixed_amount') {
            return $this->total_prize_pool;
        }
        
        $total = 0;
        foreach ($this->prize_distribution as $prize) {
            $total += $prize['amount'] ?? 0;
        }
        
        return $total;
    }
    
    /**
     * Cleanup virtual lottery data after draw completion
     */
    public function cleanupVirtualData()
    {
        try {
            // Only clean if draw is completed and not already cleaned
            if ($this->status !== 'completed' || $this->cleanup_performed) {
                return false;
            }
            
            // Use the optimization service
            $optimizationService = new \App\Services\LotteryOptimizationService();
            $result = $optimizationService->cleanupVirtualLotteryData($this);
            
            if ($result['success']) {
                Log::info('Auto-cleanup completed for draw', [
                    'draw_id' => $this->id,
                    'draw_number' => $this->draw_number,
                    'virtual_tickets_deleted' => $result['stats']['virtual_tickets_deleted']
                ]);
                
                return true;
            }
            
            return false;
            
        } catch (\Exception $e) {
            Log::error('Error in auto-cleanup', [
                'draw_id' => $this->id,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Get display draw number starting from 50+
     * This ensures users see draw numbers starting from at least 50
     */
    public function getDisplayDrawNumberAttribute()
    {
        // Start display numbers from 50 and add the actual ID
        return 50 + $this->id;
    }

    /**
     * Get formatted display draw number with prefix
     */
    public function getFormattedDrawNumberAttribute()
    {
        return 'Draw #' . $this->display_draw_number;
    }

    /**
     * Get the actual total prize pool for this draw
     * For completed draws: uses actual prize amounts awarded
     * For pending draws: estimates based on settings and tickets sold
     */
    public function getActualPrizePoolAttribute()
    {
        if ($this->status == 'completed') {
            // For completed draws: use actual total prize amounts awarded to winners
            return $this->winners()->sum('prize_amount');
        } else {
            // For pending draws: estimate based on tickets sold and settings
            $settings = LotterySetting::getSettings();
            $totalTickets = $this->total_tickets_sold ?? 0;
            $totalRevenue = $totalTickets * ($settings->ticket_price ?? 2);
            $adminCommission = $totalRevenue * (($settings->admin_commission_percentage ?? 10) / 100);
            return max($totalRevenue - $adminCommission, 0);
        }
    }
}
