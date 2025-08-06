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
        Schema::table('invests', function (Blueprint $table) {
            $table->decimal('actual_paid', 20, 8)->nullable()->after('amount')->comment('Amount actually paid by user after discounts');
            $table->decimal('token_discount', 20, 8)->default(0)->after('actual_paid')->comment('Discount amount from special tokens');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invests', function (Blueprint $table) {
            $table->dropColumn(['actual_paid', 'token_discount']);
        });
    }
};
