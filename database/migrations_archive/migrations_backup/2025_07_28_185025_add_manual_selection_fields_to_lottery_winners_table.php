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
        Schema::table('lottery_winners', function (Blueprint $table) {
            // Add fields for manual winner selection tracking
            $table->boolean('is_manual_selection')->default(false)->after('claim_status');
            $table->timestamp('selected_at')->nullable()->after('is_manual_selection');
            $table->unsignedBigInteger('selected_by')->nullable()->after('selected_at');
            $table->integer('winner_index')->default(1)->after('prize_position');
            
            // Add foreign key constraint
            $table->foreign('selected_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lottery_winners', function (Blueprint $table) {
            $table->dropForeign(['selected_by']);
            $table->dropColumn([
                'is_manual_selection',
                'selected_at', 
                'selected_by',
                'winner_index'
            ]);
        });
    }
};
