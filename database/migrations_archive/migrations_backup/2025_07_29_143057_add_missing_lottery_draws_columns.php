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
            // Add max_tickets column
            if (!Schema::hasColumn('lottery_draws', 'max_tickets')) {
                $table->integer('max_tickets')->default(1000)->after('total_tickets_sold');
            }
            
            // Add ticket_price column
            if (!Schema::hasColumn('lottery_draws', 'ticket_price')) {
                $table->decimal('ticket_price', 8, 2)->default(2.00)->after('max_tickets');
            }
            
            // Add prize_distribution_type column
            if (!Schema::hasColumn('lottery_draws', 'prize_distribution_type')) {
                $table->enum('prize_distribution_type', ['fixed_amount', 'percentage'])->default('fixed_amount')->after('ticket_price');
            }
            
            // Add auto_prize_distribution column
            if (!Schema::hasColumn('lottery_draws', 'auto_prize_distribution')) {
                $table->boolean('auto_prize_distribution')->default(true)->after('auto_draw');
            }
            
            // Add prize_distribution column (JSON)
            if (!Schema::hasColumn('lottery_draws', 'prize_distribution')) {
                $table->json('prize_distribution')->nullable()->after('auto_prize_distribution');
            }
            
            // Add virtual_tickets_sold column
            if (!Schema::hasColumn('lottery_draws', 'virtual_tickets_sold')) {
                $table->integer('virtual_tickets_sold')->default(0)->after('prize_distribution');
            }
            
            // Add display_tickets_sold column
            if (!Schema::hasColumn('lottery_draws', 'display_tickets_sold')) {
                $table->integer('display_tickets_sold')->default(0)->after('virtual_tickets_sold');
            }
            
            // Add manual_winner_selection_enabled column
            if (!Schema::hasColumn('lottery_draws', 'manual_winner_selection_enabled')) {
                $table->boolean('manual_winner_selection_enabled')->default(true)->after('display_tickets_sold');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lottery_draws', function (Blueprint $table) {
            $columns = [
                'max_tickets',
                'ticket_price', 
                'prize_distribution_type',
                'auto_prize_distribution',
                'prize_distribution',
                'virtual_tickets_sold',
                'display_tickets_sold',
                'manual_winner_selection_enabled'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('lottery_draws', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
