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
            $table->boolean('is_invalidated')->default(false)->after('transaction_reference');
            $table->timestamp('invalidated_at')->nullable()->after('is_invalidated');
            $table->string('invalidation_reason')->nullable()->after('invalidated_at');
            $table->index(['is_invalidated', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lottery_tickets', function (Blueprint $table) {
            $table->dropIndex(['is_invalidated', 'status']);
            $table->dropColumn(['is_invalidated', 'invalidated_at', 'invalidation_reason']);
        });
    }
};
