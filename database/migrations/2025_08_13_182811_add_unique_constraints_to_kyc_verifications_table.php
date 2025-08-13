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
        Schema::table('kyc_verifications', function (Blueprint $table) {
            // Add unique constraint for document_number
            $table->unique('document_number', 'kyc_document_number_unique');
            
            // Add unique constraint for phone_number
            $table->unique('phone_number', 'kyc_phone_number_unique');
            
            // Add index for better query performance
            $table->index(['user_id', 'status'], 'kyc_user_status_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kyc_verifications', function (Blueprint $table) {
            // Drop unique constraints
            $table->dropUnique('kyc_document_number_unique');
            $table->dropUnique('kyc_phone_number_unique');
            
            // Drop index
            $table->dropIndex('kyc_user_status_index');
        });
    }
};
