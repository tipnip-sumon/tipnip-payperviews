<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - EXACT PRODUCTION STRUCTURE (Part 3)
     * Lottery, Video, Support and remaining business tables
     */
    public function up(): void
    {
        // EXACT PRODUCTION: video_links table (15 columns)
        Schema::create('video_links', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 191)->nullable();
            $table->text('description')->nullable();
            $table->text('video_url')->nullable(false)->comment('ads_link');
            $table->integer('duration')->nullable();
            $table->string('ads_type', 191)->nullable();
            $table->string('category', 191)->default('general')->nullable(false);
            $table->string('country', 191)->nullable();
            $table->string('source_platform', 191)->nullable()->comment('source');
            $table->integer('views_count')->default(0)->nullable(false);
            $table->integer('clicks_count')->default(0)->nullable(false);
            $table->decimal('cost_per_click', 8, 2)->nullable()->comment('ads_amount');
            $table->enum('status', ['active', 'inactive', 'paused', 'completed'])->default('active')->nullable(false);
            $table->timestamps();
        });

        // EXACT PRODUCTION: daily_video_assignments table (13 columns)
        Schema::create('daily_video_assignments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned()->nullable(false);
            $table->bigInteger('video_link_id')->unsigned()->nullable();
            $table->date('assignment_date')->nullable(false);
            $table->boolean('is_watched')->default(0)->nullable();
            $table->timestamp('watched_at')->nullable();
            $table->decimal('earning_amount', 20, 8)->nullable();
            $table->timestamps();
            $table->json('video_ids')->nullable()->comment('JSON array of assigned video IDs');
            $table->json('watched_video_ids')->nullable()->comment('JSON array of watched video IDs');
            $table->integer('total_videos')->default(0)->nullable(false)->comment('Total number of videos assigned');
            $table->integer('watched_count')->default(0)->nullable(false)->comment('Number of videos watched');
        });

        // EXACT PRODUCTION: video_views table (15 columns)
        Schema::create('video_views', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned()->nullable(false);
            $table->date('view_date')->nullable();
            $table->string('view_type', 191)->default('individual')->nullable(false);
            $table->json('video_data')->nullable();
            $table->decimal('total_earned', 10, 8)->default(0.00000000)->nullable(false);
            $table->integer('total_videos')->default(0)->nullable(false);
            $table->bigInteger('video_link_id')->unsigned()->nullable();
            $table->decimal('earned_amount', 10, 8)->nullable();
            $table->timestamp('viewed_at')->nullable();
            $table->string('ip_address', 191)->nullable();
            $table->text('device_info')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });

        // EXACT PRODUCTION: user_video_views table (10 columns)
        Schema::create('user_video_views', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned()->nullable(false);
            $table->bigInteger('video_link_id')->unsigned()->nullable(false);
            $table->decimal('earning_amount', 10, 2)->default(0.00)->nullable(false);
            $table->integer('view_duration')->default(0)->nullable(false);
            $table->string('ip_address', 191)->nullable();
            $table->text('user_agent')->nullable();
            $table->boolean('is_completed')->default(0)->nullable(false);
            $table->timestamps();
        });

        // EXACT PRODUCTION: lottery_settings table (36 columns)
        Schema::create('lottery_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('ticket_price', 8, 2)->default(2.00)->nullable(false);
            $table->integer('draw_day')->default(0)->nullable(false);
            $table->time('draw_time')->default('20:00:00')->nullable(false);
            $table->boolean('is_active')->default(1)->nullable(false);
            $table->json('prize_structure')->nullable();
            $table->integer('max_tickets_per_user')->default(100)->nullable(false);
            $table->integer('min_tickets_for_draw')->default(10)->nullable(false);
            $table->decimal('admin_commission_percentage', 5, 2)->default(10.00)->nullable(false);
            $table->boolean('auto_draw')->default(1)->nullable(false);
            $table->boolean('auto_prize_distribution')->default(1)->nullable(false);
            $table->boolean('auto_generate_draws')->default(1)->nullable(false);
            $table->enum('auto_generation_frequency', ['daily', 'weekly', 'monthly'])->default('weekly')->nullable(false);
            $table->json('auto_generation_schedule')->nullable();
            $table->boolean('enable_virtual_tickets')->default(1)->nullable(false);
            $table->integer('min_virtual_tickets')->default(100)->nullable(false);
            $table->integer('max_virtual_tickets')->default(1000)->nullable(false);
            $table->decimal('virtual_ticket_percentage', 5, 2)->default(80.00)->nullable(false);
            $table->boolean('enable_manual_winner_selection')->default(1)->nullable(false);
            $table->json('default_winner_pool')->nullable();
            $table->boolean('auto_execute_draws')->default(1)->nullable(false);
            $table->integer('auto_execute_delay_minutes')->default(0)->nullable(false);
            $table->dateTime('next_auto_draw')->nullable();
            $table->integer('ticket_expiry_hours')->default(168)->nullable(false);
            $table->integer('auto_claim_days')->default(30)->nullable(false);
            $table->boolean('auto_refund_cancelled')->default(1)->nullable(false);
            $table->integer('prize_claim_deadline')->default(30)->nullable(false);
            $table->boolean('allow_multiple_winners_per_place')->default(0)->nullable(false);
            $table->enum('prize_distribution_type', ['percentage', 'fixed_amount'])->default('percentage')->nullable(false);
            $table->boolean('manual_winner_selection')->default(0)->nullable(false);
            $table->boolean('show_virtual_tickets')->default(0)->nullable(false);
            $table->integer('virtual_ticket_multiplier')->default(100)->nullable(false);
            $table->integer('virtual_ticket_base')->default(0)->nullable(false);
            $table->bigInteger('virtual_user_id')->unsigned()->nullable();
            $table->timestamps();
        });

        // EXACT PRODUCTION: lottery_draws table (25 columns)
        Schema::create('lottery_draws', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('draw_number', 191)->nullable(false);
            $table->date('draw_date')->nullable(false);
            $table->dateTime('draw_time')->nullable(false);
            $table->enum('status', ['pending', 'drawn', 'completed'])->default('pending')->nullable(false);
            $table->boolean('auto_draw')->default(0)->nullable(false);
            $table->boolean('auto_prize_distribution')->default(1)->nullable(false);
            $table->decimal('total_prize_pool', 15, 2)->default(0.00)->nullable(false);
            $table->decimal('admin_commission_percentage', 5, 2)->default(10.00)->nullable(false);
            $table->integer('total_tickets_sold')->default(0)->nullable(false);
            $table->integer('max_tickets')->default(1000)->nullable(false);
            $table->decimal('ticket_price', 8, 2)->default(2.00)->nullable(false);
            $table->integer('virtual_tickets_sold')->default(0)->nullable(false);
            $table->integer('display_tickets_sold')->default(0)->nullable(false);
            $table->boolean('manual_winner_selection_enabled')->default(0)->nullable(false);
            $table->boolean('has_manual_winners')->default(0)->nullable(false);
            $table->json('manually_selected_winners')->nullable();
            $table->enum('prize_distribution_type', ['percentage', 'fixed_amount'])->default('percentage')->nullable(false);
            $table->boolean('allow_multiple_winners_per_place')->default(0)->nullable(false);
            $table->json('prize_distribution')->nullable();
            $table->json('winning_numbers')->nullable();
            $table->timestamps();
            $table->timestamp('optimized_at')->nullable();
            $table->boolean('cleanup_performed')->default(0)->nullable(false);
        });

        // UNIFIED LOTTERY SYSTEM: lottery_tickets table (36 columns) - Updated for unified structure with hexadecimal format support
        Schema::create('lottery_tickets', function (Blueprint $table) {
            // Primary key and basic ticket info
            $table->bigIncrements('id');
            $table->string('ticket_number', 191)->nullable(false);
            $table->bigInteger('user_id')->unsigned()->nullable(false);
            $table->bigInteger('lottery_draw_id')->unsigned()->nullable(false);
            $table->decimal('ticket_price', 8, 2)->default(2.00)->nullable(false);
            $table->dateTime('purchased_at')->nullable(false);
            $table->enum('status', ['active', 'expired', 'winner', 'claimed', 'refunded'])->default('active')->nullable();
            
            // Unified token system columns
            $table->enum('token_type', ['lottery', 'special', 'sponsor'])->default('lottery')->nullable(false);
            $table->bigInteger('sponsor_user_id')->unsigned()->nullable();
            $table->bigInteger('referral_user_id')->unsigned()->nullable();
            $table->bigInteger('current_owner_id')->unsigned()->nullable();
            $table->bigInteger('original_owner_id')->unsigned()->nullable();
            $table->boolean('is_valid_token')->default(1)->nullable(false);
            $table->boolean('is_transferable')->default(0)->nullable(false);
            $table->integer('transfer_count')->default(0)->nullable(false);
            $table->timestamp('last_transferred_at')->nullable();
            
            // Financial and usage tracking
            $table->decimal('token_discount_amount', 10, 2)->default(0.00)->nullable(false);
            $table->bigInteger('used_for_plan_id')->unsigned()->nullable();
            $table->decimal('early_usage_bonus', 10, 2)->default(0.00)->nullable(false);
            $table->timestamp('token_expires_at')->nullable();
            $table->decimal('refund_amount', 10, 2)->default(0.00)->nullable(false);
            $table->timestamp('used_as_token_at')->nullable();
            
            // Prize and claim tracking
            $table->decimal('prize_amount', 15, 2)->nullable();
            $table->dateTime('claimed_at')->nullable();
            
            // Payment and transaction info
            $table->string('payment_method', 191)->default('balance')->nullable(false);
            $table->string('transaction_reference', 191)->nullable();
            
            // Virtual ticket support
            $table->boolean('is_virtual')->default(0)->nullable(false);
            $table->string('virtual_user_type', 191)->nullable();
            $table->json('virtual_metadata')->nullable();
            
            // Invalidation tracking
            $table->boolean('is_invalidated')->default(0)->nullable(false);
            $table->timestamp('invalidated_at')->nullable();
            $table->string('invalidation_reason', 191)->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('sponsor_user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('referral_user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('current_owner_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('original_owner_id')->references('id')->on('users')->onDelete('set null');
            
            // Performance indexes
            $table->index('token_type');
            $table->index('sponsor_user_id');
            $table->index('referral_user_id');
            $table->index('current_owner_id');
            $table->index('is_valid_token');
            $table->index('token_expires_at');
        });

        // EXACT PRODUCTION: lottery_winners table (17 columns)
        Schema::create('lottery_winners', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('lottery_draw_id')->unsigned()->nullable(false);
            $table->bigInteger('lottery_ticket_id')->unsigned()->nullable(false);
            $table->bigInteger('user_id')->unsigned()->nullable(false);
            $table->integer('prize_position')->nullable(false);
            $table->integer('winner_index')->default(1)->nullable(false);
            $table->string('prize_name', 191)->nullable(false);
            $table->decimal('prize_amount', 15, 2)->nullable(false);
            $table->enum('claim_status', ['pending', 'claimed', 'expired'])->default('pending')->nullable(false);
            $table->boolean('prize_distributed')->default(0)->nullable(false);
            $table->boolean('is_manual_selection')->default(0)->nullable(false);
            $table->timestamp('selected_at')->nullable();
            $table->bigInteger('selected_by')->unsigned()->nullable();
            $table->dateTime('claimed_at')->nullable();
            $table->string('claim_method', 191)->nullable();
            $table->timestamps();
        });

        // EXACT PRODUCTION: lottery_daily_summaries table (12 columns)
        Schema::create('lottery_daily_summaries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned()->nullable(false);
            $table->date('summary_date')->nullable(false);
            $table->integer('total_tickets_purchased')->default(0)->nullable(false);
            $table->decimal('total_amount_spent', 15, 8)->default(0.00000000)->nullable(false);
            $table->decimal('total_winnings', 15, 8)->default(0.00000000)->nullable(false);
            $table->decimal('net_result', 15, 8)->default(0.00000000)->nullable(false);
            $table->integer('draws_participated')->default(0)->nullable(false);
            $table->integer('winning_tickets')->default(0)->nullable(false);
            $table->json('ticket_details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lottery_daily_summaries');
        Schema::dropIfExists('lottery_winners');
        Schema::dropIfExists('lottery_tickets');
        Schema::dropIfExists('lottery_draws');
        Schema::dropIfExists('lottery_settings');
        Schema::dropIfExists('user_video_views');
        Schema::dropIfExists('video_views');
        Schema::dropIfExists('daily_video_assignments');
        Schema::dropIfExists('video_links');
    }
};
