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
        Schema::create('modal_settings', function (Blueprint $table) {
            $table->id();
            $table->string('modal_name')->unique(); // e.g., 'web_install_suggestion'
            $table->string('title')->default('Install App');
            $table->string('subtitle')->nullable();
            $table->string('heading')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->enum('target_users', ['all', 'guests', 'new_users', 'verified', 'unverified'])->default('all');
            $table->enum('show_frequency', ['once', 'daily', 'weekly', 'session'])->default('daily');
            $table->integer('max_shows')->default(7);
            $table->integer('delay_seconds')->default(3);
            $table->json('additional_settings')->nullable(); // For future customizations
            $table->timestamps();
            
            // Indexes
            $table->index('modal_name');
            $table->index('is_active');
            $table->index(['modal_name', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modal_settings');
    }
};
