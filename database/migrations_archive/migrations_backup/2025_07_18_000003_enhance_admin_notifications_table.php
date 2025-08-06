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
        if (!Schema::hasTable('admin_notifications')) {
            Schema::create('admin_notifications', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('admin_id')->nullable(); // null means global notification
                $table->string('title');
                $table->text('message');
                $table->enum('type', ['info', 'success', 'warning', 'danger', 'primary'])->default('info');
                $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
                $table->string('icon')->default('fas fa-bell');
                $table->boolean('read')->default(false);
                $table->timestamp('read_at')->nullable();
                $table->string('action_url')->nullable();
                $table->string('action_text')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
                $table->index(['admin_id', 'read']);
                $table->index(['created_at']);
                $table->index(['priority', 'read']);
            });
        } else {
            // Update existing table to add missing columns
            Schema::table('admin_notifications', function (Blueprint $table) {
                if (!Schema::hasColumn('admin_notifications', 'admin_id')) {
                    $table->unsignedBigInteger('admin_id')->nullable()->after('id');
                    $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
                }
                if (!Schema::hasColumn('admin_notifications', 'message')) {
                    $table->text('message')->after('title');
                }
                if (!Schema::hasColumn('admin_notifications', 'type')) {
                    $table->enum('type', ['info', 'success', 'warning', 'danger', 'primary'])->default('info')->after('message');
                }
                if (!Schema::hasColumn('admin_notifications', 'priority')) {
                    $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal')->after('type');
                }
                if (!Schema::hasColumn('admin_notifications', 'icon')) {
                    $table->string('icon')->default('fas fa-bell')->after('priority');
                }
                if (!Schema::hasColumn('admin_notifications', 'read_at')) {
                    $table->timestamp('read_at')->nullable()->after('is_read');
                }
                if (!Schema::hasColumn('admin_notifications', 'action_url')) {
                    $table->string('action_url')->nullable()->after('read_at');
                }
                if (!Schema::hasColumn('admin_notifications', 'action_text')) {
                    $table->string('action_text')->nullable()->after('action_url');
                }
                if (!Schema::hasColumn('admin_notifications', 'expires_at')) {
                    $table->timestamp('expires_at')->nullable()->after('action_text');
                }
                if (!Schema::hasColumn('admin_notifications', 'metadata')) {
                    $table->json('metadata')->nullable()->after('expires_at');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_notifications');
    }
};
