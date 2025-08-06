<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LotteryTicket;
use App\Models\LotteryWinner;
use App\Models\LotteryDraw;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanupPendingDrawWinners extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'lottery:cleanup-pending-winners {--dry-run : Show what would be cleaned without making changes}';

    /**
     * The console command description.
     */
    protected $description = 'Clean up incorrect winner data from pending lottery draws';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Scanning for incorrect winner data from pending draws...');
        
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->warn('🔒 DRY RUN MODE - No changes will be made');
        }

        // Find lottery tickets marked as winners but in pending draws
        $incorrectWinningTickets = LotteryTicket::where('status', 'winner')
            ->whereHas('lotteryDraw', function($query) {
                $query->where('status', 'pending');
            })
            ->with(['lotteryDraw', 'user'])
            ->get();

        // Find lottery winner records from pending draws
        $incorrectWinnerRecords = LotteryWinner::whereHas('lotteryDraw', function($query) {
                $query->where('status', 'pending');
            })
            ->with(['lotteryDraw', 'lotteryTicket', 'user'])
            ->get();

        $this->info("Found {$incorrectWinningTickets->count()} incorrect winning tickets");
        $this->info("Found {$incorrectWinnerRecords->count()} incorrect winner records");

        if ($incorrectWinningTickets->count() === 0 && $incorrectWinnerRecords->count() === 0) {
            $this->info('✅ No cleanup needed - all winner data is correct!');
            return;
        }

        // Display details of what will be cleaned
        if ($incorrectWinningTickets->count() > 0) {
            $this->warn("\n📋 Incorrect Winning Tickets (from pending draws):");
            $this->table(
                ['Ticket ID', 'Ticket Number', 'User', 'Draw ID', 'Draw Status', 'Prize Amount'],
                $incorrectWinningTickets->map(function($ticket) {
                    return [
                        $ticket->id,
                        $ticket->ticket_number,
                        $ticket->user->username ?? 'N/A',
                        $ticket->lottery_draw_id,
                        $ticket->lotteryDraw->status ?? 'N/A',
                        '$' . number_format($ticket->prize_amount ?? 0, 2)
                    ];
                })->toArray()
            );
        }

        if ($incorrectWinnerRecords->count() > 0) {
            $this->warn("\n📋 Incorrect Winner Records (from pending draws):");
            $this->table(
                ['Winner ID', 'User', 'Draw ID', 'Draw Status', 'Position', 'Prize Amount'],
                $incorrectWinnerRecords->map(function($winner) {
                    return [
                        $winner->id,
                        $winner->user->username ?? 'N/A',
                        $winner->lottery_draw_id,
                        $winner->lotteryDraw->status ?? 'N/A',
                        $winner->prize_position ?? 'N/A',
                        '$' . number_format($winner->prize_amount ?? 0, 2)
                    ];
                })->toArray()
            );
        }

        if ($isDryRun) {
            $this->info("\n🔍 DRY RUN COMPLETE - No changes were made");
            $this->info("Run without --dry-run to perform the cleanup");
            return;
        }

        if (!$this->confirm('Do you want to proceed with cleaning up this incorrect data?')) {
            $this->info('❌ Cleanup cancelled');
            return;
        }

        DB::beginTransaction();

        try {
            $cleanedTickets = 0;
            $cleanedWinners = 0;

            // Clean up incorrect winning tickets
            foreach ($incorrectWinningTickets as $ticket) {
                // Reset ticket status to active (remove winner status)
                $ticket->update([
                    'status' => 'active',
                    'prize_amount' => null,
                    'claimed_at' => null
                ]);

                $cleanedTickets++;

                Log::info('Cleaned incorrect winning ticket', [
                    'ticket_id' => $ticket->id,
                    'ticket_number' => $ticket->ticket_number,
                    'draw_id' => $ticket->lottery_draw_id,
                    'user_id' => $ticket->user_id
                ]);
            }

            // Clean up incorrect winner records
            foreach ($incorrectWinnerRecords as $winner) {
                // Delete the winner record entirely
                $winner->delete();

                $cleanedWinners++;

                Log::info('Removed incorrect winner record', [
                    'winner_id' => $winner->id,
                    'draw_id' => $winner->lottery_draw_id,
                    'user_id' => $winner->user_id,
                    'prize_amount' => $winner->prize_amount
                ]);
            }

            DB::commit();

            $this->info("✅ Cleanup completed successfully!");
            $this->info("🎫 Cleaned {$cleanedTickets} incorrect winning tickets");
            $this->info("🏆 Removed {$cleanedWinners} incorrect winner records");

            // Show summary of pending draws
            $pendingDraws = LotteryDraw::where('status', 'pending')->count();
            $this->info("📊 Current status: {$pendingDraws} pending draws remain");

        } catch (\Exception $e) {
            DB::rollBack();

            $this->error("❌ Cleanup failed: " . $e->getMessage());
            Log::error('Lottery cleanup failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
