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
        // Users table - Main user management
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('firstname', 40)->nullable();
            $table->string('lastname', 40)->nullable();
            $table->string('username', 40)->unique();
            $table->string('email')->unique();
            $table->string('country_code', 40)->nullable();
            $table->string('mobile', 40)->nullable();
            $table->string('country', 50)->nullable();
            $table->unsignedInteger('ref_by')->default(0);
            $table->decimal('deposit_wallet', 28, 8)->default(0.00000000);
            $table->decimal('interest_wallet', 28, 8)->default(0.00000000);
            $table->decimal('balance', 28, 8)->default(0.00000000);
            $table->string('password', 255);
            $table->string('image', 255)->nullable();
            $table->string('avatar', 255)->nullable();
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
            $table->string('referral_hash', 255)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamp('last_seen')->nullable();
            $table->string('session_id', 255)->nullable();
            $table->timestamp('session_started_at')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->index(['username', 'email']);
            $table->index(['ref_by']);
            $table->index(['status', 'ev']);
        });

        // Password reset tokens
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // User login history
        Schema::create('user_logins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('user_ip', 40)->nullable();
            $table->string('city', 40)->nullable();
            $table->string('country', 40)->nullable();
            $table->string('country_code', 40)->nullable();
            $table->string('longitude', 40)->nullable();
            $table->string('latitude', 40)->nullable();
            $table->string('browser', 40)->nullable();
            $table->string('os', 40)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });

        // KYC Verifications
        Schema::create('kyc_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('document_type', 50);
            $table->string('document_number', 100);
            $table->string('front_image', 255);
            $table->string('back_image', 255)->nullable();
            $table->string('selfie_image', 255)->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'under_review'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('submitted_at');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('under_review_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('admins')->onDelete('set null');
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['status', 'submitted_at']);
        });

        // User notifications
        Schema::create('user_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title', 255);
            $table->text('message');
            $table->enum('type', ['info', 'success', 'warning', 'error', 'promotion'])->default('info');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->json('metadata')->nullable();
            $table->string('action_url', 500)->nullable();
            $table->string('action_text', 100)->nullable();
            $table->boolean('is_dismissible')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_read']);
            $table->index(['created_at', 'priority']);
        });

        // User session notifications
        Schema::create('user_session_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title', 255);
            $table->text('message');
            $table->enum('type', ['info', 'success', 'warning', 'error'])->default('info');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->string('session_id', 255);
            $table->timestamps();

            $table->index(['user_id', 'session_id']);
            $table->index(['is_read', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_session_notifications');
        Schema::dropIfExists('user_notifications');
        Schema::dropIfExists('kyc_verifications');
        Schema::dropIfExists('user_logins');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
