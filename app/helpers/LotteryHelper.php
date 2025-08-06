<?php

use App\Models\LotteryDraw;
use App\Models\LotteryTicket;
use App\Models\LotteryWinner;
use App\Models\SpecialLotteryTicket;
use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * Lottery Helper Functions
 * Specialized functions for lottery management and operations
 */

if (!function_exists('purchaseLotteryTicket')) {
    /**
     * Purchase lottery ticket for user
     */
    function purchaseLotteryTicket($userId, $drawId, $ticketNumbers = null, $ticketType = 'regular')
    {
        try {
            $draw = LotteryDraw::find($drawId);
            if (!$draw) {
                throw new \Exception('Lottery draw not found');
            }

            if ($draw->status !== 'active') {
                throw new \Exception('Lottery draw is not active');
            }

            $user = User::find($userId);
            if (!$user) {
                throw new \Exception('User not found');
            }

            $ticketPrice = $ticketType === 'special' ? $draw->special_ticket_price : $draw->ticket_price;
            
            if ($user->balance < $ticketPrice) {
                throw new \Exception('Insufficient balance');
            }

            // Generate ticket numbers if not provided
            if (!$ticketNumbers) {
                $ticketNumbers = generateLotteryNumbers($draw->number_range, $draw->numbers_per_ticket);
            }

            // Create ticket - always use unified lottery_tickets table
            $ticket = LotteryTicket::create([
                'user_id' => $userId,
                'lottery_draw_id' => $drawId, // Use correct column name
                'ticket_numbers' => json_encode($ticketNumbers),
                'ticket_price' => $ticketPrice,
                'status' => 'active',
                'token_type' => $ticketType, // Set the ticket type (special, lottery, etc.)
                'purchased_at' => now(),
                'current_owner_id' => $userId,
                'original_owner_id' => $userId,
                'ticket_number' => \App\Services\HexadecimalTicketService::generateTicketNumber(),
                'is_valid_token' => $ticketType === 'special' ? true : false,
                'is_transferable' => true,
            ]);

            // Debit user balance
            debitUser($userId, $ticketPrice, 'Lottery Ticket Purchase', [
                'ticket_id' => $ticket->id, 
                'draw_id' => $drawId,
                'ticket_type' => $ticketType,
                'ticket_numbers' => $ticketNumbers
            ]);

            // Send notification
            if (function_exists('notifyUserLottery')) {
                notifyUserLottery($userId, 
                    "Lottery ticket purchased successfully! Your numbers: " . implode(', ', $ticketNumbers),
                    'purchase',
                    [
                        'ticket_id' => $ticket->id,
                        'draw_id' => $drawId,
                        'numbers' => $ticketNumbers,
                        'price' => $ticketPrice
                    ]
                );
            }

            return $ticket;
        } catch (\Exception $e) {
            Log::error('Failed to purchase lottery ticket: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('generateLotteryNumbers')) {
    /**
     * Generate random lottery numbers
     */
    function generateLotteryNumbers($range = 50, $count = 6)
    {
        try {
            $numbers = [];
            while (count($numbers) < $count) {
                $number = rand(1, $range);
                if (!in_array($number, $numbers)) {
                    $numbers[] = $number;
                }
            }
            sort($numbers);
            return $numbers;
        } catch (\Exception $e) {
            Log::error('Failed to generate lottery numbers: ' . $e->getMessage());
            return array_fill(0, $count, 1);
        }
    }
}

if (!function_exists('processLotteryDraw')) {
    /**
     * Process lottery draw and determine winners
     */
    function processLotteryDraw($drawId)
    {
        try {
            $draw = LotteryDraw::find($drawId);
            if (!$draw) {
                throw new \Exception('Lottery draw not found');
            }

            if ($draw->status !== 'active') {
                throw new \Exception('Lottery draw is not active');
            }

            // Generate winning numbers
            $winningNumbers = generateLotteryNumbers($draw->number_range, $draw->numbers_per_ticket);
            
            $draw->update([
                'winning_numbers' => json_encode($winningNumbers),
                'draw_date' => now(),
                'status' => 'completed'
            ]);

            // Find winners
            $winners = findLotteryWinners($drawId, $winningNumbers);

            // Process prizes
            $totalPrizeDistributed = 0;
            foreach ($winners as $winner) {
                $totalPrizeDistributed += $winner['prize_amount'];
            }

            $draw->update([
                'total_prize_distributed' => $totalPrizeDistributed,
                'total_winners' => count($winners)
            ]);

            return [
                'success' => true,
                'winning_numbers' => $winningNumbers,
                'winners' => $winners,
                'total_prize' => $totalPrizeDistributed
            ];
        } catch (\Exception $e) {
            Log::error('Failed to process lottery draw: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}

if (!function_exists('findLotteryWinners')) {
    /**
     * Find lottery winners and calculate prizes
     */
    function findLotteryWinners($drawId, $winningNumbers)
    {
        try {
            $winners = [];
            $draw = LotteryDraw::find($drawId);
            
            // Check regular tickets
            $regularTickets = LotteryTicket::where('draw_id', $drawId)
                ->where('status', 'active')
                ->get();

            foreach ($regularTickets as $ticket) {
                $ticketNumbers = json_decode($ticket->ticket_numbers, true);
                $matches = array_intersect($ticketNumbers, $winningNumbers);
                $matchCount = count($matches);

                if ($matchCount >= 3) { // Minimum 3 matches to win
                    $prizeAmount = calculateLotteryPrize($matchCount, $draw->prize_pool, 'regular');
                    
                    $winner = LotteryWinner::create([
                        'user_id' => $ticket->user_id,
                        'draw_id' => $drawId,
                        'ticket_id' => $ticket->id,
                        'matches' => $matchCount,
                        'prize_amount' => $prizeAmount,
                        'ticket_type' => 'regular',
                        'status' => 'pending'
                    ]);

                    // Credit prize to user
                    creditUser($ticket->user_id, $prizeAmount, 'Lottery Prize', [
                        'winner_id' => $winner->id,
                        'draw_id' => $drawId,
                        'matches' => $matchCount,
                        'type' => 'lottery_prize'
                    ]);

                    // Send notification
                    notifyUserLottery($ticket->user_id, 
                        "Congratulations! You won " . formatCurrency($prizeAmount) . " in the lottery with {$matchCount} matches!",
                        'winner',
                        [
                            'prize_amount' => $prizeAmount,
                            'matches' => $matchCount,
                            'draw_id' => $drawId
                        ]
                    );

                    $winners[] = [
                        'user_id' => $ticket->user_id,
                        'ticket_id' => $ticket->id,
                        'matches' => $matchCount,
                        'prize_amount' => $prizeAmount,
                        'ticket_type' => 'regular'
                    ];
                }
            }

            // Check special tickets
            $specialTickets = SpecialLotteryTicket::where('draw_id', $drawId)
                ->where('status', 'active')
                ->get();

            foreach ($specialTickets as $ticket) {
                $ticketNumbers = json_decode($ticket->ticket_numbers, true);
                $matches = array_intersect($ticketNumbers, $winningNumbers);
                $matchCount = count($matches);

                if ($matchCount >= 2) { // Lower threshold for special tickets
                    $prizeAmount = calculateLotteryPrize($matchCount, $draw->prize_pool, 'special');
                    
                    $winner = LotteryWinner::create([
                        'user_id' => $ticket->user_id,
                        'draw_id' => $drawId,
                        'ticket_id' => $ticket->id,
                        'matches' => $matchCount,
                        'prize_amount' => $prizeAmount,
                        'ticket_type' => 'special',
                        'status' => 'pending'
                    ]);

                    creditUser($ticket->user_id, $prizeAmount, 'Special Lottery Prize', [
                        'winner_id' => $winner->id,
                        'draw_id' => $drawId,
                        'matches' => $matchCount,
                        'type' => 'special_lottery_prize'
                    ]);

                    notifyUserLottery($ticket->user_id, 
                        "Congratulations! You won " . formatCurrency($prizeAmount) . " in the special lottery with {$matchCount} matches!",
                        'winner',
                        [
                            'prize_amount' => $prizeAmount,
                            'matches' => $matchCount,
                            'draw_id' => $drawId,
                            'ticket_type' => 'special'
                        ]
                    );

                    $winners[] = [
                        'user_id' => $ticket->user_id,
                        'ticket_id' => $ticket->id,
                        'matches' => $matchCount,
                        'prize_amount' => $prizeAmount,
                        'ticket_type' => 'special'
                    ];
                }
            }

            return $winners;
        } catch (\Exception $e) {
            Log::error('Failed to find lottery winners: ' . $e->getMessage());
            return [];
        }
    }
}

if (!function_exists('calculateLotteryPrize')) {
    /**
     * Calculate lottery prize based on matches
     */
    function calculateLotteryPrize($matches, $prizePool, $ticketType = 'regular')
    {
        try {
            $baseMultiplier = $ticketType === 'special' ? 1.5 : 1.0;
            
            $prizeRates = [
                6 => 0.50, // 50% of prize pool for 6 matches
                5 => 0.25, // 25% of prize pool for 5 matches  
                4 => 0.15, // 15% of prize pool for 4 matches
                3 => 0.10, // 10% of prize pool for 3 matches
                2 => 0.05, // 5% of prize pool for 2 matches (special only)
            ];

            $rate = $prizeRates[$matches] ?? 0;
            $basePrize = $prizePool * $rate;
            
            return $basePrize * $baseMultiplier;
        } catch (\Exception $e) {
            Log::error('Failed to calculate lottery prize: ' . $e->getMessage());
            return 0;
        }
    }
}

if (!function_exists('getUserLotteryStats')) {
    /**
     * Get user's lottery statistics
     */
    function getUserLotteryStats($userId)
    {
        try {
            $regularTickets = LotteryTicket::where('user_id', $userId)->count();
            $specialTickets = SpecialLotteryTicket::where('user_id', $userId)->count();
            $totalWins = LotteryWinner::where('user_id', $userId)->count();
            $totalPrizes = LotteryWinner::where('user_id', $userId)->sum('prize_amount');
            
            $totalSpent = LotteryTicket::where('user_id', $userId)->sum('purchase_price') +
                         SpecialLotteryTicket::where('user_id', $userId)->sum('purchase_price');

            return [
                'regular_tickets' => $regularTickets,
                'special_tickets' => $specialTickets,
                'total_tickets' => $regularTickets + $specialTickets,
                'total_wins' => $totalWins,
                'total_prizes' => $totalPrizes,
                'total_spent' => $totalSpent,
                'net_profit' => $totalPrizes - $totalSpent,
                'win_rate' => $regularTickets + $specialTickets > 0 ? 
                    ($totalWins / ($regularTickets + $specialTickets)) * 100 : 0
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get user lottery stats: ' . $e->getMessage());
            return [
                'regular_tickets' => 0,
                'special_tickets' => 0,
                'total_tickets' => 0,
                'total_wins' => 0,
                'total_prizes' => 0,
                'total_spent' => 0,
                'net_profit' => 0,
                'win_rate' => 0
            ];
        }
    }
}

if (!function_exists('getUpcomingLotteryDraws')) {
    /**
     * Get upcoming lottery draws
     */
    function getUpcomingLotteryDraws($limit = 5)
    {
        try {
            return LotteryDraw::where('status', 'active')
                ->where('draw_date', '>', now())
                ->orderBy('draw_date', 'asc')
                ->limit($limit)
                ->get();
        } catch (\Exception $e) {
            Log::error('Failed to get upcoming lottery draws: ' . $e->getMessage());
            return collect();
        }
    }
}

if (!function_exists('getLotteryDrawResults')) {
    /**
     * Get lottery draw results with winners
     */
    function getLotteryDrawResults($drawId)
    {
        try {
            $draw = LotteryDraw::with(['winners.user'])->find($drawId);
            if (!$draw) {
                throw new \Exception('Draw not found');
            }

            return [
                'draw' => $draw,
                'winning_numbers' => json_decode($draw->winning_numbers, true),
                'winners' => $draw->winners,
                'total_winners' => $draw->total_winners,
                'total_prize_distributed' => $draw->total_prize_distributed
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get lottery draw results: ' . $e->getMessage());
            return null;
        }
    }
}
