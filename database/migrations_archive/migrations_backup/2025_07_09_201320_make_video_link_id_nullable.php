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
            // Make video_link_id nullable for the new JSON structure
            $table->unsignedBigInteger('video_link_id')->nullable()->change();
            
            // Also make other old fields nullable for backward compatibility
            $table->boolean('is_watched')->nullable()->default(false)->change();
            $table->timestamp('watched_at')->nullable()->change();
            $table->decimal('earning_amount', 20, 8)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_video_assignments', function (Blueprint $table) {
            $table->unsignedBigInteger('video_link_id')->nullable(false)->change();
            $table->boolean('is_watched')->default(false)->change();
            $table->timestamp('watched_at')->nullable()->change();
            $table->decimal('earning_amount', 20, 8)->nullable()->change();
        });
    }
};
