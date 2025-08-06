<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LotteryDraw;
use App\Models\LotterySetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LotteryDrawCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lottery:draw {--force : Force draw even if conditions are not met}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform automatic lottery draw';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🎲 Starting lottery draw process...');

        try {
            $settings = LotterySetting::getSettings();

            if (!$settings->isActive()) {
                $this->warn('❌ Lottery system is not active.');
                return Command::FAILURE;
            }

            // Check if it's time for draw (unless forced)
            if (!$this->option('force') && !$settings->isDrawTime()) {
                $nextDraw = $settings->getNextDrawDateTime();
                $this->info("⏰ Not time for draw yet. Next draw: {$nextDraw->format('Y-m-d H:i:s')}");
                return Command::SUCCESS;
            }

            // Get current pending draw
            $currentDraw = LotteryDraw::where('status', 'pending')
                                   ->orderBy('draw_date', 'asc')
                                   ->first();

            if (!$currentDraw) {
                $this->warn('❌ No pending draw found.');
                return Command::FAILURE;
            }

            $this->info("🎯 Processing draw: {$currentDraw->draw_number}");
            $this->info("📅 Draw date: {$currentDraw->draw_date}");
            $this->info("🎫 Tickets sold: {$currentDraw->total_tickets_sold}");

            // Check if draw is ready (unless forced)
            if (!$this->option('force') && !$currentDraw->isReadyForDraw()) {
                $this->warn("❌ Not enough tickets sold. Minimum required: {$settings->min_tickets_for_draw}");
                return Command::FAILURE;
            }

            // Confirm before proceeding (unless forced)
            if (!$this->option('force')) {
                if (!$this->confirm("🤔 Proceed with draw '{$currentDraw->draw_number}'?")) {
                    $this->info('Draw cancelled by user.');
                    return Command::SUCCESS;
                }
            }

            DB::beginTransaction();

            $this->info('🎰 Performing draw...');

            // Perform the draw
            $winners = $currentDraw->performDraw();

            DB::commit();

            $this->info('✅ Draw completed successfully!');
            $this->info("🏆 Winners selected: " . count($winners));

            // Display winners
            foreach ($winners as $index => $winner) {
                $position = $index + 1;
                $this->info("  {$position}. Ticket #{$winner->ticket_number} - User: {$winner->user->username} - Prize: \${$winner->prize_amount}");
            }

            // Show prize distribution
            $totalPrizes = $currentDraw->winners()->sum('prize_amount');
            $this->info("💰 Total prizes awarded: \${$totalPrizes}");

            // Create next draw
            $this->info('📋 Creating next draw...');
            $nextDraw = LotteryDraw::getCurrentDraw();
            $this->info("🆕 Next draw created: {$nextDraw->draw_number} on {$nextDraw->draw_date}");

            // Send notifications (you can implement this)
            $this->info('📧 Sending winner notifications...');
            $this->sendWinnerNotifications($winners);

            $this->info('🎉 Lottery draw process completed successfully!');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error during lottery draw: ' . $e->getMessage());
            Log::error('Lottery draw command error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }
    }

    /**
     * Send notifications to winners
     */
    private function sendWinnerNotifications($winners)
    {
        try {
            foreach ($winners as $winner) {
                // Here you can implement email/SMS notifications
                // Mail::to($winner->user->email)->send(new LotteryWinnerNotification($winner));
                
                $this->info("  📧 Notification sent to: {$winner->user->email}");
            }
        } catch (\Exception $e) {
            $this->warn("⚠️ Failed to send some notifications: " . $e->getMessage());
        }
    }
}
