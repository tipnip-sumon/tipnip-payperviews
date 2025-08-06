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
        Schema::table('lottery_draws', function (Blueprint $table) {
            // Add max_tickets field if it doesn't exist
            if (!Schema::hasColumn('lottery_draws', 'max_tickets')) {
                $table->integer('max_tickets')->default(1000)->after('total_tickets_sold');
            }
            
            // Add ticket_price field if it doesn't exist  
            if (!Schema::hasColumn('lottery_draws', 'ticket_price')) {
                $table->decimal('ticket_price', 8, 2)->default(2.00)->after('max_tickets');
            }
            
            // Add admin_commission_percentage field if it doesn't exist
            if (!Schema::hasColumn('lottery_draws', 'admin_commission_percentage')) {
                $table->decimal('admin_commission_percentage', 5, 2)->default(10.00)->after('ticket_price');
            }
            
            // Add auto_draw field if it doesn't exist
            if (!Schema::hasColumn('lottery_draws', 'auto_draw')) {
                $table->boolean('auto_draw')->default(true)->after('admin_commission_percentage');
            }
            
            // Add auto_prize_distribution field if it doesn't exist
            if (!Schema::hasColumn('lottery_draws', 'auto_prize_distribution')) {
                $table->boolean('auto_prize_distribution')->default(true)->after('auto_draw');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lottery_draws', function (Blueprint $table) {
            $table->dropColumn([
                'max_tickets',
                'ticket_price', 
                'admin_commission_percentage',
                'auto_draw',
                'auto_prize_distribution'
            ]);
        });
    }
};
