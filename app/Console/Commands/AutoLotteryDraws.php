<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LotteryDraw;
use App\Models\LotterySetting;
use App\Models\LotteryTicket;
use App\Models\LotteryWinner;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoLotteryDraws extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lottery:auto-process {--force : Force process even if not scheduled} {--count=1 : Number of draws to create when using force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-generate and execute lottery draws based on settings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting auto lottery process...');
        
        $settings = LotterySetting::getSettings();
        
        if (!$settings->is_active) {
            $this->warn('Lottery system is disabled. Skipping...');
            return;
        }

        try {
            // 1. Auto-generate new draws if needed
            if ($settings->auto_generate_draws) {
                $this->generateNewDraws($settings);
            }
            
            // 2. Auto-execute pending draws
            if ($settings->auto_execute_draws) {
                $this->executePendingDraws($settings);
            }
            
            // 3. Update next scheduled draw time
            $this->updateNextScheduledDraw($settings);
            
            $this->info('Auto lottery process completed successfully!');
            
        } catch (\Exception $e) {
            Log::error('Auto lottery process failed: ' . $e->getMessage());
            $this->error('Auto lottery process failed: ' . $e->getMessage());
        }
    }

    /**
     * Generate new draws based on schedule
     */
    protected function generateNewDraws(LotterySetting $settings)
    {
        $this->info('Checking for new draws to generate...');
        
        $now = Carbon::now();
        $lastDraw = LotteryDraw::where('auto_draw', true)
                               ->latest('created_at')
                               ->first();
        
        $shouldCreateDraw = false;
        $nextDrawTime = null;
        
        // Check if force flag is used
        if ($this->option('force')) {
            $count = max(1, (int) $this->option('count'));
            $this->info("Force flag detected. Creating {$count} new draw(s) regardless of schedule...");
            
            for ($i = 0; $i < $count; $i++) {
                // For forced draws, use current time + increment to avoid conflicts
                $nextDrawTime = $now->copy()->addMinutes(rand(5 + ($i * 10), 30 + ($i * 10)));
                $drawNumber = 'AUTO_FORCE_' . $nextDrawTime->format('Y_m_d_H_i_s') . '_' . $i;
                
                // Check if this specific draw already exists
                $existingForcedDraw = LotteryDraw::where('draw_number', $drawNumber)->first();
                if (!$existingForcedDraw) {
                    $this->createNewDraw($settings, $nextDrawTime, $drawNumber);
                } else {
                    $this->info("Forced draw with number {$drawNumber} already exists. Skipping...");
                }
            }
            return; // Exit after creating forced draws
        } else {
            // Check if we need to generate a new draw based on schedule
            $nextDrawTime = $this->calculateNextDrawTime($settings);
            $drawNumber = 'AUTO_' . $nextDrawTime->format('Y_m_d_H_i');
            
            // Check if there's already a draw with this draw number or time
            $existingDraw = LotteryDraw::where(function($query) use ($nextDrawTime, $drawNumber) {
                $query->where('draw_time', $nextDrawTime)
                      ->orWhere('draw_number', $drawNumber);
            })->where('status', 'pending')->first();
            
            if ($existingDraw) {
                $this->info("Draw already exists: {$existingDraw->draw_number} for " . $nextDrawTime->format('Y-m-d H:i:s'));
                return;
            }
            
            if (!$lastDraw) {
                // No previous auto draw exists, create the first one
                $this->info('No previous auto draw found. Creating first draw...');
                $shouldCreateDraw = true;
            } else {
                // Check if enough time has passed based on frequency settings
                $timeSinceLastDraw = $lastDraw->created_at->diffInMinutes($now);
                $frequencyMinutes = $this->getFrequencyInMinutes($settings->auto_generation_frequency);
                
                // Allow for more flexible scheduling - check if we're past the scheduled time
                $lastDrawTime = $lastDraw->draw_time ?? $lastDraw->created_at;
                $timeSinceScheduled = $lastDrawTime->diffInMinutes($now);
                
                if ($timeSinceLastDraw >= $frequencyMinutes || $timeSinceScheduled >= $frequencyMinutes) {
                    $shouldCreateDraw = true;
                    $this->info("Time to create new draw. Last draw was {$timeSinceLastDraw} minutes ago, frequency is {$frequencyMinutes} minutes.");
                } else {
                    // Check if we have no pending draws and it's been at least 30 minutes
                    $pendingDraws = LotteryDraw::where('status', 'pending')->where('auto_draw', true)->count();
                    if ($pendingDraws == 0 && $timeSinceLastDraw >= 30) {
                        $shouldCreateDraw = true;
                        $this->info("No pending draws found and {$timeSinceLastDraw} minutes passed. Creating new draw...");
                    } else {
                        $this->info("Too soon to create new draw. Last draw was {$timeSinceLastDraw} minutes ago, need {$frequencyMinutes} minutes. Pending draws: {$pendingDraws}");
                    }
                }
            }
        }
        
        if ($shouldCreateDraw) {
            // Generate new draw
            $this->createNewDraw($settings, $nextDrawTime);
        }
    }

    /**
     * Create a new lottery draw
     */
    protected function createNewDraw(LotterySetting $settings, Carbon $drawTime, $customDrawNumber = null)
    {
        $this->info('Creating new draw for ' . $drawTime->format('Y-m-d H:i:s'));
        
        DB::beginTransaction();
        
        try {
            // Calculate prize structure
            $maxTickets = $settings->max_virtual_tickets ?? 1000;
            $ticketPrice = $settings->ticket_price;
            $totalRevenue = $maxTickets * $ticketPrice;
            $adminCommission = $totalRevenue * ($settings->admin_commission_percentage / 100);
            $prizePool = $totalRevenue - $adminCommission;
            
            // Get prize structure from settings
            $defaultType = $settings->prize_distribution_type ?? 'percentage';
            $prizeStructure = $settings->prize_structure ?? [
                1 => ['name' => 'First Prize', 'type' => $defaultType, 'amount' => $defaultType === 'fixed_amount' ? 100 : 50],
                2 => ['name' => 'Second Prize', 'type' => $defaultType, 'amount' => $defaultType === 'fixed_amount' ? 100 : 30],
                3 => ['name' => 'Third Prize', 'type' => $defaultType, 'amount' => $defaultType === 'fixed_amount' ? 100 : 20],
            ];
            
            // Calculate prize distribution based on settings
            $prizeDistribution = [];
            
            // Check the prize distribution type from settings
            if ($settings->prize_distribution_type === 'fixed_amount') {
                // Use fixed amounts directly from settings
                foreach ($prizeStructure as $position => $prize) {
                    // Check if there are multiple winners for this position
                    if (isset($prize['multiple_winners']) && is_array($prize['multiple_winners'])) {
                        // Handle multiple winners per position
                        foreach ($prize['multiple_winners'] as $winnerIndex => $winnerData) {
                            $amount = (float) $winnerData['amount'];
                            $prizeDistribution[] = [
                                'position' => $position,
                                'winner_index' => $winnerIndex + 1,
                                'amount' => $amount,
                                'name' => $prize['name'] . ' (Winner ' . ($winnerIndex + 1) . ')',
                                'type' => 'fixed_amount'
                            ];
                        }
                    } else {
                        // Single winner per position
                        $amount = (float) $prize['amount'];
                        $prizeDistribution[] = [
                            'position' => $position,
                            'amount' => $amount,
                            'name' => $prize['name'],
                            'type' => 'fixed_amount'
                        ];
                    }
                }
            } else {
                // Use percentage-based calculation
                foreach ($prizeStructure as $position => $prize) {
                    if ($prize['type'] === 'fixed_amount') {
                        // Fixed amount prizes
                        $amount = (float) $prize['amount'];
                        $percentage = ($amount / $prizePool) * 100;
                    } else {
                        // Percentage-based prizes
                        $percentage = (float) $prize['amount'];
                        $amount = $prizePool * ($percentage / 100);
                    }
                    
                    $prizeDistribution[] = [
                        'position' => $position,
                        'percentage' => $percentage,
                        'amount' => $amount,
                        'name' => $prize['name'],
                        'type' => $prize['type'] ?? 'percentage'
                    ];
                }
            }
            
            // Generate draw number
            $drawNumber = $customDrawNumber ?? ('AUTO_' . $drawTime->format('Y_m_d_H_i'));
            
            $draw = LotteryDraw::create([
                'draw_number' => $drawNumber,
                'draw_date' => $drawTime->toDateString(),
                'draw_time' => $drawTime,
                'status' => 'pending',
                'total_prize_pool' => $prizePool,
                'total_tickets_sold' => 0,
                'max_tickets' => $maxTickets,
                'ticket_price' => $ticketPrice,
                'admin_commission' => $adminCommission,
                'number_of_winners' => count($prizeDistribution),
                'prize_distribution_type' => $settings->prize_distribution_type,
                'auto_draw' => true,
                'auto_prize_distribution' => true,
                'prize_distribution' => $prizeDistribution,
                'virtual_tickets_sold' => 0,
                'display_tickets_sold' => 0,
                'manual_winner_selection_enabled' => $settings->enable_manual_winner_selection,
            ]);
            
            // Generate virtual tickets if enabled
            if ($settings->enable_virtual_tickets) {
                $this->generateVirtualTickets($draw, $settings);
            }
            
            DB::commit();
            
            $this->info("Created draw {$drawNumber} with ID {$draw->id}");
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Handle duplicate entry errors more gracefully
            if (str_contains($e->getMessage(), 'Duplicate entry') && str_contains($e->getMessage(), 'draw_number')) {
                $this->warn("Draw {$drawNumber} already exists. Skipping creation.");
                return;
            }
            
            // For other errors, log and rethrow
            Log::error('Failed to create auto lottery draw: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate virtual tickets for a draw
     */
    protected function generateVirtualTickets(LotteryDraw $draw, LotterySetting $settings)
    {
        $this->info('Generating virtual tickets...');
        
        $minVirtual = $settings->min_virtual_tickets ?? 100;
        $maxVirtual = $settings->max_virtual_tickets ?? 1000;
        
        // Random number of virtual tickets
        $virtualCount = rand($minVirtual, $maxVirtual);
        
        // Get virtual user (should be user ID 47)
        $virtualUsers = $this->getVirtualUsers(1); // Only need 1 virtual user
        
        if ($virtualUsers->isEmpty()) {
            $this->error('No virtual users available for ticket generation');
            return;
        }
        
        $virtualUser = $virtualUsers->first();
        $tickets = [];
        $existingTicketNumbers = []; // Track generated numbers to avoid duplicates
        
        // Generate all virtual tickets for the single virtual user
        for ($i = 0; $i < $virtualCount; $i++) {
            // Generate unique ticket number in format 3D7A-7D9B-A3A8-073C
            $ticketNumber = $this->generateUniqueTicketNumber($existingTicketNumbers);
            $existingTicketNumbers[] = $ticketNumber;
            
            $tickets[] = [
                'ticket_number' => $ticketNumber,
                'user_id' => $virtualUser->id,
                'lottery_draw_id' => $draw->id,
                'ticket_price' => $draw->ticket_price,
                'purchased_at' => now(),
                'status' => 'active',
                'payment_method' => 'virtual',
                'is_virtual' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        // Insert virtual tickets in batches
        if (!empty($tickets)) {
            LotteryTicket::insert($tickets);
            
            // Update draw with virtual ticket count
            $draw->update([
                'virtual_tickets_sold' => count($tickets),
                'display_tickets_sold' => count($tickets),
                'total_tickets_sold' => $draw->total_tickets_sold + count($tickets)
            ]);
            
            $this->info("Generated {$virtualCount} virtual tickets for user ID {$virtualUser->id}");
        }
    }

    /**
     * Get or create virtual users for lottery tickets
     */
    protected function getVirtualUsers($count)
    {
        // Get virtual user ID from lottery settings
        $settings = LotterySetting::getSettings();
        $virtualUserId = $settings->virtual_user_id ?? null;
        
        if (!$virtualUserId) {
            $this->error("Virtual user ID not configured in lottery settings! Please set 'virtual_user_id' in admin panel.");
            return collect();
        }
        
        // Use configured virtual user ID for all virtual tickets
        $virtualUser = User::find($virtualUserId);
        
        if (!$virtualUser) {
            $this->error("Virtual user with ID {$virtualUserId} not found! Please ensure this user exists or update the virtual_user_id setting.");
            return collect();
        }
        
        $this->info("Using configured virtual user (ID: {$virtualUserId}) for virtual tickets");
        
        // Return collection with the virtual user repeated to meet the count requirement
        return collect(array_fill(0, $count, $virtualUser));
    }

    /**
     * Generate unique ticket number in format 3D7A-7D9B-A3A8-073C
     */
    protected function generateUniqueTicketNumber(array $existingNumbers)
    {
        $maxAttempts = 100;
        $attempts = 0;
        
        do {
            $ticketNumber = $this->generateTicketNumber();
            $attempts++;
            
            // Check if this number already exists in our current batch
            if (!in_array($ticketNumber, $existingNumbers)) {
                // Also check if it exists in the database
                $exists = LotteryTicket::where('ticket_number', $ticketNumber)->exists();
                if (!$exists) {
                    return $ticketNumber;
                }
            }
        } while ($attempts < $maxAttempts);
        
        // Fallback to secure unique number if we can't generate a unique one
        $fallbackNumber = \App\Models\LotteryTicket::generateTicketNumber();
        return $fallbackNumber;
    }

    /**
     * Generate ticket number in format XXXX-XXXX-XXXX-XXXX (consistent with LotteryTicket model)
     */
    protected function generateTicketNumber()
    {
        // Use the same generation method as LotteryTicket to ensure consistency
        return \App\Models\LotteryTicket::generateTicketNumber();
    }

    /**
     * Execute pending draws that are due
     */
    protected function executePendingDraws(LotterySetting $settings)
    {
        $this->info('Checking for draws to execute...');
        
        $now = Carbon::now();
        $executeTime = $now->subMinutes($settings->auto_execute_delay_minutes ?? 0);
        
        $pendingDraws = LotteryDraw::where('status', 'pending')
                                   ->where('draw_time', '<=', $executeTime)
                                   ->where('auto_draw', true)
                                   ->get();
        
        foreach ($pendingDraws as $draw) {
            $this->executeDraw($draw, $settings);
        }
        
        if ($pendingDraws->count() === 0) {
            $this->info('No draws ready for execution');
        }
    }

    /**
     * Execute a single draw
     */
    protected function executeDraw(LotteryDraw $draw, LotterySetting $settings)
    {
        $this->info("Executing draw {$draw->draw_number}...");
        
        DB::beginTransaction();
        
        try {
            // Get all tickets for this draw
            $tickets = LotteryTicket::where('lottery_draw_id', $draw->id)
                                   ->where('status', 'active')
                                   ->get();
            
            if ($tickets->isEmpty()) {
                $this->warn("No tickets found for draw {$draw->draw_number}. Skipping execution.");
                return;
            }
            
            // Select winners
            $winners = $this->selectWinners($draw, $tickets, $settings);
            
            // Create winner records and distribute prizes
            foreach ($winners as $winnerData) {
                $isVirtualWinner = $winnerData['ticket']->is_virtual;
                
                // For virtual winners, use the ticket's virtual user ID
                // For real winners, use the actual user ID
                $winnerId = $winnerData['ticket']->user_id;
                
                LotteryWinner::create([
                    'lottery_draw_id' => $draw->id,
                    'lottery_ticket_id' => $winnerData['ticket']->id,
                    'user_id' => $winnerId,
                    'prize_position' => $winnerData['position'],
                    'prize_name' => $winnerData['name'] ?? $this->getPrizeName($winnerData['position']),
                    'prize_amount' => $winnerData['amount'],
                    'claim_status' => 'pending',
                ]);
                
                // Update ticket status
                $winnerData['ticket']->update([
                    'status' => 'winner',
                    'prize_amount' => $winnerData['amount']
                ]);
                
                // Only auto-distribute prize for real ticket holders, not virtual winners
                if ($settings->auto_prize_distribution && !$isVirtualWinner) {
                    $realUser = User::find($winnerId);
                    if ($realUser && $realUser->status == 1) { // Only for active real users
                        $this->distributePrize($realUser, $winnerData['amount']);
                    }
                }
                
                if ($isVirtualWinner) {
                    $virtualUser = User::find($winnerId);
                    $displayName = $virtualUser ? $virtualUser->firstname . ' ' . $virtualUser->lastname : 'Virtual User';
                    $this->info("Virtual winner selected: {$displayName} with Ticket {$winnerData['ticket']->ticket_number} for {$winnerData['name']} - No real money distributed");
                }
            }
            
            // Update draw status
            $draw->update([
                'status' => 'drawn',
                'drawn_at' => now(),
                'winning_numbers' => $winners->pluck('ticket.ticket_number')->toArray()
            ]);
            
            DB::commit();
            
            $this->info("Draw {$draw->draw_number} executed successfully with " . count($winners) . " winners");
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to execute draw {$draw->draw_number}: " . $e->getMessage());
            $this->error("Failed to execute draw {$draw->draw_number}: " . $e->getMessage());
        }
    }

    /**
     * Select winners for a draw
     */
    protected function selectWinners(LotteryDraw $draw, $tickets, LotterySetting $settings)
    {
        $winners = collect();
        $prizeDistribution = $draw->prize_distribution;
        
        // Check if manual winner selection is enabled and has pre-selected winners
        if ($draw->manual_winner_selection_enabled && $draw->manually_selected_winners) {
            $winners = $this->selectManualWinners($draw, $tickets, $prizeDistribution);
        } else {
            $winners = $this->selectRandomWinners($draw, $tickets, $prizeDistribution);
        }
        
        return $winners;
    }

    /**
     * Select manual winners
     */
    protected function selectManualWinners(LotteryDraw $draw, $tickets, $prizeDistribution)
    {
        $winners = collect();
        $manualWinners = $draw->manually_selected_winners;
        
        foreach ($prizeDistribution as $index => $prize) {
            if (isset($manualWinners[$index])) {
                $userId = $manualWinners[$index]['user_id'];
                $ticket = $tickets->where('user_id', $userId)->first();
                
                if ($ticket) {
                    $winners->push([
                        'position' => $prize['position'],
                        'amount' => $prize['amount'],
                        'name' => $prize['name'],
                        'ticket' => $ticket
                    ]);
                }
            }
        }
        
        return $winners;
    }

    /**
     * Select random winners
     */
    protected function selectRandomWinners(LotteryDraw $draw, $tickets, $prizeDistribution)
    {
        $winners = collect();
        
        // For auto-lottery, only select from virtual tickets
        $availableTickets = $tickets->where('is_virtual', true)->shuffle();
        
        // If no virtual tickets available, fall back to all tickets
        if ($availableTickets->isEmpty()) {
            $this->warn('No virtual tickets available for auto-lottery, using all tickets');
            $availableTickets = $tickets->shuffle();
        }
        
        foreach ($prizeDistribution as $prize) {
            if ($availableTickets->isNotEmpty()) {
                $winningTicket = $availableTickets->pop();
                
                $winners->push([
                    'position' => $prize['position'],
                    'amount' => $prize['amount'],
                    'name' => $prize['name'],
                    'ticket' => $winningTicket
                ]);
            }
        }
        
        return $winners;
    }

    /**
     * Distribute prize to winner
     */
    protected function distributePrize(User $user, $amount)
    {
        // Add prize amount to user balance
        $user->increment('balance', $amount);
        
        // Log the transaction
        Log::info("Prize distributed: User {$user->id} received ${amount}");
    }

    /**
     * Get prize name by position
     */
    protected function getPrizeName($position)
    {
        $names = [
            1 => 'First Prize',
            2 => 'Second Prize', 
            3 => 'Third Prize',
            4 => 'Fourth Prize',
            5 => 'Fifth Prize'
        ];
        
        return $names[$position] ?? $position . 'th Prize';
    }

    /**
     * Get frequency in minutes
     */
    protected function getFrequencyInMinutes($frequency)
    {
        switch ($frequency) {
            case 'daily':
                return 24 * 60; // 1440 minutes
            case 'weekly':
                return 7 * 24 * 60; // 10080 minutes
            case 'monthly':
                return 30 * 24 * 60; // 43200 minutes
            default:
                return 24 * 60; // Default to daily
        }
    }

    /**
     * Calculate next draw time based on frequency
     */
    protected function calculateNextDrawTime(LotterySetting $settings)
    {
        $now = Carbon::now();
        
        // Check if this is the first auto draw
        $hasAutoDraw = LotteryDraw::where('auto_draw', true)->exists();
        
        if (!$hasAutoDraw) {
            // For the first auto draw, schedule it soon (within next hour)
            return $now->addMinutes(30)->setSeconds(0);
        }
        
        switch ($settings->auto_generation_frequency) {
            case 'daily':
                return $now->addDay()->setTime(
                    $settings->draw_time->hour,
                    $settings->draw_time->minute
                );
                
            case 'weekly':
                return $now->next($settings->draw_day)->setTime(
                    $settings->draw_time->hour,
                    $settings->draw_time->minute
                );
                
            case 'monthly':
                return $now->addMonth()->day(1)->setTime(
                    $settings->draw_time->hour,
                    $settings->draw_time->minute
                );
                
            default:
                return $now->addDay()->setTime(20, 0); // Default to daily at 8 PM
        }
    }

    /**
     * Update next scheduled draw time
     */
    protected function updateNextScheduledDraw(LotterySetting $settings)
    {
        $nextDrawTime = $this->calculateNextDrawTime($settings);
        
        $settings->update([
            'next_auto_draw' => $nextDrawTime
        ]);
        
        $this->info('Next auto draw scheduled for: ' . $nextDrawTime->format('Y-m-d H:i:s'));
    }
}
