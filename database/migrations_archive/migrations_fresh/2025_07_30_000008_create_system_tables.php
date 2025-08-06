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
        // Laravel system tables
        
        // Cache table
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        // Cache locks table
        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });

        // Jobs table
        Schema::create('jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        // Job batches table
        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        // Failed jobs table
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        // Personal access tokens table (Laravel Sanctum)
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        // Sessions table (recreated with proper structure)
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // Now Payments logger table (if using nowpayments)
        Schema::create('nowpayments_logger', function (Blueprint $table) {
            $table->id();
            $table->string('payment_id')->nullable();
            $table->string('order_id')->nullable();
            $table->string('payment_status')->nullable();
            $table->decimal('pay_amount', 28, 8)->default(0.00000000);
            $table->string('pay_currency', 10)->nullable();
            $table->decimal('price_amount', 28, 8)->default(0.00000000);
            $table->string('price_currency', 10)->nullable();
            $table->string('purchase_id')->nullable();
            $table->decimal('amount_received', 28, 8)->default(0.00000000);
            $table->text('outcome_amount')->nullable();
            $table->string('outcome_currency', 10)->nullable();
            $table->string('payout_hash')->nullable();
            $table->string('payin_hash')->nullable();
            $table->dateTime('created_at_np')->nullable();
            $table->dateTime('updated_at_np')->nullable();
            $table->json('extra_data')->nullable();
            $table->timestamps();

            $table->index(['payment_id']);
            $table->index(['order_id']);
            $table->index(['payment_status']);
        });

        // Withdraws table (separate from withdrawals for different purposes)
        Schema::create('withdraws', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('method', 255)->nullable();
            $table->decimal('amount', 28, 8)->default(0.00000000);
            $table->string('currency', 255);
            $table->decimal('rate', 28, 8)->default(0.00000000);
            $table->decimal('charge', 28, 8)->default(0.00000000);
            $table->decimal('trx', 40);
            $table->decimal('final_amo', 28, 8)->default(0.00000000);
            $table->text('detail')->nullable();
            $table->enum('status', ['initiated', 'pending', 'complete', 'cancel'])->default('initiated');
            $table->foreignId('admin_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['trx']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdraws');
        Schema::dropIfExists('nowpayments_logger');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
    }
};
