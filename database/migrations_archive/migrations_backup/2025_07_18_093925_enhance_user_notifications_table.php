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
        Schema::table('user_notifications', function (Blueprint $table) {
            // Add priority column
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal')->after('type');
            
            // Add action_text column
            $table->string('action_text')->nullable()->after('action_url');
            
            // Add metadata column for additional data
            $table->json('metadata')->nullable()->after('data');
            
            // Add indexes for better performance
            $table->index(['user_id', 'priority']);
            $table->index(['expires_at']);
            $table->index(['type']);
            $table->index(['priority']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_notifications', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'priority']);
            $table->dropIndex(['expires_at']);
            $table->dropIndex(['type']);
            $table->dropIndex(['priority']);
            
            $table->dropColumn(['priority', 'action_text', 'metadata']);
        });
    }
};
