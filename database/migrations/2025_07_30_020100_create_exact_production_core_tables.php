<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations - EXACT PRODUCTION STRUCTURE (Part 2)
     * Core business tables with exact production specifications
     */
    public function up(): void
    {
        // EXACT PRODUCTION: users table (44 columns)
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('firstname', 40)->nullable();
            $table->string('lastname', 40)->nullable();
            $table->string('username', 40)->nullable(false);
            $table->string('referral_hash', 64)->nullable();
            $table->string('email', 191)->nullable(false);
            $table->string('avatar', 191)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->decimal('balance', 10, 4)->default(0.0000)->nullable(false);
            $table->string('country', 191)->nullable();
            $table->string('country_code', 40)->nullable();
            $table->string('mobile', 40)->nullable();
            $table->integer('ref_by')->unsigned()->default(0)->nullable(false);
            $table->decimal('deposit_wallet', 28, 8)->default(0.00000000)->nullable(false);
            $table->decimal('interest_wallet', 28, 8)->default(0.00000000)->nullable(false);
            $table->string('password', 255)->nullable(false);
            $table->string('image', 255)->nullable();
            $table->text('address')->nullable()->comment('contains full address');
            $table->boolean('status')->default(1)->nullable(false)->comment('0: banned, 1: active');
            $table->text('kyc_data')->nullable();
            $table->boolean('kv')->default(0)->nullable(false)->comment('0: KYC Unverified, 2: KYC pending, 1: KYC verified');
            $table->boolean('ev')->default(0)->nullable(false)->comment('0: email unverified, 1: email verified');
            $table->boolean('sv')->default(0)->nullable(false)->comment('0: mobile unverified, 1: mobile verified');
            $table->boolean('profile_complete')->default(0)->nullable(false);
            $table->string('ver_code', 40)->nullable()->comment('stores verification code');
            $table->dateTime('ver_code_send_at')->nullable()->comment('verification send time');
            $table->boolean('ts')->default(0)->nullable(false)->comment('0: 2fa off, 1: 2fa on');
            $table->boolean('tv')->default(1)->nullable(false)->comment('0: 2fa unverified, 1: 2fa verified');
            $table->string('tsc', 255)->nullable();
            $table->string('ban_reason', 255)->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->string('current_session_id', 191)->nullable();
            $table->timestamp('session_created_at')->nullable();
            $table->string('session_ip_address', 191)->nullable();
            $table->text('session_user_agent')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->string('last_login_user_agent', 191)->nullable();
            $table->string('last_login_ip', 45)->nullable();
            $table->integer('login_attempts')->default(0)->nullable(false);
            $table->timestamp('locked_until')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
            $table->timestamp('last_seen')->nullable();
        });

        // EXACT PRODUCTION: transactions table (13 columns)
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned()->nullable(false);
            $table->decimal('amount', 28, 8)->default(0.00000000)->nullable(false);
            $table->decimal('charge', 28, 8)->default(0.00000000)->nullable(false);
            $table->decimal('post_balance', 28, 8)->default(0.00000000)->nullable(false);
            $table->string('trx_type', 40)->nullable();
            $table->string('trx', 40)->nullable();
            $table->string('details', 255)->nullable();
            $table->string('remark', 40)->nullable();
            $table->string('note', 191)->nullable();
            $table->string('wallet_type', 40)->nullable();
            $table->timestamps();
        });

        // EXACT PRODUCTION: plans table (20 columns)
        Schema::create('plans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 40)->nullable(false);
            $table->decimal('minimum', 28, 8)->default(0.00000000)->nullable(false);
            $table->decimal('maximum', 28, 8)->default(0.00000000)->nullable(false);
            $table->decimal('fixed_amount', 28, 8)->default(0.00000000)->nullable(false);
            $table->decimal('interest', 28, 8)->default(0.00000000)->nullable(false);
            $table->boolean('interest_type')->default(0)->nullable()->comment('1 = \'%\' / 0 =\'currency\'');
            $table->string('time', 40)->default('0')->nullable(false)->comment('e.g., 30 days, 60 days, etc.');
            $table->string('time_name', 40)->nullable()->comment('e.g., days, weeks, months, years');
            $table->boolean('status')->default(1)->nullable(false);
            $table->boolean('featured')->default(0)->nullable(false);
            $table->boolean('capital_back')->default(0)->nullable();
            $table->boolean('lifetime')->default(0)->nullable();
            $table->string('repeat_time', 40)->nullable();
            $table->integer('daily_video_limit')->default(5)->nullable()->comment('Number of daily video assignments allowed');
            $table->text('description')->nullable();
            $table->decimal('video_earning_rate', 8, 4)->default(0.0010)->nullable(false);
            $table->boolean('video_access_enabled')->default(1)->nullable(false);
            $table->timestamps();
        });

        // EXACT PRODUCTION: invests table (21 columns)
        Schema::create('invests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned()->default(0)->nullable();
            $table->bigInteger('plan_id')->unsigned()->default(0)->nullable();
            $table->decimal('amount', 28, 8)->default(0.00000000)->nullable(false);
            $table->decimal('actual_paid', 20, 8)->nullable()->comment('Amount actually paid by user after discounts');
            $table->decimal('token_discount', 20, 8)->default(0.00000000)->nullable(false)->comment('Discount amount from special tokens');
            $table->decimal('interest', 28, 8)->default(0.00000000)->nullable(false);
            $table->decimal('should_pay', 28, 8)->default(0.00000000)->nullable(false);
            $table->decimal('paid', 28, 8)->default(0.00000000)->nullable(false);
            $table->integer('period')->default(0)->nullable();
            $table->string('hours', 40)->nullable(false);
            $table->string('time_name', 40)->nullable(false);
            $table->integer('return_rec_time')->default(0)->nullable(false);
            $table->timestamp('next_time')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->nullable(false);
            $table->timestamp('last_time')->nullable();
            $table->boolean('status')->default(1)->nullable(false);
            $table->boolean('capital_status')->default(0)->nullable(false)->comment('1 = YES & 0 = NO');
            $table->string('trx', 40)->nullable();
            $table->string('wallet_type', 40)->nullable();
            $table->timestamps();
        });

        // EXACT PRODUCTION: deposits table (21 columns)
        Schema::create('deposits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned()->default(0)->nullable(false);
            $table->bigInteger('plan_id')->unsigned()->default(0)->nullable(false);
            $table->bigInteger('method_code')->unsigned()->default(0)->nullable(false);
            $table->bigInteger('payment_id')->nullable();
            $table->decimal('amount', 28, 8)->default(0.00000000)->nullable(false);
            $table->string('method_currency', 40)->nullable();
            $table->decimal('charge', 28, 8)->default(0.00000000)->nullable(false);
            $table->decimal('rate', 28, 8)->default(0.00000000)->nullable(false);
            $table->decimal('final_amo', 28, 8)->default(0.00000000)->nullable(false);
            $table->text('detail')->nullable();
            $table->string('customer_email', 191)->nullable();
            $table->string('btc_amo', 255)->nullable();
            $table->string('btc_wallet', 255)->nullable();
            $table->string('trx', 40)->nullable();
            $table->integer('try')->default(0)->nullable(false);
            $table->boolean('status')->default(0)->nullable(false)->comment('1=>success, 2=>pending, 3=>cancel');
            $table->boolean('from_api')->default(0)->nullable(false);
            $table->string('admin_feedback', 255)->nullable();
            $table->timestamps();
        });

        // EXACT PRODUCTION: withdrawals table (16 columns)
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('method_id')->unsigned()->default(0)->nullable(false);
            $table->bigInteger('user_id')->unsigned()->default(0)->nullable(false);
            $table->decimal('amount', 28, 8)->default(0.00000000)->nullable(false);
            $table->string('currency', 40)->nullable();
            $table->decimal('rate', 28, 8)->default(0.00000000)->nullable(false);
            $table->decimal('charge', 28, 8)->default(0.00000000)->nullable(false);
            $table->string('trx', 40)->nullable();
            $table->decimal('final_amount', 28, 8)->default(0.00000000)->nullable(false);
            $table->decimal('after_charge', 28, 8)->default(0.00000000)->nullable(false);
            $table->text('withdraw_information')->nullable();
            $table->string('withdraw_type', 191)->default('deposit')->nullable(false)->comment('Type: deposit or wallet');
            $table->boolean('status')->default(0)->nullable(false)->comment('1=>success, 2=>pending, 3=>cancel');
            $table->text('admin_feedback')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
        Schema::dropIfExists('deposits');
        Schema::dropIfExists('invests');
        Schema::dropIfExists('plans');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('users');
    }
};
