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
        Schema::table('invests', function (Blueprint $table) {
            $table->string('applied_ticket', 50)->nullable()->after('ticket_discount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invests', function (Blueprint $table) {
            $table->dropColumn('applied_ticket');
        });
    }
};
