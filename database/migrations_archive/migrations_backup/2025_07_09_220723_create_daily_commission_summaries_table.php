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
        Schema::create('daily_commission_summaries', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique(); // The date for which commissions were processed
            $table->integer('total_assignments')->default(0); // Number of assignments processed
            $table->decimal('total_distributed', 15, 6)->default(0); // Total commission amount distributed
            $table->integer('total_users_earned')->default(0); // Number of users who received commissions
            $table->integer('total_levels_processed')->default(0); // Total referral levels processed
            $table->decimal('level_1_total', 15, 6)->default(0); // Level 1 commission total
            $table->decimal('level_2_total', 15, 6)->default(0); // Level 2 commission total
            $table->decimal('level_3_total', 15, 6)->default(0); // Level 3 commission total
            $table->decimal('level_4_total', 15, 6)->default(0); // Level 4 commission total
            $table->decimal('level_5_total', 15, 6)->default(0); // Level 5 commission total
            $table->decimal('level_6_total', 15, 6)->default(0); // Level 6 commission total
            $table->decimal('level_7_total', 15, 6)->default(0); // Level 7 commission total
            $table->decimal('level_8_total', 15, 6)->default(0); // Level 8 commission total
            $table->decimal('level_9_total', 15, 6)->default(0); // Level 9 commission total
            $table->decimal('level_10_total', 15, 6)->default(0); // Level 10 commission total
            $table->timestamp('processed_at'); // When the distribution was processed
            $table->timestamps();
            
            // Indexes
            $table->index('date');
            $table->index('processed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_commission_summaries');
    }
};
