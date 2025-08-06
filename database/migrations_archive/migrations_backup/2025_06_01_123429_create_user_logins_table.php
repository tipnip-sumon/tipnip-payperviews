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
        Schema::create('user_logins', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('user_id')->default(false);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('user_ip', 40)->nullable()->comment('IPv4 or IPv6 address');
            $table->string('city', 40)->nullable();
            $table->string('country', 40)->nullable();
            $table->string('country_code', 40)->nullable();
            $table->string('longitude', 40)->nullable();
            $table->string('latitude', 40)->nullable();
            $table->string('browser', 40)->nullable();
            $table->string('os', 40)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_logins');
    }
};
