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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('special_ticket_transfers');
    }
};
