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
        Schema::table('lottery_tickets', function (Blueprint $table) {
            // Add virtual ticket flag
            $table->boolean('is_virtual')->default(false)->after('transaction_reference');
            $table->string('virtual_user_type')->nullable()->after('is_virtual'); // 'real_user', 'bot', 'system'
            $table->json('virtual_metadata')->nullable()->after('virtual_user_type'); // Additional virtual ticket data
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lottery_tickets', function (Blueprint $table) {
            $table->dropColumn(['is_virtual', 'virtual_user_type', 'virtual_metadata']);
        });
    }
};
