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
        Schema::create('commission_level_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('level')->unique(); // Referral level (1-10)
            $table->decimal('percentage', 5, 2); // Commission percentage (e.g., 3.00 for 3%)
            $table->boolean('is_active')->default(true); // Whether this level is active
            $table->text('description')->nullable(); // Optional description for this level
            $table->timestamps();
            
            // Indexes
            $table->index(['level', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commission_level_settings');
    }
};
