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
            // Auto-generation settings
            $table->boolean('auto_generate_draws')->default(true)->after('auto_prize_distribution');
            $table->enum('auto_generation_frequency', ['daily', 'weekly', 'monthly'])->default('weekly')->after('auto_generate_draws');
            $table->json('auto_generation_schedule')->nullable()->after('auto_generation_frequency'); // Days of week, times, etc.
            
            // Virtual ticket settings
            $table->boolean('enable_virtual_tickets')->default(true)->after('auto_generation_schedule');
            $table->integer('min_virtual_tickets')->default(100)->after('enable_virtual_tickets');
            $table->integer('max_virtual_tickets')->default(1000)->after('min_virtual_tickets');
            $table->decimal('virtual_ticket_percentage', 5, 2)->default(80.00)->after('max_virtual_tickets'); // Percentage of virtual vs real tickets
            
            // Manual winner control
            $table->boolean('enable_manual_winner_selection')->default(true)->after('virtual_ticket_percentage');
            $table->json('default_winner_pool')->nullable()->after('enable_manual_winner_selection'); // Pool of potential winners
            
            // Auto-execution settings
            $table->boolean('auto_execute_draws')->default(true)->after('default_winner_pool');
            $table->integer('auto_execute_delay_minutes')->default(0)->after('auto_execute_draws'); // Delay before auto-execution
            
            // Next scheduled draw
            $table->datetime('next_auto_draw')->nullable()->after('auto_execute_delay_minutes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lottery_settings', function (Blueprint $table) {
            $table->dropColumn([
                'auto_generate_draws',
                'auto_generation_frequency', 
                'auto_generation_schedule',
                'enable_virtual_tickets',
                'min_virtual_tickets',
                'max_virtual_tickets',
                'virtual_ticket_percentage',
                'enable_manual_winner_selection',
                'default_winner_pool',
                'auto_execute_draws',
                'auto_execute_delay_minutes',
                'next_auto_draw'
            ]);
        });
    }
};
