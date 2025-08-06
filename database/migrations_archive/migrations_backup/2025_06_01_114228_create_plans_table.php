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
        Schema::create('plans', function (Blueprint $table) {
            $table->id(); 
            $table->string('name', 40);
            $table->decimal('minimum', 28, 8)->default(0.00000000);
            $table->decimal('maximum', 28, 8)->default(0.00000000);
            $table->decimal('fixed_amount', 28, 8)->default(0.00000000);
            $table->decimal('interest', 28, 8)->default(0.00000000);
            $table->boolean('interest_type')->default(false)->nullable()->comment("1 = '%' / 0 ='currency'"); 
            $table->string('time', 40)->default(0)->comment('e.g., 30 days, 60 days, etc.');
            $table->string('time_name', 40)->nullable()->comment('e.g., days, weeks, months, years');
            $table->boolean('status')->default(true);
            $table->boolean('featured')->default(false);
            $table->boolean('capital_back')->nullable()->default(false);
            $table->boolean('lifetime')->nullable()->default(false);
            $table->string('repeat_time', 40)->nullable();
            $table->integer('daily_video_limit')->nullable()->default(5)->comment('Number of daily video assignments allowed');
            $table->text('description')->nullable();
            $table->decimal('video_earning_rate', 8, 4)->default(0.0010);
            $table->boolean('video_access_enabled')->default(true); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
