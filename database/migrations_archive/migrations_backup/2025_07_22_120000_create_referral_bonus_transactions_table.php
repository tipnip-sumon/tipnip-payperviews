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
        // Create table to track referral transactions and bonuses
        Schema::create('referral_bonus_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_benefit_id'); // Reference to referral_user_benefits
            $table->string('type'); // transfer_bonus, receive_bonus, withdraw_reduction
            $table->decimal('original_amount', 15, 2);
            $table->decimal('percentage_used', 5, 2);
            $table->decimal('amount', 15, 2); // bonus amount or reduction amount
            $table->string('related_transaction_id')->nullable(); // Reference to the actual transfer/withdraw
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('user_benefit_id')->references('id')->on('referral_user_benefits')->onDelete('cascade');
            // Shortened index names
            $table->index(['user_benefit_id', 'type'], 'rbt_user_type_idx');
            $table->index('created_at', 'rbt_created_at_idx');
            $table->index('related_transaction_id', 'rbt_related_txn_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_bonus_transactions');
    }
};
