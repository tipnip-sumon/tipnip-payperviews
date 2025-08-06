<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Mime\Part\Multipart\AlternativePart;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'last_login_user_agent','last_login_ip', 'login_attempts', 'locked_until')) {
                $table->string('last_login_user_agent')->nullable()->after('remember_token');
                $table->string('last_login_ip', 45)->nullable()->after('last_login_user_agent');
                $table->integer('login_attempts')->default(0)->after('last_login_ip');
                $table->timestamp('locked_until')->nullable()->after('login_attempts');
            }
            if (Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('locked_until')->change();
            } else {
                $table->timestamp('last_login_at')->nullable()->after('locked_until');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'last_login_ip')) {
                $table->dropColumn('last_login_ip');
            }
            if (Schema::hasColumn('users', 'login_attempts')) {
                $table->dropColumn('login_attempts');
            }
            if (Schema::hasColumn('users', 'locked_until')) {
                $table->dropColumn('locked_until');
            }
            if (Schema::hasColumn('users', 'last_login_at')) {
                $table->dropColumn('last_login_at');
            }
            if (Schema::hasColumn('users', 'last_login_user_agent')) {
                $table->dropColumn('last_login_user_agent');
            }
        });
    }
};
