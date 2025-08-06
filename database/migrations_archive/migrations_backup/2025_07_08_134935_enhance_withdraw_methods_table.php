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
        Schema::table('withdraw_methods', function (Blueprint $table) {
            if (!Schema::hasColumn('withdraw_methods', 'name')) {
                $table->string('name')->after('id');
            }
            if (!Schema::hasColumn('withdraw_methods', 'method_key')) {
                $table->string('method_key')->unique()->after('name');
            }
            if (!Schema::hasColumn('withdraw_methods', 'status')) {
                $table->boolean('status')->default(true)->after('method_key');
            }
            if (!Schema::hasColumn('withdraw_methods', 'min_amount')) {
                $table->decimal('min_amount', 28, 8)->default(1)->after('status');
            }
            if (!Schema::hasColumn('withdraw_methods', 'max_amount')) {
                $table->decimal('max_amount', 28, 8)->default(10000)->after('min_amount');
            }
            if (!Schema::hasColumn('withdraw_methods', 'daily_limit')) {
                $table->decimal('daily_limit', 28, 8)->default(5000)->after('max_amount');
            }
            if (!Schema::hasColumn('withdraw_methods', 'charge_type')) {
                $table->enum('charge_type', ['fixed', 'percent'])->default('fixed')->after('daily_limit');
            }
            if (!Schema::hasColumn('withdraw_methods', 'charge')) {
                $table->decimal('charge', 28, 8)->default(0)->after('charge_type');
            }
            if (!Schema::hasColumn('withdraw_methods', 'description')) {
                $table->text('description')->nullable()->after('charge');
            }
            if (!Schema::hasColumn('withdraw_methods', 'icon')) {
                $table->string('icon')->nullable()->after('description');
            }
            if (!Schema::hasColumn('withdraw_methods', 'processing_time')) {
                $table->string('processing_time')->default('1-3 business days')->after('icon');
            }
            if (!Schema::hasColumn('withdraw_methods', 'currency')) {
                $table->string('currency')->default('USD')->after('processing_time');
            }
            if (!Schema::hasColumn('withdraw_methods', 'instructions')) {
                $table->text('instructions')->nullable()->after('currency');
            }
            if (!Schema::hasColumn('withdraw_methods', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('instructions');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('withdraw_methods', function (Blueprint $table) {
            $table->dropColumn([
                'name', 'method_key', 'status', 'min_amount', 'max_amount', 
                'daily_limit', 'charge_type', 'charge', 'description', 'icon',
                'processing_time', 'currency', 'instructions', 'sort_order'
            ]);
        });
    }
};
