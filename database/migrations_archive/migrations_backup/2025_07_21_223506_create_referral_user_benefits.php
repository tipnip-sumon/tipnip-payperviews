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
         Schema::create('referral_user_benefits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->integer('qualified_referrals_count')->default(0);
            $table->decimal('transfer_bonus_percentage', 5, 2)->default(0.00);
            $table->decimal('receive_bonus_percentage', 5, 2)->default(0.00);
            $table->decimal('withdraw_reduction_percentage', 5, 2)->default(0.00);
            $table->decimal('total_bonuses_earned', 15, 2)->default(0.00);
            $table->boolean('is_qualified')->default(false);
            $table->boolean('is_active')->default(true);
            $table->datetime('qualified_at')->nullable();
            $table->datetime('last_recalculated_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique('user_id');
            // Shortened index names to avoid MySQL length limit
            $table->index(['is_qualified', 'qualified_referrals_count'], 'rub_qualified_count_idx');
            $table->index(['is_active', 'qualified_at'], 'rub_active_qualified_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_user_benefits');
    }
};
