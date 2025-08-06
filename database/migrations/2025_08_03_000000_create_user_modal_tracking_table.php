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
        Schema::create('user_modal_tracking', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('modal_type')->default('welcome_guide');
            $table->integer('daily_click_count')->default(0);
            $table->timestamp('last_shown_at')->nullable();
            $table->timestamp('last_clicked_at')->nullable();
            $table->date('current_date');
            $table->boolean('dismissed_today')->default(false);
            $table->json('click_history')->nullable(); // Store click timestamps
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['user_id', 'modal_type', 'current_date']);
            $table->index(['user_id', 'modal_type']);
            $table->index('current_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_modal_tracking');
    }
};
