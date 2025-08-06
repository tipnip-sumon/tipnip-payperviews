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
        Schema::table('users', function (Blueprint $table) {
            // Add verification timestamp columns
            $table->timestamp('sms_verified_at')->nullable()->after('sv');
            $table->timestamp('kyc_verified_at')->nullable()->after('kv');
            $table->timestamp('phone_verified_at')->nullable()->after('mobile');
            $table->timestamp('identity_verified_at')->nullable()->after('kyc_verified_at');
            $table->timestamp('two_fa_enabled_at')->nullable()->after('tv');
            
            // Add additional verification status columns
            $table->boolean('phone_verified')->default(0)->nullable(false)->after('mobile')->comment('0: phone unverified, 1: phone verified');
            $table->boolean('identity_verified')->default(0)->nullable(false)->after('phone_verified_at')->comment('0: identity unverified, 1: identity verified');
            $table->boolean('two_fa_status')->default(0)->nullable(false)->after('tv')->comment('0: 2fa disabled, 1: 2fa enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'sms_verified_at',
                'kyc_verified_at', 
                'phone_verified_at',
                'identity_verified_at',
                'two_fa_enabled_at',
                'phone_verified',
                'identity_verified',
                'two_fa_status'
            ]);
        });
    }
};
