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
        Schema::table('referral_commissions', function (Blueprint $table) {
            $table->string('earning_type')->default('video_earning')->after('commission_type');
            $table->date('earning_date')->nullable()->after('earning_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('referral_commissions', function (Blueprint $table) {
            $table->dropColumn(['earning_type', 'earning_date']);
        });
    }
};
