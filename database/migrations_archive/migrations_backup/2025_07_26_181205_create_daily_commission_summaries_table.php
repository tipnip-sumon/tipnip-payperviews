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
        // Check if table exists and alter it if needed
        if (Schema::hasTable('daily_commission_summaries')) {
            Schema::table('daily_commission_summaries', function (Blueprint $table) {
                if (!Schema::hasColumn('daily_commission_summaries', 'total_users')) {
                    $table->integer('total_users')->default(0)->after('date');
                }
                if (!Schema::hasColumn('daily_commission_summaries', 'total_users_earned')) {
                    $table->integer('total_users_earned')->default(0)->after('total_distributed');
                }
                if (!Schema::hasColumn('daily_commission_summaries', 'total_levels_processed')) {
                    $table->integer('total_levels_processed')->default(0)->after('total_users_earned');
                }
                
                // Add level columns if they don't exist
                for ($i = 1; $i <= 7; $i++) {
                    if (!Schema::hasColumn('daily_commission_summaries', "level_{$i}_total")) {
                        $table->decimal("level_{$i}_total", 15, 6)->default(0);
                    }
                }
                
                if (!Schema::hasColumn('daily_commission_summaries', 'processed_at')) {
                    $table->timestamp('processed_at')->nullable();
                }
            });
        } else {
            Schema::create('daily_commission_summaries', function (Blueprint $table) {
                $table->id();
                $table->date('date')->unique();
                $table->integer('total_users')->default(0);
                $table->decimal('total_distributed', 15, 6)->default(0);
                $table->integer('total_users_earned')->default(0);
                $table->integer('total_levels_processed')->default(0);
                
                // Level-wise totals
                $table->decimal('level_1_total', 15, 6)->default(0);
                $table->decimal('level_2_total', 15, 6)->default(0);
                $table->decimal('level_3_total', 15, 6)->default(0);
                $table->decimal('level_4_total', 15, 6)->default(0);
                $table->decimal('level_5_total', 15, 6)->default(0);
                $table->decimal('level_6_total', 15, 6)->default(0);
                $table->decimal('level_7_total', 15, 6)->default(0);
                
                $table->timestamp('processed_at');
                $table->timestamps();
                
                $table->index('date');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_commission_summaries');
    }
};
