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
        Schema::table('daily_video_assignments', function (Blueprint $table) {
            // Add new JSON columns for optimized storage
            $table->json('video_ids')->nullable()->comment('JSON array of assigned video IDs');
            $table->json('watched_video_ids')->nullable()->comment('JSON array of watched video IDs');
            $table->integer('total_videos')->default(0)->comment('Total number of videos assigned');
            $table->integer('watched_count')->default(0)->comment('Number of videos watched');
            
            // Add index for better performance
            $table->index(['user_id', 'assignment_date'], 'idx_user_date_optimized');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_video_assignments', function (Blueprint $table) {
            $table->dropColumn(['video_ids', 'watched_video_ids', 'total_videos', 'watched_count']);
            $table->dropIndex('idx_user_date_optimized');
        });
    }
};
