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
        Schema::table('users', function (Blueprint $table) {
            // Session tracking fields
            $table->string('current_session_id')->nullable()->after('remember_token');
            $table->timestamp('session_created_at')->nullable()->after('current_session_id');
            $table->string('session_ip_address')->nullable()->after('session_created_at');
            $table->text('session_user_agent')->nullable()->after('session_ip_address');
            $table->timestamp('last_activity_at')->nullable()->after('session_user_agent');
            
            // Add index for performance
            $table->index('current_session_id');
            $table->index('last_activity_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['current_session_id']);
            $table->dropIndex(['last_activity_at']);
            $table->dropColumn([
                'current_session_id',
                'session_created_at',
                'session_ip_address',
                'session_user_agent',
                'last_activity_at'
            ]);
        });
    }
};
