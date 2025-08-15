<?php

namespace Database\Seeders;

use App\Models\LotteryDraw;
use App\Models\LotteryTicket;
use App\Models\LotteryWinner;
use App\Models\LotterySetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HistoricalLotteryDrawSeeder extends Seeder
{
    private $users;
    private $virtualUser;

    public function run()
    {
        
        // Get all regular users and virtual user
        $this->users = User::where('username', '!=', 'lottery_virtual_user')->where('status', 1)->get();
        $this->virtualUser = User::where('username', 'lottery_virtual_user')->first();
        
        if ($this->users->count() < 5) {
            $this->command->warn('Not enough users found (' . $this->users->count() . '). Need at least 5 users for realistic lottery draws.');
            $this->command->info('Current users: ' . $this->users->pluck('username')->join(', '));
            return;
        }
        
        if ($this->users->count() < 1) {
            $this->command->error('No active users found for lottery draws. Please create some users first.');
            return;
        }

        if (!$this->virtualUser) {
            $this->command->error('Virtual lottery user not found. Please run UserSeeder first.');
            return;
        }

        $this->command->info('Creating 50 historical lottery draws...');
        $this->command->info('Using ' . $this->users->count() . ' real users for tickets and winners');
        $this->command->info('Real users: ' . $this->users->pluck('username')->take(5)->join(', ') . ($this->users->count() > 5 ? '...' : ''));

        // Start from August 10, 2025 (Sunday) as Draw #50
        $latestSunday = Carbon::create(2025, 8, 10); // August 10, 2025 (Sunday)
        
        DB::beginTransaction();
        
        try {
            for ($week = 0; $week < 50; $week++) {
                // Count backwards from latest Sunday
                $drawDate = $latestSunday->copy()->subWeeks($week);
                $drawNumber = 50 - $week; // Draw 50 is most recent (Aug 10), 49 is previous week (Aug 3), etc.
                $this->createLotteryDraw($drawDate, $drawNumber);
                
                if ($week % 10 == 0) {
                    $this->command->info("Created " . ($week + 1) . " draws...");
                }
            }
            
            DB::commit();
            $this->command->info('Successfully created 50 historical lottery draws with realistic data!');
            $this->command->info('Draw #50 (latest): ' . $latestSunday->format('Y-m-d'));
            $this->command->info('Draw #49: ' . $latestSunday->copy()->subWeek()->format('Y-m-d'));
            $this->command->info('Draw #1 (oldest): ' . $latestSunday->copy()->subWeeks(49)->format('Y-m-d'));
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error creating historical draws: ' . $e->getMessage());
            throw $e;
        }
    }

    private function createLotteryDraw($drawDate, $drawNumber)
    {
        // Generate draw number like: DRAW_51, DRAW_50, etc.
        $drawNumberString = 'DRAW_' . $drawNumber;
        $drawTime = $drawDate->copy()->setTime(20, 0, 0); // 8 PM draw time
        
        // Create the lottery draw
        $draw = LotteryDraw::create([
            'draw_number' => $drawNumberString,
            'draw_date' => $drawDate->toDateString(),
            'draw_time' => $drawTime,
            'status' => 'completed',
            'optimized_at' => $drawTime,
            'cleanup_performed' => true,
            'total_prize_pool' => 0, // Will be calculated after tickets
            'total_tickets_sold' => 0, // Will be updated
            'virtual_tickets_sold' => 0, // Will be updated
            'display_tickets_sold' => 0, // Will be updated
            'manual_winner_selection_enabled' => false,
            'has_manual_winners' => false,
            'prize_distribution_type' => 'fixed_amount',
            'allow_multiple_winners_per_place' => false,
            'prize_distribution' => json_encode([
                '1' => ['name' => '1st Prize', 'type' => 'fixed_amount', 'amount' => '1000'],
                '2' => ['name' => '2nd Prize', 'type' => 'fixed_amount', 'amount' => '300'],
                '3' => ['name' => '3rd Prize', 'type' => 'fixed_amount', 'amount' => '100'],
            ]),
            'winning_numbers' => null, // Will be set after winners
            'max_tickets' => fake()->numberBetween(500, 2000),
            'ticket_price' => 2.00,
            'admin_commission_percentage' => 10.00,
            'auto_draw' => true,
            'auto_prize_distribution' => true,
        ]);

        // Generate realistic ticket sales
        $this->createTicketsForDraw($draw);
        
        // Select winners
        $this->selectWinnersForDraw($draw);
        
        // Update final statistics
        $this->updateDrawStatistics($draw);
        
        // Delete all tickets after the draw is completed (as requested)
        $this->deleteTicketsAfterDraw($draw);
        
        return $draw;
    }

    private function createTicketsForDraw($draw)
    {
        // Realistic ticket sales distribution
        $minTickets = fake()->numberBetween(50, 200);
        $maxTickets = min($draw->max_tickets, fake()->numberBetween(300, 800));
        $realTicketCount = fake()->numberBetween($minTickets, $maxTickets);
        
        // Only 1 virtual ticket per draw
        $virtualTicketCount = 1;
        $totalDisplayTickets = $realTicketCount + $virtualTicketCount;
        
        $tickets = collect();
        
        // Create all tickets as virtual tickets with user_id = 1 to avoid conflicts with future real users
        for ($i = 0; $i < $realTicketCount; $i++) {
            $purchaseTime = fake()->dateTimeBetween(
                $draw->draw_date->copy()->subDays(6), 
                $draw->draw_time->copy()->subHour()
            );
            
            // Use specific ticket number for the first ticket of draw #50 (most recent)
            $ticketNumber = ($i == 0 && $draw->draw_number == 'DRAW_50') 
                ? '340B-660D-3DE4-5A6A' 
                : $this->generateTicketNumber($draw, $i + 1);
            
            $ticket = LotteryTicket::create([
                'ticket_number' => $ticketNumber,
                'user_id' => 1, // Always use virtual user ID = 1
                'lottery_draw_id' => $draw->id,
                'ticket_price' => $draw->ticket_price,
                'purchased_at' => $purchaseTime,
                'status' => 'active', // Will be updated if winner
                'payment_method' => 'balance',
                'transaction_reference' => 'VIRTUAL_' . fake()->uuid(),
                'is_virtual' => true, // Mark as virtual
                'token_type' => 'lottery', // Use allowed enum value
                'is_valid_token' => true,
                'is_transferable' => false,
                'transfer_count' => 0,
                'created_at' => $purchaseTime,
                'updated_at' => $purchaseTime,
            ]);
            
            $tickets->push($ticket);
        }
        
        // Create one additional system virtual ticket
        for ($i = 0; $i < $virtualTicketCount; $i++) {
            $purchaseTime = fake()->dateTimeBetween(
                $draw->draw_date->copy()->subDays(6), 
                $draw->draw_time->copy()->subHour()
            );
            
            LotteryTicket::create([
                'ticket_number' => $this->generateTicketNumber($draw, $realTicketCount + $i + 1, true),
                'user_id' => 1, // Virtual user ID = 1
                'lottery_draw_id' => $draw->id,
                'ticket_price' => $draw->ticket_price,
                'purchased_at' => $purchaseTime,
                'status' => 'active',
                'payment_method' => 'balance',
                'transaction_reference' => 'VIRTUAL_' . fake()->uuid(),
                'is_virtual' => true,
                'virtual_user_type' => 'system',
                'virtual_metadata' => json_encode(['type' => 'system_generated']),
                'token_type' => 'lottery', // Use allowed enum value
                'is_valid_token' => true,
                'is_transferable' => false,
                'transfer_count' => 0,
                'created_at' => $purchaseTime,
                'updated_at' => $purchaseTime,
            ]);
        }
        
        // Update draw ticket counts
        $draw->update([
            'total_tickets_sold' => $realTicketCount,
            'virtual_tickets_sold' => $virtualTicketCount,
            'display_tickets_sold' => $totalDisplayTickets,
        ]);
        
        return $tickets;
    }

    private function selectWinnersForDraw($draw)
    {
        // Get all tickets for winner selection (all are virtual with user_id = 1)
        $allTickets = $draw->tickets()->get();
            
        if ($allTickets->count() < 3) {
            // Not enough tickets for all prizes
            return;
        }
        
        $prizeStructure = json_decode($draw->prize_distribution, true);
        $winnerTicketIds = [];
        $selectedTickets = $allTickets->shuffle();
        
        foreach ($prizeStructure as $position => $prizeData) {
            if ($selectedTickets->isEmpty()) {
                break;
            }
            
            $winningTicket = $selectedTickets->shift(); // Remove from collection to avoid duplicates
            $prizeAmount = (float) $prizeData['amount'];
            
            // Create winner record - always assign to virtual user (user_id = 1) to avoid conflicts with future real users
            LotteryWinner::create([
                'lottery_draw_id' => $draw->id,
                'lottery_ticket_id' => $winningTicket->id,
                'user_id' => 1, // Always use virtual user ID = 1 for all historical winners
                'prize_position' => (int) $position,
                'winner_index' => 1,
                'prize_name' => $prizeData['name'],
                'prize_amount' => $prizeAmount,
                'claim_status' => fake()->randomElement(['claimed', 'claimed', 'claimed', 'pending']), // Most are claimed
                'prize_distributed' => fake()->boolean(85),
                'is_manual_selection' => false,
                'selected_at' => $draw->draw_time,
                'claimed_at' => fake()->boolean(85) ? fake()->dateTimeBetween($draw->draw_time, $draw->draw_time->copy()->addDays(7)) : null,
                'claim_method' => 'auto',
                'created_at' => $draw->draw_time,
                'updated_at' => $draw->draw_time,
            ]);
            
            // Update winning ticket
            $winningTicket->update([
                'status' => 'winner',
                'prize_amount' => $prizeAmount,
            ]);
            
            $winnerTicketIds[] = $winningTicket->id;
        }
        
        // Update all non-winning tickets to 'expired'
        $draw->tickets()
            ->whereNotIn('id', $winnerTicketIds)
            ->update(['status' => 'expired']);
        
        // Set winning numbers on draw
        $draw->update([
            'winning_numbers' => json_encode($winnerTicketIds),
        ]);
    }

    private function updateDrawStatistics($draw)
    {
        $totalRevenue = $draw->tickets()->sum('ticket_price');
        $totalPrizes = $draw->winners()->sum('prize_amount');
        $adminCommission = $totalRevenue * ($draw->admin_commission_percentage / 100);
        $prizePool = $totalRevenue - $adminCommission;
        
        $draw->update([
            'total_prize_pool' => $prizePool,
        ]);
    }

    private function deleteTicketsAfterDraw($draw)
    {
        // Delete all lottery tickets for this draw after it's completed
        // This simulates the system where tickets are removed after each draw
        $deletedCount = LotteryTicket::where('lottery_draw_id', $draw->id)->delete();
        
        $this->command->info("Deleted {$deletedCount} tickets for {$draw->draw_number} after draw completion");
        
        // Update draw statistics to reflect that tickets were deleted
        $draw->update([
            'cleanup_performed' => true,
            'total_tickets_sold' => 0, // Reset since tickets are deleted
            'virtual_tickets_sold' => 0, // Reset since tickets are deleted
            'display_tickets_sold' => 0, // Reset since tickets are deleted
        ]);
    }

    private function generateTicketNumber($draw, $sequence, $isVirtual = false)
    {
        // Generate hexadecimal format like: 340B-660D-3DE4-5A6A
        $part1 = strtoupper(dechex(fake()->numberBetween(0x1000, 0xFFFF)));
        $part2 = strtoupper(dechex(fake()->numberBetween(0x1000, 0xFFFF)));
        $part3 = strtoupper(dechex(fake()->numberBetween(0x1000, 0xFFFF)));
        $part4 = strtoupper(dechex(fake()->numberBetween(0x1000, 0xFFFF)));
        
        return $part1 . '-' . $part2 . '-' . $part3 . '-' . $part4;
    }
}
