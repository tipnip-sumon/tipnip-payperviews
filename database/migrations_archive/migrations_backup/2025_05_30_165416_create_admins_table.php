<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use App\Models\Admin;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email', 40)->unique();
            $table->string('username', 40)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('image', 255)->nullable();
            $table->string('password', 255);
            $table->string('remember_token', 255)->nullable();
            
            // Financial columns
            $table->decimal('balance', 15, 2)->default(0.00);
            $table->decimal('total_deposited', 15, 2)->default(0.00);
            $table->decimal('total_withdrawn', 15, 2)->default(0.00);
            $table->decimal('total_transferred', 15, 2)->default(0.00);
            
            // Admin profile and permissions
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('role', 20)->default('admin');
            $table->json('permissions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_super_admin')->default(false);
            
            // Security columns
            $table->string('two_factor_secret', 255)->nullable();
            $table->string('two_factor_recovery_codes', 255)->nullable();
            $table->timestamp('two_factor_confirmed_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();
            $table->string('last_login_user_agent', 255)->nullable();
            
            // Additional tracking
            $table->integer('login_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
        });
        Admin::create([
            'name' => 'Super Admin',
            'email' => 'sumonmti498@gmail.com',
            'username' => 'admin',
            'image' => 'default.png',
            'password' => Hash::make('123456'),
            'remember_token' => null,
            'balance' => 100000.00,
            'phone' => '+1234567890',
            'address' => 'Main Office, City, Country',
            'role' => 'super_admin',
            'permissions' => json_encode([
                'users' => ['view', 'create', 'edit', 'delete'],
                'transactions' => ['view', 'create', 'edit', 'delete'],
                'deposits' => ['view', 'approve', 'reject'],
                'withdrawals' => ['view', 'approve', 'reject'],
                'transfers' => ['view', 'create'],
                'settings' => ['view', 'edit'],
                'reports' => ['view', 'export'],
                'video_links' => ['view', 'create', 'edit', 'delete'],
                'plans' => ['view', 'create', 'edit', 'delete'],
                'kyc' => ['view', 'approve', 'reject'],
                'notifications' => ['view', 'create', 'send'],
                'admin_management' => ['view', 'create', 'edit', 'delete']
            ]),
            'is_active' => true,
            'is_super_admin' => true,
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
