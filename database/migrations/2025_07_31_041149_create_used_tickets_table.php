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
        Schema::create('used_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number', 50);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['valid', 'invalid', 'used'])->default('used');
            $table->enum('usage_type', ['investment', 'deposit', 'video_access', 'account_activation'])->default('investment');
            $table->decimal('discount_amount', 28, 8)->default(0.00000000);
            $table->decimal('discount_percentage', 5, 2)->default(0.00);
            $table->foreignId('investment_id')->nullable()->constrained('invests')->onDelete('set null');
            $table->json('metadata')->nullable();
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->index(['ticket_number', 'user_id']);
            $table->index(['user_id', 'status']);
            $table->index(['investment_id']);
            $table->index(['used_at']);
            $table->unique(['ticket_number', 'user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('used_tickets');
    }
};
