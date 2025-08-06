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
        Schema::table('lottery_draws', function (Blueprint $table) {
            if (!Schema::hasColumn('lottery_draws', 'auto_draw')) {
                $table->boolean('auto_draw')->default(false)->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lottery_draws', function (Blueprint $table) {
            if (Schema::hasColumn('lottery_draws', 'auto_draw')) {
                $table->dropColumn('auto_draw');
            }
        });
    }
};
