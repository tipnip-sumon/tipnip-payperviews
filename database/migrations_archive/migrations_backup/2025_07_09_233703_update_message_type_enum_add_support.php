<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update the enum to include 'support'
        DB::statement("ALTER TABLE messages MODIFY COLUMN message_type ENUM('private', 'system', 'broadcast', 'support') DEFAULT 'private'");
        
        // Also update status enum to include support-specific statuses
        DB::statement("ALTER TABLE messages MODIFY COLUMN status ENUM('active', 'deleted', 'archived', 'open', 'pending', 'closed', 'resolved') DEFAULT 'active'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values
        DB::statement("ALTER TABLE messages MODIFY COLUMN message_type ENUM('private', 'system', 'broadcast') DEFAULT 'private'");
        DB::statement("ALTER TABLE messages MODIFY COLUMN status ENUM('active', 'deleted', 'archived') DEFAULT 'active'");
    }
};
