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
        // Schema::create('video_links', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('title');
        //     $table->text('description')->nullable();
        //     $table->string('video_url'); // YouTube URL or embed code
        //     $table->string('thumbnail_url')->nullable();
        //     $table->decimal('earning_per_view', 8, 4)->default(0.0001); // Money per view
        //     $table->integer('total_views')->default(0);
        //     $table->decimal('total_earnings', 10, 4)->default(0);
        //     $table->boolean('is_active')->default(true);
        //     $table->timestamps();
        // }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('video_links');
    }
};
