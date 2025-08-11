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
            // Current email verification step (Step 1)
            $table->string('current_email_otp', 6)->nullable()->comment('OTP for current email verification');
            $table->timestamp('current_email_otp_sent_at')->nullable()->comment('When current email OTP was sent');
            $table->boolean('current_email_verified')->default(false)->comment('Whether current email OTP is verified');
            
            // New email verification step (Step 2)  
            $table->string('new_email_verification_token')->nullable()->comment('Verification token for new email');
            $table->timestamp('new_email_token_sent_at')->nullable()->comment('When new email verification was sent');
            $table->string('email_change_step', 20)->default('initial')->comment('Current step: initial, current_verified, completed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'current_email_otp',
                'current_email_otp_sent_at', 
                'current_email_verified',
                'new_email_verification_token',
                'new_email_token_sent_at',
                'email_change_step'
            ]);
        });
    }
};
