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
        Schema::table('general_settings', function (Blueprint $table) {
            // Transfer and Withdrawal Condition Settings
            $table->json('transfer_conditions')->nullable()->after('security_settings');
            $table->json('withdrawal_conditions')->nullable()->after('transfer_conditions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->dropColumn(['transfer_conditions', 'withdrawal_conditions']);
        });
    }
};
