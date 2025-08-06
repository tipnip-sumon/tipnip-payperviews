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
            // Check if column exists before renaming
            if (Schema::hasColumn('video_links', 'category_user')) {
                $table->renameColumn('category_user', 'category');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('video_links', function (Blueprint $table) {
            // Check if column exists before renaming back
            if (Schema::hasColumn('video_links', 'category')) {
                $table->renameColumn('category', 'category_user');
            }
        });
    }
};
