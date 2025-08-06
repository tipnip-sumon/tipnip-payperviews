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
        Schema::create('withdraws', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('method_id')->constrained('withdraw_methods')->onDelete('cascade');
            $table->decimal('amount', 28, 8);
            $table->decimal('charge', 28, 8)->default(0);
            $table->decimal('rate', 28, 8)->default(1);
            $table->decimal('final_amount', 28, 8);
            $table->decimal('after_charge', 28, 8);
            $table->json('withdraw_information')->nullable();
            $table->string('trx', 40)->unique();
            $table->tinyInteger('status')->default(0)->comment('0: Pending, 1: Approved, 2: Rejected, 3: Processing');
            $table->text('admin_feedback')->nullable();
            $table->timestamp('processing_date')->nullable();
            $table->timestamp('approved_date')->nullable();
            $table->timestamp('rejected_date')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['status', 'created_at']);
            $table->index('trx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdraws');
    }
};
