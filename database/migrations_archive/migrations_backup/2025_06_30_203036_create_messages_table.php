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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('to_user_id')->constrained('users')->onDelete('cascade');
            $table->string('subject');
            $table->text('message');
            $table->enum('priority', ['normal', 'high', 'urgent'])->default('normal');
            $table->boolean('is_read')->default(false);
            $table->boolean('is_starred')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->foreignId('reply_to_id')->nullable()->constrained('messages')->onDelete('cascade');
            $table->string('attachment_path')->nullable();
            $table->enum('message_type', ['private', 'system', 'broadcast'])->default('private');
            $table->enum('status', ['active', 'deleted', 'archived'])->default('active');
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index(['to_user_id', 'is_read']);
            $table->index(['from_user_id', 'created_at']);
            $table->index(['priority', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
