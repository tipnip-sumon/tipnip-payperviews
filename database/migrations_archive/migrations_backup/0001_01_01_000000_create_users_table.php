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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('firstname',40)->nullable();
            $table->string('lastname',40)->nullable();
            $table->string('username',40)->unique();
            $table->string('email')->unique();
            $table->string('country_code',40)->nullable();
            $table->string('mobile',40)->nullable();
            $table->unsignedInteger('ref_by')->default(0);
            $table->decimal('deposit_wallet', 28, 8)->default(0.00000000);
            $table->decimal('interest_wallet', 28, 8)->default(0.00000000);
            $table->string('password',255);
            $table->string('image',255)->nullable();
            $table->text('address')->nullable()->comment('contains full address');
            $table->boolean('status')->default(true)->comment('0: banned, 1: active');
            $table->text('kyc_data')->nullable();
            $table->boolean('kv')->default(false)->comment('0: KYC Unverified, 2: KYC pending, 1: KYC verified');
            $table->boolean('ev')->default(false)->comment('0: email unverified, 1: email verified');
            $table->boolean('sv')->default(false)->comment('0: mobile unverified, 1: mobile verified');
            $table->boolean('profile_complete')->default(false);
            $table->string('ver_code', 40)->nullable()->comment('stores verification code');
            $table->datetime('ver_code_send_at')->nullable()->comment('verification send time');
            $table->boolean('ts')->default(false)->comment('0: 2fa off, 1: 2fa on');
            $table->boolean('tv')->default(true)->comment('0: 2fa unverified, 1: 2fa verified');
            $table->string('tsc', 255)->nullable();
            $table->string('ban_reason', 255)->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
