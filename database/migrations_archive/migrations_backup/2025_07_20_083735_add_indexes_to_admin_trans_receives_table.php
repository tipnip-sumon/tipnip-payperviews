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
        Schema::table('admin_trans_receives', function (Blueprint $table) {
            // Add indexes for better query performance
            $table->index('created_at', 'idx_admin_trans_receives_created_at');
            $table->index('amount', 'idx_admin_trans_receives_amount');
            $table->index('user_receive', 'idx_admin_trans_receives_user_receive');
            $table->index(['created_at', 'amount'], 'idx_admin_trans_receives_created_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_trans_receives', function (Blueprint $table) {
            // Drop the indexes
            $table->dropIndex('idx_admin_trans_receives_created_at');
            $table->dropIndex('idx_admin_trans_receives_amount');
            $table->dropIndex('idx_admin_trans_receives_user_receive');
            $table->dropIndex('idx_admin_trans_receives_created_amount');
        });
    }
};
