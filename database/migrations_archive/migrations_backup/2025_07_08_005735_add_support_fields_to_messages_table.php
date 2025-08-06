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
        Schema::table('messages', function (Blueprint $table) {
            if (!Schema::hasColumn('messages', 'attachments')) {
                $table->text('attachments')->nullable()->after('attachment_path');
            }
            if (!Schema::hasColumn('messages', 'metadata')) {
                $table->text('metadata')->nullable()->after('attachments');
            }
            if (!Schema::hasColumn('messages', 'category')) {
                $table->string('category')->nullable()->after('priority');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['attachments', 'metadata', 'category']);
        });
    }
};
