<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations to optimize withdrawals table for large datasets (10K-20K records)
     */
    public function up(): void
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            // Add composite index for common query patterns
            $table->index(['status', 'created_at'], 'withdrawals_status_created_at_index');
            
            // Add index for withdrawal type filtering
            $table->index(['withdraw_type', 'status'], 'withdrawals_type_status_index');
            
            // Add index for user_id for faster user-related queries
            $table->index('user_id', 'withdrawals_user_id_index');
            
            // Add index for transaction ID searches
            $table->index('trx', 'withdrawals_trx_index');
            
            // Add composite index for date range queries
            $table->index(['created_at', 'status'], 'withdrawals_created_status_index');
            
            // Add index for amount-based queries (useful for reporting)
            $table->index(['amount', 'status'], 'withdrawals_amount_status_index');
        });
    }

    /**
     * Reverse the migrations
     */
    public function down(): void
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            // Drop all the indexes we created
            $table->dropIndex('withdrawals_status_created_at_index');
            $table->dropIndex('withdrawals_type_status_index');
            $table->dropIndex('withdrawals_user_id_index');
            $table->dropIndex('withdrawals_trx_index');
            $table->dropIndex('withdrawals_created_status_index');
            $table->dropIndex('withdrawals_amount_status_index');
        });
    }
};
