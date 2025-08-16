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
        Schema::table('video_links', function (Blueprint $table) {
            // Add embed_url column after description
            $table->text('embed_url')->nullable()->after('description');
            
            // Add earning_per_view column after cost_per_click  
            $table->decimal('earning_per_view', 8, 2)->nullable()->after('cost_per_click');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('video_links', function (Blueprint $table) {
            // Remove the columns
            $table->dropColumn(['embed_url', 'earning_per_view']);
        });
    }
};
