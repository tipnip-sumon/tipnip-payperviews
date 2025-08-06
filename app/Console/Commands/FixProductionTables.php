<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class FixProductionTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:production-tables';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix missing production database tables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking for missing production tables...');

        // Check and create special_ticket_transfers table
        if (!Schema::hasTable('special_ticket_transfers')) {
            $this->warn('❌ special_ticket_transfers table is missing. Creating...');
            $this->createSpecialTicketTransfersTable();
            $this->info('✅ special_ticket_transfers table created successfully!');
        } else {
            $this->info('✅ special_ticket_transfers table already exists.');
        }

        // Check and initialize lottery settings if missing
        $this->checkLotterySettings();

        $this->info('🎉 Production table fixes completed successfully!');

        return 0;
    }

    private function checkLotterySettings()
    {
        try {
            $settingsCount = DB::table('lottery_settings')->count();
            if ($settingsCount == 0) {
                $this->warn('❌ lottery_settings table is empty. Initializing default settings...');
                
                // Create default lottery settings
                \App\Models\LotterySetting::getDefaultSettings();
                
                $this->info('✅ Default lottery settings created successfully!');
            } else {
                $this->info('✅ lottery_settings table has data.');
            }
        } catch (\Exception $e) {
            $this->error('❌ Error checking lottery settings: ' . $e->getMessage());
        }
    }

    private function createSpecialTicketTransfersTable()
    {
        Schema::create('special_ticket_transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('special_ticket_id');
            $table->unsignedBigInteger('from_user_id');
            $table->unsignedBigInteger('to_user_id')->nullable();
            $table->decimal('transfer_amount', 10, 2)->default(0);
            $table->enum('transfer_type', ['gift', 'sale', 'trade'])->default('gift');
            $table->text('transfer_message')->nullable();
            $table->enum('status', ['pending', 'completed', 'cancelled', 'expired'])->default('pending');
            $table->timestamp('transfer_requested_at')->nullable();
            $table->timestamp('transfer_completed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('transfer_code')->unique()->nullable();
            $table->boolean('requires_acceptance')->default(false);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('special_ticket_id')->references('id')->on('special_lottery_tickets')->onDelete('cascade');
            $table->foreign('from_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('to_user_id')->references('id')->on('users')->onDelete('cascade');

            // Indexes
            $table->index(['from_user_id', 'status']);
            $table->index(['to_user_id', 'status']);
            $table->index(['status', 'expires_at']);
        });
    }
}
