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
            // Email change tracking fields
            $table->string('pending_email_change')->nullable()->after('email');
            $table->string('email_change_token', 6)->nullable()->after('pending_email_change');
            $table->timestamp('email_change_requested_at')->nullable()->after('email_change_token');
            
            // Username change tracking fields
            $table->string('pending_username_change')->nullable()->after('username');
            $table->string('username_change_token', 6)->nullable()->after('pending_username_change');
            $table->timestamp('username_change_requested_at')->nullable()->after('username_change_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'pending_email_change',
                'email_change_token',
                'email_change_requested_at',
                'pending_username_change',
                'username_change_token',
                'username_change_requested_at'
            ]);
        });
    }
};
