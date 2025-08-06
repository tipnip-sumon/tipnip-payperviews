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
        Schema::create('lottery_daily_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('summary_date');
            $table->integer('total_tickets_purchased')->default(0);
            $table->decimal('total_amount_spent', 15, 8)->default(0);
            $table->decimal('total_winnings', 15, 8)->default(0);
            $table->decimal('net_result', 15, 8)->default(0);
            $table->integer('draws_participated')->default(0);
            $table->integer('winning_tickets')->default(0);
            $table->json('ticket_details')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->unique(['user_id', 'summary_date'], 'user_date_unique');
            $table->index('summary_date');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lottery_daily_summaries');
    }
};
