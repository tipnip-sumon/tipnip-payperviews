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
        Schema::table('referral_commissions', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['daily_video_assignment_id']);
            
            // Modify the column to allow null
            $table->unsignedBigInteger('daily_video_assignment_id')->nullable()->change();
            
            // Add back the foreign key constraint that allows null
            $table->foreign('daily_video_assignment_id')
                  ->references('id')
                  ->on('daily_video_assignments')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('referral_commissions', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['daily_video_assignment_id']);
            
            // Modify the column back to not null
            $table->unsignedBigInteger('daily_video_assignment_id')->nullable(false)->change();
            
            // Add back the original foreign key constraint
            $table->foreign('daily_video_assignment_id')
                  ->references('id')
                  ->on('daily_video_assignments')
                  ->onDelete('cascade');
        });
    }
};
