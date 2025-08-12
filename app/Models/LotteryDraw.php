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
     * Boot method to set default values
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($draw) {
            // Set default prize distribution if not already set
            if (empty($draw->prize_distribution)) {
                $draw->prize_distribution = [
                    "1" => [
                        "name" => "1st Prize",
                        "type" => "fixed_amount",
                        "amount" => "1000"
                    ],
                    "2" => [
                        "name" => "2nd Prize", 
                        "type" => "fixed_amount",
                        "amount" => "300"
                    ],
                    "3" => [
                        "name" => "3rd Prize",
                        "type" => "fixed_amount", 
                        "amount" => "100"
                    ]
                ];
            }
        });
    }

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
        // Calculate total prize pool from prize_distribution amounts
        if (!empty($this->prize_distribution) && is_array($this->prize_distribution)) {
            $total = 0;
            foreach ($this->prize_distribution as $prize) {
                if (isset($prize['type']) && $prize['type'] === 'fixed_amount' && isset($prize['amount'])) {
                    $total += floatval($prize['amount']);
                }
            }
            return $total;
        }
        
        // Fallback to default prize distribution if not set
        return 1400.00; // Default: 1000 + 300 + 100
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
                        
                        // Send lottery win notification
                        try {
                            notifyLotteryWin($winningTicket->user_id, $prizeAmount, $position, $winningTicket->ticket_number);
                        } catch (\Exception $e) {
                            Log::error("Failed to send lottery win notification: " . $e->getMessage());
                        }
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

        // Auto-create next draw if this is an auto draw and auto_generate_draws is enabled
        if ($this->auto_draw && $settings->auto_generate_draws) {
            $this->createNextAutoDraw($settings);
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
        
        // Send refund notification for sponsor tickets
        if ($ticket->payment_method === 'sponsor_reward') {
            try {
                notifySponsorTicketRefund($user->id, $refundAmount, $ticket->ticket_number);
            } catch (\Exception $e) {
                Log::error("Failed to send sponsor ticket refund notification: " . $e->getMessage());
            }
        }
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
        
        // Auto-adjust active tickets boost after draw completion
        $this->autoAdjustActiveTicketsBoost();
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
     * Get total prize pool calculated from prize distribution
     */
    public function getTotalPrizePoolAttribute()
    {
        return $this->calculatePrizePool();
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

    /**
     * Create next automatic draw after current draw completion
     */
    private function createNextAutoDraw($settings)
    {
        try {
            // Calculate next draw time based on draw frequency
            $nextDrawTime = $this->calculateNextDrawTime($settings);
            $drawNumber = 'AUTO_' . $nextDrawTime->format('Y_m_d_H_i');
            
            // Check if a draw already exists for this time
            $existingDraw = LotteryDraw::where('draw_number', $drawNumber)
                                     ->orWhere('draw_time', $nextDrawTime)
                                     ->first();
            
            if (!$existingDraw) {
                $newDraw = self::create([
                    'draw_number' => $drawNumber,
                    'draw_date' => $nextDrawTime->toDateString(),
                    'draw_time' => $nextDrawTime,
                    'status' => 'pending',
                    'auto_draw' => true,
                    'auto_prize_distribution' => true,
                    'manual_winner_selection_enabled' => false,
                    'ticket_price' => $settings->ticket_price ?? 2.00,
                    'admin_commission_percentage' => $settings->admin_commission_percentage ?? 10,
                ]);
                
                Log::info("Next automatic draw created: {$drawNumber} for {$nextDrawTime->format('Y-m-d H:i:s')}", [
                    'new_draw_id' => $newDraw->id,
                    'triggered_by_draw' => $this->id
                ]);
                
                return $newDraw;
            } else {
                Log::info("Next draw already exists: {$existingDraw->draw_number}");
                return $existingDraw;
            }
        } catch (\Exception $e) {
            Log::error("Failed to create next auto draw: " . $e->getMessage(), [
                'current_draw_id' => $this->id,
                'error' => $e->getTraceAsString()
            ]);
        }
        
        return null;
    }

    /**
     * Calculate next draw time based on settings
     */
    private function calculateNextDrawTime($settings)
    {
        $frequency = $settings->auto_generation_frequency ?? 'weekly';
        $drawTime = $settings->draw_time ?? '20:00';
        $drawDay = $settings->draw_day ?? 'sunday';
        
        // Parse the draw time
        $timeParts = explode(':', $drawTime);
        $hour = (int) ($timeParts[0] ?? 20);
        $minute = (int) ($timeParts[1] ?? 0);
        
        $nextDrawTime = Carbon::now();
        
        switch ($frequency) {
            case 'daily':
                $nextDrawTime = $nextDrawTime->addDay()->setTime($hour, $minute, 0);
                break;
                
            case 'weekly':
                // Default to next week same day/time
                $nextDrawTime = $nextDrawTime->addWeek()->setTime($hour, $minute, 0);
                
                // If specific day is set, adjust to that day
                if ($drawDay && $drawDay !== 'current') {
                    $dayMap = [
                        'monday' => Carbon::MONDAY,
                        'tuesday' => Carbon::TUESDAY, 
                        'wednesday' => Carbon::WEDNESDAY,
                        'thursday' => Carbon::THURSDAY,
                        'friday' => Carbon::FRIDAY,
                        'saturday' => Carbon::SATURDAY,
                        'sunday' => Carbon::SUNDAY,
                    ];
                    
                    if (isset($dayMap[strtolower($drawDay)])) {
                        $nextDrawTime = $nextDrawTime->next($dayMap[strtolower($drawDay)])->setTime($hour, $minute, 0);
                    }
                }
                break;
                
            case 'monthly':
                $nextDrawTime = $nextDrawTime->addMonth()->setTime($hour, $minute, 0);
                break;
                
            case 'hourly':
                $nextDrawTime = $nextDrawTime->addHour();
                break;
                
            case 'every_3_hours':
                $nextDrawTime = $nextDrawTime->addHours(3);
                break;
                
            case 'every_6_hours':
                $nextDrawTime = $nextDrawTime->addHours(6);
                break;
                
            case 'every_12_hours':
                $nextDrawTime = $nextDrawTime->addHours(12);
                break;
                
            default:
                // Default to weekly on the same day at the specified time
                $nextDrawTime = $nextDrawTime->addWeek()->setTime($hour, $minute, 0);
        }
        
        return $nextDrawTime;
    }

    /**
     * Auto-adjust active tickets boost after draw completion
     * Reduces boost by the number of tickets that were active in this draw
     */
    private function autoAdjustActiveTicketsBoost()
    {
        try {
            $settings = LotterySetting::getSettings();
            
            // Check if auto-adjustment is enabled
            if (!($settings->auto_adjust_boost ?? true)) {
                return;
            }
            
            // Get the number of tickets that were active in this draw
            $ticketsInThisDraw = $this->tickets()->where('status', 'active')->count();
            
            if ($ticketsInThisDraw > 0) {
                $currentBoost = $settings->active_tickets_boost ?? 0;
                
                // Reduce boost by the number of tickets that are no longer active
                $newBoost = max(0, $currentBoost - $ticketsInThisDraw);
                
                LotterySetting::updateSettings(['active_tickets_boost' => $newBoost]);
                
                Log::info("Auto-adjusted active tickets boost after draw completion", [
                    'draw_id' => $this->id,
                    'tickets_completed' => $ticketsInThisDraw,
                    'old_boost' => $currentBoost,
                    'new_boost' => $newBoost,
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Failed to auto-adjust active tickets boost", [
                'draw_id' => $this->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
