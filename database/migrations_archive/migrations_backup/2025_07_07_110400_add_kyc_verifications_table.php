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
            if(!Schema::hasColumn('kyc_verifications', 'date_of_birth','document_number')) {
                $table->date('date_of_birth')->nullable()->after('user_id');
                $table->string('document_number', 50)->unique()->after('document_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kyc_verifications', function (Blueprint $table) {
            if(Schema::hasColumn('kyc_verifications', 'date_of_birth')) {
                $table->dropColumn('date_of_birth'); // Remove date_of_birth column
            }
            if(Schema::hasColumn('kyc_verifications', 'document_number')) {
                $table->dropColumn('document_number'); // Remove document_number column
            }
        });
    }
};
