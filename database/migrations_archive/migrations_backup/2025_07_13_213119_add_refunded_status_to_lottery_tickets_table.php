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
        // Add 'refunded' to the status enum
        DB::statement("ALTER TABLE lottery_tickets MODIFY COLUMN status ENUM('active', 'expired', 'winner', 'claimed', 'refunded') DEFAULT 'active'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'refunded' from the status enum
        DB::statement("ALTER TABLE lottery_tickets MODIFY COLUMN status ENUM('active', 'expired', 'winner', 'claimed') DEFAULT 'active'");
    }
};
