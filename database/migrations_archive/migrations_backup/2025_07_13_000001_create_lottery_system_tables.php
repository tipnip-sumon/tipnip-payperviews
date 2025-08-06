<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Lottery Draws Table
        Schema::create('lottery_draws', function (Blueprint $table) {
            $table->id();
            $table->string('draw_number')->unique(); // e.g., "DRAW_2025_01"
            $table->date('draw_date');
            $table->datetime('draw_time');
            $table->enum('status', ['pending', 'drawn', 'completed'])->default('pending');
            $table->decimal('total_prize_pool', 15, 2)->default(0);
            $table->integer('total_tickets_sold')->default(0);
            $table->json('prize_distribution')->nullable(); // How prizes are distributed
            $table->json('winning_numbers')->nullable(); // Winning ticket IDs
            $table->timestamps();
        });

        // Lottery Tickets Table
        Schema::create('lottery_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique(); // e.g., "TKT_2025_000001"
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('lottery_draw_id')->constrained()->onDelete('cascade');
            $table->decimal('ticket_price', 8, 2)->default(2.00);
            $table->datetime('purchased_at');
            $table->enum('status', ['active', 'expired', 'winner', 'claimed'])->default('active');
            $table->decimal('prize_amount', 15, 2)->nullable();
            $table->datetime('claimed_at')->nullable();
            $table->string('payment_method')->default('balance'); // balance, deposit, etc.
            $table->string('transaction_reference')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'lottery_draw_id']);
            $table->index(['status', 'lottery_draw_id']);
        });

        // Lottery Winners Table
        Schema::create('lottery_winners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lottery_draw_id')->constrained()->onDelete('cascade');
            $table->foreignId('lottery_ticket_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('prize_position'); // 1st, 2nd, 3rd, etc.
            $table->string('prize_name'); // "First Prize", "Second Prize", etc.
            $table->decimal('prize_amount', 15, 2);
            $table->enum('claim_status', ['pending', 'claimed', 'expired'])->default('pending');
            $table->datetime('claimed_at')->nullable();
            $table->string('claim_method')->nullable(); // auto, manual
            $table->timestamps();
        });

        // Lottery Settings Table
        Schema::create('lottery_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('ticket_price', 8, 2)->default(2.00);
            $table->integer('draw_day')->default(0); // 0 = Sunday, 1 = Monday, etc.
            $table->time('draw_time')->default('20:00:00');
            $table->boolean('is_active')->default(true);
            $table->json('prize_structure')->nullable(); // How prizes are calculated
            $table->integer('max_tickets_per_user')->default(100);
            $table->integer('min_tickets_for_draw')->default(10);
            $table->decimal('admin_commission_percentage', 5, 2)->default(10.00);
            $table->boolean('auto_draw')->default(true);
            $table->boolean('auto_prize_distribution')->default(true);
            $table->integer('ticket_expiry_hours')->default(168); // 1 week
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lottery_winners');
        Schema::dropIfExists('lottery_tickets');
        Schema::dropIfExists('lottery_draws');
        Schema::dropIfExists('lottery_settings');
    }
};
