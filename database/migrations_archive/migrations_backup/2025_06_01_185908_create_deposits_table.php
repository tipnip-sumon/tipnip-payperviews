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
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->default(0);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('plan_id')->default(0);
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade');
            $table->unsignedBigInteger('method_code')->default(false);
            // $table->foreign('method_code')->references('code')->on('gateways')->onDelete('cascade');
            $table->decimal('amount', 28, 8)->default(0.00000000);
            $table->string('method_currency', 40)->nullable();
            $table->decimal('charge', 28, 8)->default(0.00000000);
            $table->decimal('rate', 28, 8)->default(0.00000000);
            $table->decimal('final_amo', 28, 8)->default(0.00000000);
            $table->text('detail')->nullable();
            $table->string('btc_amo', 255)->nullable();
            $table->string('btc_wallet', 255)->nullable();
            $table->string('trx', 40)->nullable();
            $table->integer('try')->default(false);
            $table->boolean('status')->default(false)->comment('1=>success, 2=>pending, 3=>cancel');
            $table->boolean('from_api')->default(false);
            $table->string('admin_feedback',255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposits');
    }
};
