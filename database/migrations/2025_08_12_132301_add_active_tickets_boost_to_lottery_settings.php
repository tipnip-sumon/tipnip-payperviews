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
            $table->integer('active_tickets_boost')->default(0)->after('virtual_user_id')->comment('Manual boost number to add to real active tickets count for display');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lottery_settings', function (Blueprint $table) {
            $table->dropColumn('active_tickets_boost');
        });
    }
};
