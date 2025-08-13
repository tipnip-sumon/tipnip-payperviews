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
            $table->string('name');
            $table->string('currency', 10);
            $table->string('symbol', 10);
            $table->string('method_code', 50)->index();
            $table->string('gateway_alias')->nullable();
            $table->decimal('min_amount', 15, 8)->default(0);
            $table->decimal('max_amount', 15, 8)->default(0);
            $table->decimal('percent_charge', 5, 2)->default(0);
            $table->decimal('fixed_charge', 15, 8)->default(0);
            $table->decimal('rate', 15, 8)->default(1);
            $table->string('image')->nullable();
            $table->json('gateway_parameter')->nullable();
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index(['currency', 'method_code']);
            $table->index('gateway_alias');
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
