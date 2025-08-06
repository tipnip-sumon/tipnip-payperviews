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
        Schema::create('configuration_changes', function (Blueprint $table) {
            $table->id();
            $table->string('setting_name');
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->string('setting_type')->default('lottery'); // lottery, general, commission, etc.
            $table->string('change_type')->default('update'); // update, create, delete, enable, disable
            $table->unsignedBigInteger('changed_by')->nullable();
            $table->string('changed_by_name')->nullable(); // Store name for when user is deleted
            $table->text('description')->nullable();
            $table->json('metadata')->nullable(); // Additional context about the change
            $table->timestamps();

            // Add indexes for better performance
            $table->index('setting_name');
            $table->index('setting_type');
            $table->index('change_type');
            $table->index('changed_by');
            $table->index('created_at');

            // Foreign key constraint
            $table->foreign('changed_by')->references('id')->on('admins')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuration_changes');
    }
};
