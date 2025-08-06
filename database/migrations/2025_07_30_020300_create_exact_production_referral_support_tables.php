<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations - EXACT PRODUCTION STRUCTURE (Part 4)
     * Referrals, Support, Messages, Notifications and remaining tables
     */
    public function up(): void
    {
        // EXACT PRODUCTION: referrals table (7 columns)
        Schema::create('referrals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('commission_type', 40)->nullable();
            $table->integer('level')->default(0)->nullable(false);
            $table->decimal('percent', 5, 2)->default(0.00)->nullable(false);
            $table->boolean('status')->default(1)->nullable(false);
            $table->timestamps();
        });

        // EXACT PRODUCTION: referral_commissions table (14 columns)
        Schema::create('referral_commissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('earner_user_id')->unsigned()->nullable(false);
            $table->bigInteger('referrer_user_id')->unsigned()->nullable(false);
            $table->bigInteger('daily_video_assignment_id')->unsigned()->nullable();
            $table->integer('level')->nullable(false);
            $table->decimal('original_earning', 10, 6)->nullable(false);
            $table->decimal('commission_percentage', 5, 2)->nullable(false);
            $table->decimal('commission_amount', 10, 6)->nullable(false);
            $table->string('commission_type', 191)->default('video_earning')->nullable(false);
            $table->string('earning_type', 191)->default('video_earning')->nullable(false);
            $table->date('earning_date')->nullable();
            $table->timestamp('distributed_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->nullable(false);
            $table->timestamps();
        });

        // EXACT PRODUCTION: commission_level_settings table (7 columns)
        Schema::create('commission_level_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('level')->nullable(false);
            $table->decimal('percentage', 5, 2)->nullable(false);
            $table->boolean('is_active')->default(1)->nullable(false);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // EXACT PRODUCTION: daily_commission_summaries table (20 columns)
        Schema::create('daily_commission_summaries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date')->nullable(false);
            $table->integer('total_users')->default(0)->nullable(false);
            $table->integer('total_assignments')->default(0)->nullable(false);
            $table->decimal('total_distributed', 15, 6)->default(0.000000)->nullable(false);
            $table->integer('total_users_earned')->default(0)->nullable(false);
            $table->integer('total_levels_processed')->default(0)->nullable(false);
            $table->decimal('level_1_total', 15, 6)->default(0.000000)->nullable(false);
            $table->decimal('level_2_total', 15, 6)->default(0.000000)->nullable(false);
            $table->decimal('level_3_total', 15, 6)->default(0.000000)->nullable(false);
            $table->decimal('level_4_total', 15, 6)->default(0.000000)->nullable(false);
            $table->decimal('level_5_total', 15, 6)->default(0.000000)->nullable(false);
            $table->decimal('level_6_total', 15, 6)->default(0.000000)->nullable(false);
            $table->decimal('level_7_total', 15, 6)->default(0.000000)->nullable(false);
            $table->decimal('level_8_total', 15, 6)->default(0.000000)->nullable(false);
            $table->decimal('level_9_total', 15, 6)->default(0.000000)->nullable(false);
            $table->decimal('level_10_total', 15, 6)->default(0.000000)->nullable(false);
            $table->timestamp('processed_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->nullable(false);
            $table->timestamps();
        });

        // EXACT PRODUCTION: first_purchase_commissions table (13 columns)
        Schema::create('first_purchase_commissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('referral_user_id')->unsigned()->nullable(false);
            $table->bigInteger('sponsor_user_id')->unsigned()->nullable(false);
            $table->bigInteger('plan_id')->unsigned()->nullable(false);
            $table->decimal('purchase_amount', 10, 2)->nullable(false);
            $table->decimal('commission_amount', 10, 2)->default(25.00)->nullable(false);
            $table->boolean('commission_paid')->default(0)->nullable(false);
            $table->boolean('special_ticket_issued')->default(0)->nullable(false);
            $table->bigInteger('special_ticket_id')->unsigned()->nullable();
            $table->dateTime('processed_at')->nullable(false);
            $table->string('transaction_reference', 191)->nullable(false);
            $table->timestamps();
        });

        // EXACT PRODUCTION: referral_user_benefits table (13 columns)
        Schema::create('referral_user_benefits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned()->nullable(false);
            $table->integer('qualified_referrals_count')->default(0)->nullable(false);
            $table->decimal('transfer_bonus_percentage', 5, 2)->default(0.00)->nullable(false);
            $table->decimal('receive_bonus_percentage', 5, 2)->default(0.00)->nullable(false);
            $table->decimal('withdraw_reduction_percentage', 5, 2)->default(0.00)->nullable(false);
            $table->decimal('total_bonuses_earned', 15, 2)->default(0.00)->nullable(false);
            $table->boolean('is_qualified')->default(0)->nullable(false);
            $table->boolean('is_active')->default(1)->nullable(false);
            $table->dateTime('qualified_at')->nullable();
            $table->dateTime('last_recalculated_at')->nullable();
            $table->timestamps();
        });

        // EXACT PRODUCTION: referral_bonus_transactions table (10 columns)
        Schema::create('referral_bonus_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_benefit_id')->unsigned()->nullable(false);
            $table->string('type', 191)->nullable(false);
            $table->decimal('original_amount', 15, 2)->nullable(false);
            $table->decimal('percentage_used', 5, 2)->nullable(false);
            $table->decimal('amount', 15, 2)->nullable(false);
            $table->string('related_transaction_id', 191)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // EXACT PRODUCTION: special_lottery_tickets table (27 columns)
        Schema::create('special_lottery_tickets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ticket_number', 191)->nullable(false);
            $table->bigInteger('user_id')->unsigned()->nullable(false);
            $table->bigInteger('sponsor_user_id')->unsigned()->nullable(false);
            $table->bigInteger('referral_user_id')->unsigned()->nullable(false);
            $table->bigInteger('current_owner_id')->unsigned()->nullable(false);
            $table->bigInteger('original_owner_id')->unsigned()->nullable(false);
            $table->bigInteger('lottery_draw_id')->unsigned()->nullable();
            $table->decimal('ticket_price', 10, 2)->default(2.00)->nullable(false);
            $table->enum('status', ['active', 'expired', 'winner', 'lost', 'refunded', 'used_as_token'])->default('active')->nullable(false);
            $table->decimal('prize_amount', 10, 2)->nullable();
            $table->decimal('refund_amount', 10, 2)->default(1.00)->nullable(false);
            $table->dateTime('purchased_at')->nullable(false);
            $table->dateTime('claimed_at')->nullable();
            $table->dateTime('used_as_token_at')->nullable();
            $table->decimal('token_discount_amount', 10, 2)->nullable();
            $table->bigInteger('used_for_plan_id')->unsigned()->nullable();
            $table->bigInteger('related_lottery_ticket_id')->unsigned()->nullable();
            $table->decimal('early_usage_bonus', 5, 2)->default(0.00)->nullable(false);
            $table->boolean('is_valid_token')->default(1)->nullable(false);
            $table->dateTime('token_expires_at')->nullable();
            $table->string('transaction_reference', 191)->nullable();
            $table->boolean('is_transferable')->default(1)->nullable(false);
            $table->integer('transfer_count')->default(0)->nullable(false);
            $table->dateTime('last_transferred_at')->nullable();
            $table->timestamps();
        });

        // EXACT PRODUCTION: special_ticket_transfers table (15 columns)
        Schema::create('special_ticket_transfers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('special_ticket_id')->unsigned()->nullable(false);
            $table->bigInteger('from_user_id')->unsigned()->nullable(false);
            $table->bigInteger('to_user_id')->unsigned()->nullable(false);
            $table->decimal('transfer_amount', 10, 2)->default(0.00)->nullable(false);
            $table->string('transfer_type', 191)->default('gift')->nullable(false);
            $table->text('transfer_message')->nullable();
            $table->enum('status', ['pending', 'completed', 'cancelled', 'rejected'])->default('pending')->nullable(false);
            $table->dateTime('transfer_requested_at')->nullable(false);
            $table->dateTime('transfer_completed_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->string('transfer_code', 191)->nullable();
            $table->boolean('requires_acceptance')->default(1)->nullable(false);
            $table->timestamps();
        });

        // EXACT PRODUCTION: support_tickets table (12 columns)
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned()->nullable(false);
            $table->string('name', 191)->nullable(false);
            $table->string('email', 191)->nullable(false);
            $table->string('ticket', 40)->nullable(false);
            $table->string('subject', 191)->nullable(false);
            $table->tinyInteger('status')->default(0)->nullable(false)->comment('0: Open, 1: Answered, 2: Customer Reply, 3: Closed');
            $table->tinyInteger('priority')->default(2)->nullable(false)->comment('1: Low, 2: Medium, 3: High');
            $table->timestamp('last_reply')->nullable();
            $table->bigInteger('admin_id')->unsigned()->nullable();
            $table->timestamps();
        });

        // EXACT PRODUCTION: support_messages table (7 columns)
        Schema::create('support_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('supportticket_id')->unsigned()->nullable(false);
            $table->bigInteger('admin_id')->unsigned()->nullable();
            $table->longText('message')->nullable(false);
            $table->json('attachments')->nullable();
            $table->timestamps();
        });

        // EXACT PRODUCTION: messages table (18 columns)
        Schema::create('messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('from_user_id')->unsigned()->nullable(false);
            $table->bigInteger('to_user_id')->unsigned()->nullable(false);
            $table->string('subject', 191)->nullable(false);
            $table->text('message')->nullable(false);
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal')->nullable();
            $table->string('category', 191)->nullable();
            $table->boolean('is_read')->default(0)->nullable(false);
            $table->boolean('is_starred')->default(0)->nullable(false);
            $table->timestamp('read_at')->nullable();
            $table->bigInteger('reply_to_id')->unsigned()->nullable();
            $table->string('attachment_path', 191)->nullable();
            $table->text('attachments')->nullable();
            $table->text('metadata')->nullable();
            $table->enum('message_type', ['private', 'system', 'broadcast', 'support'])->default('private')->nullable();
            $table->enum('status', ['active', 'deleted', 'archived', 'open', 'pending', 'closed', 'resolved'])->default('active')->nullable();
            $table->timestamps();
        });

        // EXACT PRODUCTION: user_notifications table (16 columns)
        Schema::create('user_notifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned()->nullable(false);
            $table->string('type', 50)->default('info')->nullable(false);
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal')->nullable(false);
            $table->string('title', 191)->nullable(false);
            $table->text('message')->nullable(false);
            $table->string('icon', 191)->nullable();
            $table->json('data')->nullable();
            $table->json('metadata')->nullable();
            $table->boolean('read')->default(0)->nullable(false);
            $table->timestamp('read_at')->nullable();
            $table->string('action_url', 191)->nullable();
            $table->string('action_text', 191)->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        // EXACT PRODUCTION: user_session_notifications table (15 columns)
        Schema::create('user_session_notifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned()->nullable(false);
            $table->string('username', 191)->nullable();
            $table->string('type', 191)->default('new_login_detected')->nullable(false);
            $table->string('title', 191)->nullable(false);
            $table->text('message')->nullable(false);
            $table->string('new_login_ip', 191)->nullable();
            $table->string('new_login_device', 191)->nullable();
            $table->string('new_login_location', 191)->nullable();
            $table->string('old_session_ip', 191)->nullable();
            $table->string('old_session_duration', 191)->nullable();
            $table->boolean('is_read')->default(0)->nullable(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        // EXACT PRODUCTION: user_logins table (12 columns)
        Schema::create('user_logins', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned()->default(0)->nullable(false);
            $table->string('user_ip', 40)->nullable()->comment('IPv4 or IPv6 address');
            $table->string('city', 40)->nullable();
            $table->string('country', 40)->nullable();
            $table->string('country_code', 40)->nullable();
            $table->string('longitude', 40)->nullable();
            $table->string('latitude', 40)->nullable();
            $table->string('browser', 40)->nullable();
            $table->string('os', 40)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_logins');
        Schema::dropIfExists('user_session_notifications');
        Schema::dropIfExists('user_notifications');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('support_messages');
        Schema::dropIfExists('support_tickets');
        Schema::dropIfExists('special_ticket_transfers');
        Schema::dropIfExists('special_lottery_tickets');
        Schema::dropIfExists('referral_bonus_transactions');
        Schema::dropIfExists('referral_user_benefits');
        Schema::dropIfExists('first_purchase_commissions');
        Schema::dropIfExists('daily_commission_summaries');
        Schema::dropIfExists('commission_level_settings');
        Schema::dropIfExists('referral_commissions');
        Schema::dropIfExists('referrals');
    }
};
