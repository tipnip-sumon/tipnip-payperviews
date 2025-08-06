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
        // Video links table
        Schema::create('video_links', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('video_url', 500);
            $table->string('thumbnail', 255)->nullable();
            $table->integer('duration')->nullable()->comment('Duration in seconds');
            $table->decimal('earning_amount', 8, 2)->default(0.00);
            $table->string('category', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('view_count')->default(0);
            $table->integer('daily_view_limit')->default(100);
            $table->decimal('min_watch_duration', 5, 2)->default(30.00)->comment('Minimum watch duration in seconds for earning');
            $table->foreignId('created_by')->nullable()->constrained('admins')->onDelete('set null');
            $table->timestamps();

            $table->index(['is_active', 'category']);
            $table->index(['earning_amount', 'is_active']);
        });

        // Video views table
        Schema::create('video_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('video_link_id')->constrained()->onDelete('cascade');
            $table->decimal('watch_duration', 8, 2)->default(0.00)->comment('Watch duration in seconds');
            $table->decimal('earned_amount', 8, 2)->default(0.00);
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->string('session_id', 255)->nullable();
            $table->json('device_info')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'video_link_id']);
            $table->index(['is_completed', 'is_verified']);
            $table->index(['created_at', 'earned_amount']);
        });

        // User video views table (for tracking user-specific video viewing)
        Schema::create('user_video_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('video_link_id')->constrained()->onDelete('cascade');
            $table->integer('total_views')->default(0);
            $table->decimal('total_earned', 8, 2)->default(0.00);
            $table->timestamp('last_viewed_at')->nullable();
            $table->integer('daily_views')->default(0);
            $table->date('last_daily_reset')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'video_link_id']);
            $table->index(['user_id', 'last_viewed_at']);
        });

        // Daily video assignments table
        Schema::create('daily_video_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('video_link_id')->nullable()->constrained()->onDelete('set null');
            $table->date('assignment_date');
            $table->integer('required_views')->default(1);
            $table->integer('completed_views')->default(0);
            $table->decimal('target_earning', 8, 2)->default(0.00);
            $table->decimal('actual_earning', 8, 2)->default(0.00);
            $table->enum('status', ['pending', 'in_progress', 'completed', 'expired'])->default('pending');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'assignment_date']);
            $table->index(['assignment_date', 'status']);
            $table->index(['video_link_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_video_assignments');
        Schema::dropIfExists('user_video_views');
        Schema::dropIfExists('video_views');
        Schema::dropIfExists('video_links');
    }
};
