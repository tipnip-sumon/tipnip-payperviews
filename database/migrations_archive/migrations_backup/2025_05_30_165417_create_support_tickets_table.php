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
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('email');
            $table->string('ticket', 40)->unique();
            $table->string('subject');
            $table->tinyInteger('status')->default(0)->comment('0: Open, 1: Answered, 2: Customer Reply, 3: Closed');
            $table->tinyInteger('priority')->default(2)->comment('1: Low, 2: Medium, 3: High');
            $table->timestamp('last_reply')->nullable();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->onDelete('set null');
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['status', 'priority']);
            $table->index('ticket');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
