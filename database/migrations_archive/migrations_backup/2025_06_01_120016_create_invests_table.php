<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invests', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('user_id')->nullable()->default(false);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // $table->foreignId('plan_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('plan_id')->nullable()->default(false);
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade');
            $table->decimal('amount', 28, 8)->default(0.00000000);
            $table->decimal('interest', 28, 8)->default(0.00000000);
            $table->decimal('should_pay', 28, 8)->default(0.00000000);
            $table->decimal('paid', 28, 8)->default(0.00000000);
            $table->integer('period')->default(false)->nullable();
            $table->string('hours', 40);
            $table->string('time_name', 40);
            $table->integer('return_rec_time')->default(false);
            $table->timestamp('next_time')->default(DB::raw('CURRENT_TIMESTAMP'))->useCurrentOnUpdate();
            $table->timestamp('last_time')->nullable();
            $table->boolean('status')->default(true);
            $table->boolean('capital_status')->default(false)->comment('1 = YES & 0 = NO');
            $table->string('trx', 40)->nullable();
            $table->string('wallet_type', 40)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invests');
    }
};
