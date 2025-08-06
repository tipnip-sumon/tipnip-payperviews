<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lottery_tickets', function (Blueprint $table) {
            // Check if unique constraint doesn't already exist
            $indexExists = collect(DB::select("SHOW INDEX FROM lottery_tickets"))
                ->where('Key_name', 'lottery_tickets_ticket_number_unique')
                ->isNotEmpty();
                
            if (!$indexExists) {
                // Add unique constraint to ticket_number column
                $table->unique('ticket_number', 'lottery_tickets_ticket_number_unique');
            }
            
            // Add indexes for performance if they don't exist
            $userDrawIndexExists = collect(DB::select("SHOW INDEX FROM lottery_tickets"))
                ->where('Key_name', 'lottery_tickets_user_draw_index')
                ->isNotEmpty();
                
            if (!$userDrawIndexExists) {
                $table->index(['user_id', 'lottery_draw_id'], 'lottery_tickets_user_draw_index');
            }
            
            $purchasedIndexExists = collect(DB::select("SHOW INDEX FROM lottery_tickets"))
                ->where('Key_name', 'lottery_tickets_purchased_at_index')
                ->isNotEmpty();
                
            if (!$purchasedIndexExists) {
                $table->index('purchased_at', 'lottery_tickets_purchased_at_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lottery_tickets', function (Blueprint $table) {
            // Only drop if they exist
            $uniqueExists = collect(DB::select("SHOW INDEX FROM lottery_tickets"))
                ->where('Key_name', 'lottery_tickets_ticket_number_unique')
                ->isNotEmpty();
                
            if ($uniqueExists) {
                $table->dropUnique('lottery_tickets_ticket_number_unique');
            }
            
            $userDrawExists = collect(DB::select("SHOW INDEX FROM lottery_tickets"))
                ->where('Key_name', 'lottery_tickets_user_draw_index')
                ->isNotEmpty();
                
            if ($userDrawExists) {
                $table->dropIndex('lottery_tickets_user_draw_index');
            }
            
            $purchasedExists = collect(DB::select("SHOW INDEX FROM lottery_tickets"))
                ->where('Key_name', 'lottery_tickets_purchased_at_index')
                ->isNotEmpty();
                
            if ($purchasedExists) {
                $table->dropIndex('lottery_tickets_purchased_at_index');
            }
        });
    }
};
