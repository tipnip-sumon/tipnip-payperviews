<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations - EXACT PRODUCTION STRUCTURE (Part 5)
     * Gateway, Payment, System and remaining production tables
     */
    public function up(): void
    {
        // EXACT PRODUCTION: gateways table (13 columns)
        if (!Schema::hasTable('gateways')) {
            Schema::create('gateways', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('form_id')->unsigned()->default(0)->nullable(false);
                $table->bigInteger('code')->unsigned()->nullable();
                $table->string('name', 40)->nullable();
                $table->string('alias', 40)->nullable(false);
                $table->boolean('status')->default(1)->nullable(false)->comment('1=>enable, 2=>disable');
                $table->text('gateway_parameters')->nullable();
                $table->text('supported_currencies')->nullable();
                $table->boolean('crypto')->default(0)->nullable(false)->comment('0: fiat currency, 1: crypto currency');
                $table->text('extra')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // EXACT PRODUCTION: gateway_currencies table (15 columns)
        if (!Schema::hasTable('gateway_currencies')) {
            Schema::create('gateway_currencies', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name', 40)->nullable();
                $table->string('currency', 40)->nullable();
                $table->string('symbol', 40)->nullable();
                $table->integer('method_code')->nullable();
                $table->string('gateway_alias', 40)->nullable();
                $table->decimal('min_amount', 28, 8)->default(0.00000000)->nullable(false);
                $table->decimal('max_amount', 28, 8)->default(0.00000000)->nullable(false);
                $table->decimal('percent_charge', 5, 2)->default(0.00)->nullable(false);
                $table->decimal('fixed_charge', 28, 8)->default(0.00000000)->nullable(false);
                $table->decimal('rate', 28, 8)->default(0.00000000)->nullable(false);
                $table->string('image', 255)->nullable();
                $table->text('gateway_parameter')->nullable();
                $table->timestamps();
            });
        }

        // EXACT PRODUCTION: withdraw_methods table (23 columns)
        if (!Schema::hasTable('withdraw_methods')) {
            Schema::create('withdraw_methods', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('form_id')->unsigned()->default(0)->nullable(false);
                $table->string('name', 40)->nullable();
                $table->string('method_key', 191)->nullable(false);
                $table->decimal('min_limit', 28, 8)->default(0.00000000)->nullable();
                $table->decimal('max_limit', 28, 8)->default(0.00000000)->nullable(false);
                $table->decimal('fixed_charge', 28, 8)->default(0.00000000)->nullable();
                $table->decimal('rate', 28, 8)->default(0.00000000)->nullable();
                $table->decimal('percent_charge', 5, 2)->default(0.00)->nullable();
                $table->string('currency', 40)->nullable();
                $table->text('instructions')->nullable();
                $table->integer('sort_order')->default(0)->nullable(false);
                $table->text('description')->nullable();
                $table->string('icon', 191)->nullable();
                $table->string('processing_time', 191)->default('1-3 business days')->nullable(false);
                $table->boolean('status')->default(1)->nullable(false);
                $table->decimal('min_amount', 28, 8)->default(1.00000000)->nullable(false);
                $table->decimal('max_amount', 28, 8)->default(10000.00000000)->nullable(false);
                $table->decimal('daily_limit', 28, 8)->default(5000.00000000)->nullable(false);
                $table->enum('charge_type', ['fixed', 'percent'])->default('fixed')->nullable(false);
                $table->decimal('charge', 28, 8)->default(0.00000000)->nullable(false);
                $table->timestamps();
            });
        }

        // EXACT PRODUCTION: withdraws table (17 columns)
        if (!Schema::hasTable('withdraws')) {
            Schema::create('withdraws', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('user_id')->unsigned()->nullable(false);
                $table->bigInteger('method_id')->unsigned()->nullable(false);
                $table->decimal('amount', 28, 8)->nullable(false);
                $table->decimal('charge', 28, 8)->default(0.00000000)->nullable(false);
                $table->decimal('rate', 28, 8)->default(1.00000000)->nullable(false);
                $table->decimal('final_amount', 28, 8)->nullable(false);
                $table->decimal('after_charge', 28, 8)->nullable(false);
                $table->json('withdraw_information')->nullable();
                $table->string('trx', 40)->nullable(false);
                $table->tinyInteger('status')->default(0)->nullable(false)->comment('0: Pending, 1: Approved, 2: Rejected, 3: Processing');
                $table->text('admin_feedback')->nullable();
                $table->timestamp('processing_date')->nullable();
                $table->timestamp('approved_date')->nullable();
                $table->timestamp('rejected_date')->nullable();
                $table->timestamps();
            });
        }

        // EXACT PRODUCTION: forms table (5 columns)
        if (!Schema::hasTable('forms')) {
            Schema::create('forms', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('act', 40)->nullable();
                $table->text('form_data')->nullable();
                $table->timestamps();
            });
        }

        // EXACT PRODUCTION: holidays table (5 columns)
        if (!Schema::hasTable('holidays')) {
            Schema::create('holidays', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('title', 100)->nullable()->comment('Holiday title');
                $table->date('date')->nullable()->comment('Holiday date');
                $table->timestamps();
            });
        }

        // EXACT PRODUCTION: subscribers table (4 columns)
        if (!Schema::hasTable('subscribers')) {
            Schema::create('subscribers', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('email', 100)->nullable(false)->comment('Subscriber email address');
                $table->timestamps();
            });
        }

        // EXACT PRODUCTION: newsletter_subscribers table (10 columns)
        if (!Schema::hasTable('newsletter_subscribers')) {
            Schema::create('newsletter_subscribers', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('email', 191)->nullable(false);
                $table->string('ip_address', 191)->nullable();
                $table->text('user_agent')->nullable();
                $table->string('source', 191)->default('website')->nullable(false);
                $table->boolean('is_active')->default(1)->nullable(false);
                $table->timestamp('subscribed_at')->nullable();
                $table->timestamp('unsubscribed_at')->nullable();
                $table->timestamps();
            });
        }

        // EXACT PRODUCTION: popup_views table (10 columns)
        if (!Schema::hasTable('popup_views')) {
            Schema::create('popup_views', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('popup_id')->unsigned()->nullable(false);
                $table->bigInteger('user_id')->unsigned()->nullable();
                $table->string('session_id', 191)->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->string('user_agent', 191)->nullable();
                $table->boolean('clicked')->default(0)->nullable(false);
                $table->timestamp('clicked_at')->nullable();
                $table->timestamps();
            });
        }

        // EXACT PRODUCTION: configuration_changes table (12 columns)
        if (!Schema::hasTable('configuration_changes')) {
            Schema::create('configuration_changes', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('setting_name', 191)->nullable(false);
                $table->text('old_value')->nullable();
                $table->text('new_value')->nullable();
                $table->string('setting_type', 191)->default('lottery')->nullable(false);
                $table->string('change_type', 191)->default('update')->nullable(false);
                $table->bigInteger('changed_by')->unsigned()->nullable();
                $table->string('changed_by_name', 191)->nullable();
                $table->text('description')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
            });
        }

        // EXACT PRODUCTION: nowpayments_api_call_logger table (3 columns)
        if (!Schema::hasTable('nowpayments_api_call_logger')) {
            Schema::create('nowpayments_api_call_logger', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('endpoint', 191)->nullable(false);
                $table->integer('count')->default(0)->nullable(false);
            });
        }

        // Laravel system tables (also in production)
        if (!Schema::hasTable('migrations')) {
            Schema::create('migrations', function (Blueprint $table) {
                $table->increments('id');
                $table->string('migration', 191)->nullable(false);
                $table->integer('batch')->nullable(false);
            });
        }

        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email', 191)->primary();
                $table->string('token', 191)->nullable(false);
                $table->timestamp('created_at')->nullable();
            });
        }

        if (!Schema::hasTable('failed_jobs')) {
            Schema::create('failed_jobs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('uuid', 191)->unique()->nullable(false);
                $table->text('connection')->nullable(false);
                $table->text('queue')->nullable(false);
                $table->longText('payload')->nullable(false);
                $table->longText('exception')->nullable(false);
                $table->timestamp('failed_at')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable(false);
            });
        }

        if (!Schema::hasTable('personal_access_tokens')) {
            Schema::create('personal_access_tokens', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('tokenable_type', 191)->nullable(false);
                $table->bigInteger('tokenable_id')->unsigned()->nullable(false);
                $table->string('name', 191)->nullable(false);
                $table->string('token', 64)->unique()->nullable(false);
                $table->text('abilities')->nullable();
                $table->timestamp('last_used_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
                
                $table->index(['tokenable_type', 'tokenable_id']);
            });
        }

        if (!Schema::hasTable('cache')) {
            Schema::create('cache', function (Blueprint $table) {
                $table->string('key', 191)->primary();
                $table->mediumText('value')->nullable(false);
                $table->integer('expiration')->nullable(false);
            });
        }

        if (!Schema::hasTable('cache_locks')) {
            Schema::create('cache_locks', function (Blueprint $table) {
                $table->string('key', 191)->primary();
                $table->string('owner', 191)->nullable(false);
                $table->integer('expiration')->nullable(false);
            });
        }

        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id', 191)->primary();
                $table->bigInteger('user_id')->unsigned()->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload')->nullable(false);
                $table->integer('last_activity')->nullable(false);
                
                $table->index('user_id');
                $table->index('last_activity');
            });
        }

        if (!Schema::hasTable('jobs')) {
            Schema::create('jobs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('queue', 191)->nullable(false);
                $table->longText('payload')->nullable(false);
                $table->unsignedTinyInteger('attempts')->nullable(false);
                $table->unsignedInteger('reserved_at')->nullable();
                $table->unsignedInteger('available_at')->nullable(false);
                $table->unsignedInteger('created_at')->nullable(false);
                
                $table->index(['queue']);
            });
        }

        if (!Schema::hasTable('job_batches')) {
            Schema::create('job_batches', function (Blueprint $table) {
                $table->string('id', 191)->primary();
                $table->string('name', 191)->nullable(false);
                $table->integer('total_jobs')->nullable(false);
                $table->integer('pending_jobs')->nullable(false);
                $table->integer('failed_jobs')->nullable(false);
                $table->longText('failed_job_ids')->nullable(false);
                $table->mediumText('options')->nullable();
                $table->integer('cancelled_at')->nullable();
                $table->integer('created_at')->nullable(false);
                $table->integer('finished_at')->nullable();
            });
        }

        // Additional tables found in production that don't exist yet
        if (!Schema::hasTable('admin_kycs')) {
            Schema::create('admin_kycs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('admin_kycs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('migrations');
        Schema::dropIfExists('nowpayments_api_call_logger');
        Schema::dropIfExists('configuration_changes');
        Schema::dropIfExists('popup_views');
        Schema::dropIfExists('newsletter_subscribers');
        Schema::dropIfExists('subscribers');
        Schema::dropIfExists('holidays');
        Schema::dropIfExists('forms');
        Schema::dropIfExists('withdraws');
        Schema::dropIfExists('withdraw_methods');
        Schema::dropIfExists('gateway_currencies');
        Schema::dropIfExists('gateways');
    }
};
