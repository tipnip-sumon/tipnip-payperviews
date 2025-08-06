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
        // Transactions table
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('trx', 40)->unique();
            $table->decimal('amount', 28, 8)->default(0.00000000);
            $table->decimal('charge', 28, 8)->default(0.00000000);
            $table->decimal('post_balance', 28, 8)->default(0.00000000);
            $table->enum('trx_type', ['+', '-']);
            $table->string('details', 255)->nullable();
            $table->string('remark', 100)->nullable();
            $table->string('wallet_type', 40)->nullable()->comment('deposit_wallet, interest_wallet');
            $table->timestamps();

            $table->index(['user_id', 'trx_type']);
            $table->index(['trx']);
            $table->index(['created_at', 'trx_type']);
        });

        // Deposits table
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('gateway_id')->nullable()->constrained('gateways')->onDelete('set null');
            $table->string('trx', 40)->unique();
            $table->decimal('amount', 28, 8)->default(0.00000000);
            $table->decimal('charge', 28, 8)->default(0.00000000);
            $table->decimal('rate', 28, 8)->default(0.00000000);
            $table->decimal('final_amo', 28, 8)->default(0.00000000);
            $table->text('detail')->nullable();
            $table->string('btc_amo', 255)->nullable();
            $table->string('btc_wallet', 255)->nullable();
            $table->enum('status', ['initiated', 'pending', 'complete', 'cancel'])->default('initiated');
            $table->datetime('success_at')->nullable();
            $table->string('from_api', 125)->nullable();
            $table->foreignId('admin_id')->nullable()->constrained()->onDelete('set null');
            $table->text('admin_feedback')->nullable();
            $table->string('method_code', 50)->nullable();
            $table->string('method_currency', 20)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['trx']);
            $table->index(['status', 'created_at']);
        });

        // Withdrawals table
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('method_id')->nullable()->constrained('withdraw_methods')->onDelete('set null');
            $table->string('trx', 40)->unique();
            $table->decimal('amount', 28, 8)->default(0.00000000);
            $table->decimal('charge', 28, 8)->default(0.00000000);
            $table->decimal('rate', 28, 8)->default(0.00000000);
            $table->decimal('final_amo', 28, 8)->default(0.00000000);
            $table->decimal('after_charge', 28, 8)->default(0.00000000);
            $table->text('withdraw_information')->nullable();
            $table->enum('status', ['initiated', 'pending', 'complete', 'cancel'])->default('initiated');
            $table->string('withdraw_type', 20)->default('balance');
            $table->foreignId('admin_id')->nullable()->constrained()->onDelete('set null');
            $table->text('admin_feedback')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['trx']);
            $table->index(['status', 'created_at']);
        });

        // Withdraw methods table
        Schema::create('withdraw_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name', 40);
            $table->string('image', 255)->nullable();
            $table->decimal('rate', 28, 8)->default(1.00000000);
            $table->decimal('min_limit', 28, 8)->default(1.00000000);
            $table->decimal('max_limit', 28, 8)->default(1000.00000000);
            $table->decimal('fixed_charge', 28, 8)->default(0.00000000);
            $table->decimal('percent_charge', 5, 2)->default(0.00);
            $table->text('description')->nullable();
            $table->json('user_data')->nullable();
            $table->text('instructions')->nullable();
            $table->boolean('status')->default(true);
            $table->string('currency', 40);
            $table->decimal('delay', 11, 0)->default(0);
            $table->timestamps();

            $table->index(['status', 'currency']);
        });

        // Payments table
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('payment_method', 100);
            $table->decimal('amount', 28, 8);
            $table->string('currency', 10)->default('USD');
            $table->string('status', 20)->default('pending');
            $table->string('transaction_id', 255)->nullable();
            $table->string('reference', 255)->nullable();
            $table->json('payment_data')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['transaction_id']);
            $table->index(['reference']);
        });

        // Gateways table
        Schema::create('gateways', function (Blueprint $table) {
            $table->id();
            $table->string('form_id', 40);
            $table->string('code', 40);
            $table->string('name', 40);
            $table->string('alias', 40)->unique();
            $table->string('image', 255)->nullable();
            $table->boolean('status')->default(true);
            $table->json('gateway_parameters')->nullable();
            $table->json('supported_currencies')->nullable();
            $table->json('crypto')->nullable();
            $table->text('extra')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['alias', 'status']);
        });

        // Gateway currencies table
        Schema::create('gateway_currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 40);
            $table->string('currency', 40);
            $table->string('symbol', 40);
            $table->foreignId('method_code')->constrained('gateways')->onDelete('cascade');
            $table->decimal('min_amount', 28, 8)->default(0.00000000);
            $table->decimal('max_amount', 28, 8)->default(0.00000000);
            $table->decimal('fixed_charge', 28, 8)->default(0.00000000);
            $table->decimal('percent_charge', 5, 2)->default(0.00);
            $table->decimal('rate', 28, 8)->default(0.00000000);
            $table->text('image')->nullable();
            $table->timestamps();

            $table->index(['method_code', 'currency']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gateway_currencies');
        Schema::dropIfExists('gateways');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('withdraw_methods');
        Schema::dropIfExists('withdrawals');
        Schema::dropIfExists('deposits');
        Schema::dropIfExists('transactions');
    }
};
