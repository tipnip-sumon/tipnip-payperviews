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
        Schema::table('video_views', function (Blueprint $table) {
            // Add fields for optimized single-row video view system
            $table->date('view_date')->nullable()->after('user_id'); // Date of viewing session
            $table->string('view_type')->default('individual')->after('view_date'); // 'daily_summary' or 'individual'
            $table->json('video_data')->nullable()->after('view_type'); // JSON array of videos watched
            $table->decimal('total_earned', 10, 8)->default(0)->after('video_data'); // Total earned that day
            $table->integer('total_videos')->default(0)->after('total_earned'); // Total videos watched that day
            
            // Make existing fields nullable for compatibility
            $table->unsignedBigInteger('video_link_id')->nullable()->change();
            $table->decimal('earned_amount', 10, 8)->nullable()->change();
            
            // Add indexes for performance
            $table->index(['user_id', 'view_date', 'view_type']);
            $table->index(['view_type', 'view_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('video_views', function (Blueprint $table) {
            // Remove added fields
            $table->dropColumn([
                'view_date',
                'view_type', 
                'video_data',
                'total_earned',
                'total_videos'
            ]);
            
            // Remove indexes
            $table->dropIndex(['user_id', 'view_date', 'view_type']);
            $table->dropIndex(['view_type', 'view_date']);
            
            // Restore original constraints (if needed)
            $table->unsignedBigInteger('video_link_id')->nullable(false)->change();
            $table->decimal('earned_amount', 10, 8)->nullable(false)->change();
        });
    }
};
