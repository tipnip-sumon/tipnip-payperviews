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
        // Remove foreign key from withdraw_methods table
        Schema::table('withdraw_methods', function (Blueprint $table) {
            // Drop the foreign key constraint for form_id
            $table->dropForeign(['form_id']);
            
            // Optionally, you can also drop the form_id column if it's not needed
            // $table->dropColumn('form_id');
        });
        
        // Remove foreign key from withdrawals table that references withdraw_methods
        Schema::table('withdrawals', function (Blueprint $table) {
            // Drop the foreign key constraint for method_id
            $table->dropForeign(['method_id']);
            
            // Keep the method_id column but remove the foreign key constraint
            // The column will remain as a regular unsignedBigInteger
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add the foreign key constraints
        Schema::table('withdraw_methods', function (Blueprint $table) {
            $table->foreign('form_id')->references('id')->on('forms')->onDelete('cascade');
        });
        
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->foreign('method_id')->references('id')->on('withdraw_methods')->onDelete('cascade');
        });
    }
};
