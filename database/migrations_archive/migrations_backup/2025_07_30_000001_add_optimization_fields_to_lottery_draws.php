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
            $table->timestamp('optimized_at')->nullable();
            $table->boolean('cleanup_performed')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lottery_draws', function (Blueprint $table) {
            $table->dropColumn(['optimized_at', 'cleanup_performed']);
        });
    }
};
