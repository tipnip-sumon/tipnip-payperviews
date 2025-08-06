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
        Schema::create('withdraw_methods', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form_id')->default(false);
            $table->foreign('form_id')->references('id')->on('forms')->onDelete('cascade');
            $table->string('name', 40)->nullable();
            $table->decimal('min_limit', 28, 8)->nullable()->default(0.00000000);
            $table->decimal('max_limit', 28, 8)->default(0.00000000);
            $table->decimal('fixed_charge', 28, 8)->nullable()->default(0.00);
            $table->decimal('rate', 28, 8)->nullable()->default(0.00000000);
            $table->decimal('percent_charge', 5, 2)->nullable()->default(0.00);
            $table->string('currency', 40)->nullable();
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdraw_methods');
    }
};
