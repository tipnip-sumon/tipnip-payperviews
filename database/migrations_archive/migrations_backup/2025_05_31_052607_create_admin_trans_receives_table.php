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
        Schema::create('admin_trans_receives', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id');
            $table->string('user_transfer',255);
            $table->integer('amount');
            $table->boolean('status')->default(0)->comment('0 = pending, 1 = active, 2 = suspended');
            $table->string('user_receive', 255);
            $table->string('note')->nullable();
            $table->timestamps();
            
            $table->foreign('admin_id')->references('id')->on('admin_trans_receives')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_trans_receives');
    }
};
