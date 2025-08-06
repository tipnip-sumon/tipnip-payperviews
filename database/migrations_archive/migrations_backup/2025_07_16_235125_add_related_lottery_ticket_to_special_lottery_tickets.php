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
        Schema::table('special_lottery_tickets', function (Blueprint $table) {
            $table->unsignedBigInteger('related_lottery_ticket_id')->nullable()->after('used_for_plan_id');
            $table->foreign('related_lottery_ticket_id')->references('id')->on('lottery_tickets')->onDelete('set null');
            $table->index('related_lottery_ticket_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('special_lottery_tickets', function (Blueprint $table) {
            $table->dropForeign(['related_lottery_ticket_id']);
            $table->dropIndex(['related_lottery_ticket_id']);
            $table->dropColumn('related_lottery_ticket_id');
        });
    }
};
