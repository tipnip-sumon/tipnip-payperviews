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
        Schema::table('lottery_settings', function (Blueprint $table) {
            $table->boolean('auto_adjust_boost')->default(true)->after('active_tickets_boost')->comment('Automatically reduce boost when draws complete');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lottery_settings', function (Blueprint $table) {
            $table->dropColumn('auto_adjust_boost');
        });
    }
};
