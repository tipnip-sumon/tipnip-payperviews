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
        Schema::table('deposits', function (Blueprint $table) {
            if (!Schema::hasColumn('deposits', 'payment_id', 'customer_email')) {
                $table->bigInteger('payment_id')->nullable()->after('method_code');
                $table->string('customer_email')->nullable()->after('detail');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            if (Schema::hasColumn('deposits', 'payment_id', 'customer_email')) {
                $table->dropColumn('payment_id');
                $table->dropColumn('customer_email');
            }
        });
    }
};
