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
        Schema::create('referral_commissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('earner_user_id'); // User who earned the original amount
            $table->unsignedBigInteger('referrer_user_id'); // User who gets the commission
            $table->unsignedBigInteger('daily_video_assignment_id'); // Source assignment
            $table->integer('level'); // Referral level (1-7)
            $table->decimal('original_earning', 10, 6); // Original earning amount
            $table->decimal('commission_percentage', 5, 2); // Commission percentage for this level
            $table->decimal('commission_amount', 10, 6); // Actual commission amount
            $table->string('commission_type')->default('video_earning'); // Type of commission
            $table->timestamp('distributed_at'); // When commission was distributed
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('earner_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('referrer_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('daily_video_assignment_id')->references('id')->on('daily_video_assignments')->onDelete('cascade');
            
            // Indexes
            $table->index(['earner_user_id', 'distributed_at']);
            $table->index(['referrer_user_id', 'distributed_at']);
            $table->index(['level', 'commission_type']);
            $table->index('daily_video_assignment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_commissions');
    }
};
