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
        Schema::create('special_lottery_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->unsignedBigInteger('user_id'); // Original user field for compatibility
            $table->unsignedBigInteger('sponsor_user_id'); // User who received the ticket
            $table->unsignedBigInteger('referral_user_id'); // User who made first purchase
            $table->unsignedBigInteger('current_owner_id'); // Current owner (for transfers)
            $table->unsignedBigInteger('original_owner_id'); // Original owner (never changes)
            $table->unsignedBigInteger('lottery_draw_id')->nullable();
            $table->decimal('ticket_price', 10, 2)->default(2.00);
            $table->enum('status', ['active', 'expired', 'winner', 'lost', 'refunded', 'used_as_token'])->default('active');
            $table->decimal('prize_amount', 10, 2)->nullable();
            $table->decimal('refund_amount', 10, 2)->default(1.00); // $1 refund if not winning
            $table->datetime('purchased_at');
            $table->datetime('claimed_at')->nullable();
            $table->datetime('used_as_token_at')->nullable();
            $table->decimal('token_discount_amount', 10, 2)->nullable(); // Amount used as discount
            $table->unsignedBigInteger('used_for_plan_id')->nullable(); // Plan ID where token was used
            $table->decimal('early_usage_bonus', 5, 2)->default(0); // 0-5% bonus for early usage
            $table->boolean('is_valid_token')->default(true); // Can be used as discount token
            $table->datetime('token_expires_at')->nullable(); // Token expiry date
            $table->string('transaction_reference')->nullable(); // Reference for tracking
            $table->boolean('is_transferable')->default(true);
            $table->integer('transfer_count')->default(0); // Track how many times transferred
            $table->datetime('last_transferred_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('sponsor_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('referral_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('current_owner_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('original_owner_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('lottery_draw_id')->references('id')->on('lottery_draws')->onDelete('set null');
            $table->foreign('used_for_plan_id')->references('id')->on('plans')->onDelete('set null');
            
            $table->index(['sponsor_user_id', 'status']);
            $table->index(['current_owner_id', 'status']);
            $table->index(['status', 'is_valid_token']);
            $table->index('token_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('special_lottery_tickets');
    }
};
