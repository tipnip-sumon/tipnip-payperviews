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
            // Add fields for virtual tickets
            $table->integer('virtual_tickets_sold')->default(0)->after('total_tickets_sold');
            $table->integer('display_tickets_sold')->default(0)->after('virtual_tickets_sold');
            
            // Add fields for manual winner selection
            $table->boolean('manual_winner_selection_enabled')->default(false)->after('display_tickets_sold');
            $table->json('manually_selected_winners')->nullable()->after('manual_winner_selection_enabled');
            
            // Add prize distribution type
            $table->enum('prize_distribution_type', ['percentage', 'fixed_amount'])->default('percentage')->after('manually_selected_winners');
            
            // Add field to track if multiple winners per place are allowed
            $table->boolean('allow_multiple_winners_per_place')->default(false)->after('prize_distribution_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lottery_draws', function (Blueprint $table) {
            $table->dropColumn([
                'virtual_tickets_sold',
                'display_tickets_sold',
                'manual_winner_selection_enabled',
                'manually_selected_winners',
                'prize_distribution_type',
                'allow_multiple_winners_per_place'
            ]);
        });
    }
};
