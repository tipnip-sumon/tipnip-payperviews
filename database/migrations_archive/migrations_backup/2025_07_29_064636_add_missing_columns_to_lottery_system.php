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
        // Add missing columns to lottery_draws table
        Schema::table('lottery_draws', function (Blueprint $table) {
            if (!Schema::hasColumn('lottery_draws', 'admin_commission_percentage')) {
                $table->decimal('admin_commission_percentage', 5, 2)->default(10.00)->after('total_prize_pool');
            }
        });

        // Add missing columns to lottery_winners table  
        Schema::table('lottery_winners', function (Blueprint $table) {
            if (!Schema::hasColumn('lottery_winners', 'prize_distributed')) {
                $table->boolean('prize_distributed')->default(false)->after('claim_status');
            }
        });

        // Add missing columns to video_links table
        if (Schema::hasTable('video_links')) {
            Schema::table('video_links', function (Blueprint $table) {
                if (!Schema::hasColumn('video_links', 'description')) {
                    $table->text('description')->nullable()->after('title');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove added columns from lottery_draws table
        Schema::table('lottery_draws', function (Blueprint $table) {
            if (Schema::hasColumn('lottery_draws', 'admin_commission_percentage')) {
                $table->dropColumn('admin_commission_percentage');
            }
        });

        // Remove added columns from lottery_winners table
        Schema::table('lottery_winners', function (Blueprint $table) {
            if (Schema::hasColumn('lottery_winners', 'prize_distributed')) {
                $table->dropColumn('prize_distributed');
            }
        });

        // Remove added columns from video_links table
        if (Schema::hasTable('video_links')) {
            Schema::table('video_links', function (Blueprint $table) {
                if (Schema::hasColumn('video_links', 'description')) {
                    $table->dropColumn('description');
                }
            });
        }
    }
};
