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
        Schema::table('lottery_settings', function (Blueprint $table) {
            // Add missing fields
            $table->integer('auto_claim_days')->default(30)->after('ticket_expiry_hours');
            $table->boolean('auto_refund_cancelled')->default(true)->after('auto_claim_days');
            $table->integer('prize_claim_deadline')->default(30)->after('auto_refund_cancelled');
            
            // Add new lottery features
            $table->boolean('allow_multiple_winners_per_place')->default(false)->after('prize_claim_deadline');
            $table->enum('prize_distribution_type', ['percentage', 'fixed_amount'])->default('percentage')->after('allow_multiple_winners_per_place');
            $table->boolean('manual_winner_selection')->default(false)->after('prize_distribution_type');
            $table->boolean('show_virtual_tickets')->default(false)->after('manual_winner_selection');
            $table->integer('virtual_ticket_multiplier')->default(100)->after('show_virtual_tickets');
            $table->integer('virtual_ticket_base')->default(0)->after('virtual_ticket_multiplier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lottery_settings', function (Blueprint $table) {
            $table->dropColumn([
                'auto_claim_days',
                'auto_refund_cancelled', 
                'prize_claim_deadline',
                'allow_multiple_winners_per_place',
                'prize_distribution_type',
                'manual_winner_selection',
                'show_virtual_tickets',
                'virtual_ticket_multiplier',
                'virtual_ticket_base'
            ]);
        });
    }
};
