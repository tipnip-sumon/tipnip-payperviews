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
        // Update the priority ENUM to include 'low'
        DB::statement("ALTER TABLE messages MODIFY COLUMN priority ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to the original ENUM values
        DB::statement("ALTER TABLE messages MODIFY COLUMN priority ENUM('normal', 'high', 'urgent') DEFAULT 'normal'");
    }
};
