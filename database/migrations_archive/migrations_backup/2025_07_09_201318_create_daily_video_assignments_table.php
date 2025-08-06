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
        Schema::create('daily_video_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('video_link_id');
            $table->date('assignment_date');
            $table->boolean('is_watched')->default(false);
            $table->timestamp('watched_at')->nullable();
            $table->decimal('earning_amount', 10, 6)->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('video_link_id')->references('id')->on('video_links')->onDelete('cascade');
            
            // Indexes for performance
            $table->index(['user_id', 'assignment_date']);
            $table->index(['assignment_date', 'is_watched']);
            
            // Unique constraint to prevent duplicate assignments
            $table->unique(['user_id', 'video_link_id', 'assignment_date'], 'daily_video_user_date_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_video_assignments');
    }
};
