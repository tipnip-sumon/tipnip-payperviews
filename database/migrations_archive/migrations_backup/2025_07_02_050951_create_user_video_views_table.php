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
        Schema::create('user_video_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('video_link_id')->constrained()->onDelete('cascade');
            $table->decimal('earning_amount', 10, 2)->default(0);
            $table->integer('view_duration')->default(0); // in seconds
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['video_link_id', 'created_at']);
            $table->index(['is_completed', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_video_views');
    }
};
