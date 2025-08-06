<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LotteryDraw;
use App\Models\LotteryTicket;
use App\Models\LotterySetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LotteryManageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lottery:manage {action} {--force : Force action without confirmation} {--tickets=0 : Number of test tickets to create}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage lottery draws - cancel current draws and create new ones';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        
        switch ($action) {
            case 'cancel':
                return $this->cancelCurrentDraws();
            case 'create':
                return $this->createNewDraw();
            case 'reset':
                return $this->resetAndCreateNew();
            case 'status':
                return $this->showStatus();
            case 'test':
                return $this->addTestTickets();
            case 'draw':
                return $this->performTestDraw();
            default:
                $this->error('Invalid action. Available actions: cancel, create, reset, status, test, draw');
                return Command::FAILURE;
        }
    }

    /**
     * Cancel all current pending draws
     */
    private function cancelCurrentDraws()
    {
        $this->info('🔍 Checking for pending draws...');
        
        $pendingDraws = LotteryDraw::where('status', 'pending')->get();
        
        if ($pendingDraws->isEmpty()) {
            $this->info('✅ No pending draws found.');
            return Command::SUCCESS;
        }

        $this->info("Found {$pendingDraws->count()} pending draw(s):");
        foreach ($pendingDraws as $draw) {
            $this->line("  - Draw #{$draw->id} ({$draw->draw_number}) - {$draw->total_tickets_sold} tickets sold");
        }

        if (!$this->option('force') && !$this->confirm('Do you want to cancel these draws?')) {
            $this->info('Operation cancelled.');
            return Command::SUCCESS;
        }

        DB::beginTransaction();

        try {
            foreach ($pendingDraws as $draw) {
                $this->info("Cancelling draw #{$draw->id}...");
                
                // Refund all tickets
                $tickets = $draw->tickets()->where('status', 'active')->get();
                foreach ($tickets as $ticket) {
                    if ($ticket->user && $ticket->payment_method === 'deposit_wallet') {
                        $ticket->user->increment('deposit_wallet', $ticket->ticket_price);
                        $this->line("  Refunded \${$ticket->ticket_price} to user #{$ticket->user_id}");
                    }
                    $ticket->update(['status' => 'refunded']);
                }
                
                // Update draw status
                $draw->update(['status' => 'completed']); // Use 'completed' instead of 'cancelled'
                $this->info("  ✅ Draw #{$draw->id} cancelled successfully");
            }

            DB::commit();
            $this->info('🎉 All pending draws cancelled successfully!');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error cancelling draws: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Create a new draw
     */
    private function createNewDraw()
    {
        $this->info('🎲 Creating new lottery draw...');

        try {
            $settings = LotterySetting::getSettings();
            
            // Calculate next draw date (next Sunday at 8 PM)
            $nextDrawDate = Carbon::now()->next(Carbon::SUNDAY)->setTime(20, 0, 0);
            
            // Generate draw number
            $drawNumber = 'DRAW_' . $nextDrawDate->format('Y_W') . '_' . time();
            
            $draw = LotteryDraw::create([
                'draw_number' => $drawNumber,
                'draw_date' => $nextDrawDate->toDateString(),
                'draw_time' => $nextDrawDate,
                'status' => 'pending',
                'total_tickets_sold' => 0,
                'total_prize_pool' => 0,
            ]);

            $this->info('✅ New draw created successfully!');
            $this->table(['Field', 'Value'], [
                ['Draw ID', $draw->id],
                ['Draw Number', $draw->draw_number],
                ['Draw Date', $draw->draw_date],
                ['Draw Time', $draw->draw_time->format('Y-m-d H:i:s')],
                ['Ticket Price', '$' . number_format($settings->ticket_price ?? 0, 2)],
                ['Status', $draw->status],
            ]);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Error creating draw: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Reset all draws and create a new one
     */
    private function resetAndCreateNew()
    {
        $this->info('🔄 Resetting lottery system...');
        
        // First cancel current draws
        $this->call('lottery:manage', ['action' => 'cancel', '--force' => true]);
        
        // Then create new draw
        return $this->call('lottery:manage', ['action' => 'create']);
    }

    /**
     * Show current lottery status
     */
    private function showStatus()
    {
        $this->info('📊 Current Lottery System Status');
        $this->line('');

        $settings = LotterySetting::getSettings();
        $this->info('🔧 System Settings:');
        $this->table(['Setting', 'Value'], [
            ['System Active', $settings->is_active ? '✅ Yes' : '❌ No'],
            ['Ticket Price', '$' . number_format($settings->ticket_price ?? 0, 2)],
            ['Min Tickets for Draw', $settings->min_tickets_for_draw ?? 'Not set'],
            ['Max Tickets per User', $settings->max_tickets_per_user ?? 'Not set'],
        ]);

        $this->line('');
        $this->info('🎲 Current Draws:');
        
        $draws = LotteryDraw::orderBy('created_at', 'desc')->take(5)->get();
        
        if ($draws->isEmpty()) {
            $this->line('  No draws found.');
        } else {
            $drawData = [];
            foreach ($draws as $draw) {
                $drawData[] = [
                    $draw->id,
                    $draw->draw_number ?: 'N/A',
                    $draw->status,
                    $draw->draw_date ? $draw->draw_date->format('Y-m-d H:i') : 'N/A',
                    $draw->total_tickets_sold ?: 0,
                    '$' . number_format($draw->total_prize_pool ?: 0, 2)
                ];
            }
            
            $this->table(['ID', 'Number', 'Status', 'Date', 'Tickets', 'Prize Pool'], $drawData);
        }

        return Command::SUCCESS;
    }

    /**
     * Add test tickets for testing draws
     */
    private function addTestTickets()
    {
        $this->info('🎫 Adding test tickets...');
        
        $pendingDraw = LotteryDraw::where('status', 'pending')->first();
        if (!$pendingDraw) {
            $this->error('❌ No pending draw found. Create a draw first.');
            return Command::FAILURE;
        }

        $ticketCount = (int) $this->option('tickets') ?: $this->ask('How many test tickets to create?', 15);
        
        if ($ticketCount < 1) {
            $this->error('❌ Invalid ticket count.');
            return Command::FAILURE;
        }

        try {
            // Get first admin user for test tickets
            $user = \App\Models\User::first();
                 
            if (!$user) {
                $this->error('❌ No users found in database.');
                return Command::FAILURE;
            }

            $settings = LotterySetting::getSettings();
            $ticketPrice = $settings->ticket_price ?? 2.00;

            for ($i = 0; $i < $ticketCount; $i++) {
                $ticketNumber = 'TEST_' . str_pad($i + 1, 6, '0', STR_PAD_LEFT);
                
                LotteryTicket::create([
                    'ticket_number' => $ticketNumber,
                    'user_id' => $user->id,
                    'lottery_draw_id' => $pendingDraw->id,
                    'ticket_price' => $ticketPrice,
                    'purchased_at' => now(),
                    'status' => 'active',
                    'payment_method' => 'test',
                    'transaction_reference' => 'TEST_' . time() . '_' . $i,
                ]);
            }

            // Update draw totals
            $pendingDraw->updateTotals();

            $this->info("✅ Created {$ticketCount} test tickets for draw #{$pendingDraw->id}");
            $this->info("🎯 Draw now has {$pendingDraw->fresh()->total_tickets_sold} total tickets");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Error creating test tickets: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Perform a test draw
     */
    private function performTestDraw()
    {
        $this->info('🎲 Performing test lottery draw...');

        $pendingDraw = LotteryDraw::where('status', 'pending')->first();
        if (!$pendingDraw) {
            $this->error('❌ No pending draw found.');
            return Command::FAILURE;
        }

        $this->info("🎯 Processing draw: {$pendingDraw->draw_number}");
        $this->info("🎫 Tickets sold: {$pendingDraw->total_tickets_sold}");

        if ($pendingDraw->total_tickets_sold < 1) {
            $this->error('❌ No tickets sold. Add test tickets first using: php artisan lottery:manage test --tickets=15');
            return Command::FAILURE;
        }

        if (!$this->option('force') && !$this->confirm('Perform draw now?')) {
            $this->info('Draw cancelled.');
            return Command::SUCCESS;
        }

        try {
            DB::beginTransaction();

            $winners = $pendingDraw->performDraw();

            DB::commit();

            $this->info('✅ Draw completed successfully!');
            $this->info("🏆 Winners selected: " . count($winners));

            if (!empty($winners)) {
                $this->info('🎉 Winners:');
                $winnerRecords = \App\Models\LotteryWinner::where('lottery_draw_id', $pendingDraw->id)->with('user')->get();
                foreach ($winnerRecords as $winner) {
                    $username = $winner->user->username ?? 'N/A';
                    $this->line("  {$winner->prize_position}. {$winner->prize_name} - \${$winner->prize_amount} (User: {$username})");
                }
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error performing draw: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
