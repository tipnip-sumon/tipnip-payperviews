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
        Schema::create('video_links', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('video_url')->comment('ads_link');
            $table->integer('duration')->nullable(); // Optional duration field
            $table->string('ads_type')->nullable(); // Optional field for video type (e.g., tutorial, ad, etc.)
            $table->string('category')->default('general'); // Category of the video (e.g., 'general', 'premium', etc.)
            $table->string('country')->nullable(); // Optional country field
            $table->string('source_platform')->nullable()->comment('source'); // YouTube, Vimeo, etc.
            $table->integer('views_count')->default(0); // Count of views for the video link
            $table->integer('clicks_count')->default(0); // Count of clicks for the video link
            $table->decimal('cost_per_click', 8, 2)->nullable()->comment('ads_amount'); // Cost per click for ads
            $table->enum('status', ['active', 'inactive', 'paused', 'completed'])->default('active'); // Status of the video link
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_links');
    }
};
