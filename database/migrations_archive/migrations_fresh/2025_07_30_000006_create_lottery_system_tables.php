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
        // Lottery settings table
        Schema::create('lottery_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 255)->unique();
            $table->text('value')->nullable();
            $table->string('type', 50)->default('string');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('validation_rules')->nullable();
            $table->foreignId('virtual_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('auto_ticket_generation')->default(false);
            $table->integer('auto_generation_count')->default(0);
            $table->decimal('auto_generation_price', 8, 2)->default(2.00);
            $table->timestamps();

            $table->index(['key', 'is_active']);
        });

        // Lottery draws table
        Schema::create('lottery_draws', function (Blueprint $table) {
            $table->id();
            $table->string('draw_number')->unique();
            $table->date('draw_date');
            $table->datetime('draw_time');
            $table->enum('status', ['pending', 'drawn', 'completed', 'cancelled'])->default('pending');
            $table->decimal('total_prize_pool', 15, 2)->default(0);
            $table->integer('total_tickets_sold')->default(0);
            $table->json('prize_distribution')->nullable();
            $table->json('winning_numbers')->nullable();
            $table->json('draw_configuration')->nullable();
            $table->boolean('manual_winner_selection')->default(false);
            $table->json('manual_winners')->nullable();
            $table->text('draw_notes')->nullable();
            $table->boolean('auto_draw')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('admins')->onDelete('set null');
            $table->foreignId('drawn_by')->nullable()->constrained('admins')->onDelete('set null');
            $table->timestamp('optimization_completed_at')->nullable();
            $table->json('optimization_results')->nullable();
            $table->timestamps();

            $table->index(['draw_date', 'status']);
            $table->index(['status', 'draw_time']);
        });

        // Lottery tickets table
        Schema::create('lottery_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('lottery_draw_id')->constrained()->onDelete('cascade');
            $table->decimal('ticket_price', 8, 2)->default(2.00);
            $table->datetime('purchased_at');
            $table->enum('status', ['active', 'expired', 'winner', 'claimed', 'refunded'])->default('active');
            $table->decimal('prize_amount', 15, 2)->nullable();
            $table->datetime('claimed_at')->nullable();
            $table->string('payment_method')->default('balance');
            $table->string('transaction_reference')->nullable();
            $table->boolean('is_invalidated')->default(false);
            $table->text('invalidation_reason')->nullable();
            $table->boolean('is_virtual')->default(false);
            $table->foreignId('virtual_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->json('virtual_data')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'lottery_draw_id']);
            $table->index(['status', 'lottery_draw_id']);
            $table->index(['ticket_number']);
            $table->unique(['user_id', 'lottery_draw_id', 'ticket_number']);
        });

        // Lottery winners table
        Schema::create('lottery_winners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lottery_draw_id')->constrained()->onDelete('cascade');
            $table->foreignId('lottery_ticket_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('position');
            $table->decimal('prize_amount', 15, 2);
            $table->enum('status', ['pending', 'claimed', 'expired'])->default('pending');
            $table->datetime('claimed_at')->nullable();
            $table->string('claim_method')->nullable();
            $table->text('claim_details')->nullable();
            $table->boolean('manual_selection')->default(false);
            $table->foreignId('selected_by')->nullable()->constrained('admins')->onDelete('set null');
            $table->text('selection_reason')->nullable();
            $table->timestamps();

            $table->index(['lottery_draw_id', 'position']);
            $table->index(['user_id', 'status']);
            $table->index(['lottery_ticket_id']);
        });

        // Special lottery tickets table
        Schema::create('special_lottery_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('lottery_draw_id')->constrained()->onDelete('cascade');
            $table->decimal('ticket_price', 8, 2)->default(5.00);
            $table->string('special_type', 50)->default('premium');
            $table->decimal('multiplier', 5, 2)->default(1.00);
            $table->json('special_features')->nullable();
            $table->datetime('purchased_at');
            $table->enum('status', ['active', 'expired', 'winner', 'claimed'])->default('active');
            $table->decimal('prize_amount', 15, 2)->nullable();
            $table->datetime('claimed_at')->nullable();
            $table->string('payment_method')->default('balance');
            $table->string('transaction_reference')->nullable();
            $table->foreignId('related_lottery_ticket_id')->nullable()->constrained('lottery_tickets')->onDelete('set null');
            $table->timestamps();

            $table->index(['user_id', 'lottery_draw_id']);
            $table->index(['status', 'special_type']);
            $table->index(['ticket_number']);
        });

        // Special ticket transfers table
        Schema::create('special_ticket_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('to_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('special_lottery_ticket_id')->constrained()->onDelete('cascade');
            $table->decimal('transfer_fee', 8, 2)->default(0.00);
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->text('transfer_reason')->nullable();
            $table->timestamp('transferred_at')->nullable();
            $table->string('transaction_reference')->nullable();
            $table->timestamps();

            $table->index(['from_user_id', 'status']);
            $table->index(['to_user_id', 'status']);
            $table->index(['special_lottery_ticket_id']);
        });

        // Lottery daily summaries table
        Schema::create('lottery_daily_summaries', function (Blueprint $table) {
            $table->id();
            $table->date('summary_date');
            $table->integer('total_tickets_sold')->default(0);
            $table->decimal('total_sales_amount', 15, 2)->default(0.00);
            $table->integer('total_participants')->default(0);
            $table->decimal('total_prizes_awarded', 15, 2)->default(0.00);
            $table->integer('total_winners')->default(0);
            $table->json('draw_statistics')->nullable();
            $table->timestamps();

            $table->unique(['summary_date']);
            $table->index(['summary_date', 'total_sales_amount']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lottery_daily_summaries');
        Schema::dropIfExists('special_ticket_transfers');
        Schema::dropIfExists('special_lottery_tickets');
        Schema::dropIfExists('lottery_winners');
        Schema::dropIfExists('lottery_tickets');
        Schema::dropIfExists('lottery_draws');
        Schema::dropIfExists('lottery_settings');
    }
};
