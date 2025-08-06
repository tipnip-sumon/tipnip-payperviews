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
        Schema::create('gateway_currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 40)->nullable();
            $table->string('currency', 40)->nullable();
            $table->string('symbol', 40)->nullable();
            $table->integer('method_code')->nullable();
            $table->string('gateway_alias', 40)->nullable();
            $table->decimal('min_amount', 28, 8)->default(0.00000000);
            $table->decimal('max_amount', 28, 8)->default(0.00000000);
            $table->decimal('percent_charge', 5, 2)->default(0.00);
            $table->decimal('fixed_charge', 28, 8)->default(0.00000000);
            $table->decimal('rate', 28, 8)->default(0.0000000);
            $table->string('image', 255)->nullable();
            $table->text('gateway_parameter')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gateway_currencies');
    }
};
