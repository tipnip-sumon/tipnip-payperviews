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
        Schema::table('lottery_winners', function (Blueprint $table) {
            $table->string('winning_ticket_number', 191)->nullable()->after('lottery_ticket_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lottery_winners', function (Blueprint $table) {
            $table->dropColumn('winning_ticket_number');
        });
    }
};
