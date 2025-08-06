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
        // Investment plans table
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name', 40);
            $table->decimal('minimum', 28, 8)->default(0.00000000);
            $table->decimal('maximum', 28, 8)->default(0.00000000);
            $table->decimal('fixed_amount', 28, 8)->default(0.00000000);
            $table->decimal('interest', 5, 2)->default(0.00);
            $table->enum('interest_type', ['percent', 'fixed'])->default('percent');
            $table->enum('time', ['Hour', 'Day', 'Week', 'Month', 'Year'])->default('Day');
            $table->integer('lifetime')->default(0);
            $table->boolean('featured')->default(false);
            $table->boolean('capital_back')->default(true);
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->index(['status', 'featured']);
        });

        // User investments table
        Schema::create('invests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('plan_id')->constrained('plans')->onDelete('cascade');
            $table->string('trx', 40);
            $table->decimal('amount', 28, 8)->default(0.00000000);
            $table->decimal('interest', 28, 8)->default(0.00000000);
            $table->decimal('period', 50)->default(0);
            $table->enum('time_name', ['Hour', 'Day', 'Week', 'Month', 'Year'])->default('Day');
            $table->boolean('capital_status')->default(false);
            $table->enum('status', ['running', 'completed', 'cancelled'])->default('running');
            $table->datetime('last_time')->nullable();
            $table->datetime('next_time')->nullable();
            $table->decimal('discount_percentage', 5, 2)->default(0.00);
            $table->decimal('discount_amount', 28, 8)->default(0.00000000);
            $table->decimal('final_amount', 28, 8)->default(0.00000000);
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['plan_id', 'status']);
            $table->index(['trx']);
            $table->index(['next_time', 'status']);
        });

        // Referral system table
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('level', 40);
            $table->decimal('percent', 5, 2)->default(0.00);
            $table->decimal('commission_amount', 28, 8)->default(0.00000000);
            $table->string('commission_type', 40);
            $table->string('trx', 40);
            $table->timestamps();

            $table->index(['user_id', 'level']);
            $table->index(['trx']);
        });

        // Referral commissions table
        Schema::create('referral_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('referred_id')->constrained('users')->onDelete('cascade');
            $table->decimal('commission_amount', 28, 8)->default(0.00000000);
            $table->string('commission_type', 50);
            $table->integer('level')->default(1);
            $table->decimal('commission_percentage', 5, 2)->default(0.00);
            $table->string('transaction_id', 255)->nullable();
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->decimal('referrer_earning', 28, 8)->default(0.00000000);
            $table->decimal('referred_earning', 28, 8)->default(0.00000000);
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['referrer_id', 'status']);
            $table->index(['referred_id', 'level']);
            $table->index(['transaction_id']);
        });

        // Commission level settings table
        Schema::create('commission_level_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('level');
            $table->decimal('commission_percentage', 5, 2);
            $table->string('commission_type', 50)->default('percentage');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['level']);
            $table->index(['level', 'is_active']);
        });

        // Daily commission summaries table
        Schema::create('daily_commission_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('summary_date');
            $table->decimal('total_commission', 28, 8)->default(0.00000000);
            $table->integer('total_referrals')->default(0);
            $table->json('level_breakdown')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'summary_date']);
            $table->index(['summary_date', 'total_commission']);
        });

        // Referral user benefits table
        Schema::create('referral_user_benefits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('benefit_type', 50);
            $table->decimal('benefit_amount', 28, 8)->default(0.00000000);
            $table->decimal('benefit_percentage', 5, 2)->nullable();
            $table->string('description', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('valid_until')->nullable();
            $table->timestamp('applied_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'benefit_type']);
            $table->index(['is_active', 'valid_until']);
        });

        // Referral bonus transactions table
        Schema::create('referral_bonus_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('referred_id')->constrained('users')->onDelete('cascade');
            $table->string('transaction_type', 50);
            $table->decimal('bonus_amount', 28, 8)->default(0.00000000);
            $table->string('description', 255)->nullable();
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->string('reference', 255)->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['referrer_id', 'status']);
            $table->index(['referred_id', 'transaction_type']);
            $table->index(['reference']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_bonus_transactions');
        Schema::dropIfExists('referral_user_benefits');
        Schema::dropIfExists('daily_commission_summaries');
        Schema::dropIfExists('commission_level_settings');
        Schema::dropIfExists('referral_commissions');
        Schema::dropIfExists('referrals');
        Schema::dropIfExists('invests');
        Schema::dropIfExists('plans');
    }
};
