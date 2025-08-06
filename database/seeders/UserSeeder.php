<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if admin user already exists
        $adminUser = Admin::where('username', 'superadmin')
                          ->orWhere('email', 'mainur22@gmail.com')
                          ->first();
        if (!$adminUser) {
            Admin::create([
                'name' => 'Super Admin',
                'email' => 'mainur22@gmail.com',
                'username' => 'superadmin',
                'password' => Hash::make('123456'), // Use a secure password
                'remember_token' => null,
                'balance' => 1000000.00,
                'total_deposited' => 0.00,
                'total_withdrawn' => 0.00,
                'total_transferred' => 0.00,
                'phone' => '+1234567890',
                'address' => 'Main Office, City, Country',
                'role' => 'super_admin',
                'permissions' => '{"users":["view","create","edit","delete"],"transactions":["view","create","edit","delete"],"deposits":["view","approve","reject"],"withdrawals":["view","approve","reject"],"transfers":["view","create"],"settings":["view","edit"],"reports":["view","export"],"video_links":["view","create","edit","delete"],"plans":["view","create","edit","delete"],"kyc":["view","approve","reject"],"notifications":["view","create","send"],"admin_management":["view","create","edit","delete"]}',
                'is_active' => 1,
                'is_super_admin' => 1,
                'two_factor_secret' => null,
                'two_factor_recovery_codes' => null,
                'two_factor_confirmed_at' => null,
                'login_attempts' => 0,
                'locked_until' => null,
                'notes' => null
            ]);
        }
        // Check if virtual user already exists
        $virtualUser = User::where('username', 'lottery_virtual_user')
                          ->orWhere('email', 'virtual@lottery.system')
                          ->first();
        if (!$virtualUser) {
            User::create([
                'firstname' => 'Lottery',
                'lastname' => 'Virtual System',
                'username' => 'lottery_virtual_user',
                'referral_hash' => hash('sha256', 'lottery_virtual_user'),
                'email' => 'virtual@lottery.system',
                'email_verified_at' => now(),
                'password' => Hash::make('secure_virtual_password_' . time()),
                'country_code' => 'XX',
                'mobile' => '+0000000000',
                'balance' => 0,
                'status' => 0, // Inactive
                'ev' => 0, // Not verified
                'kv' => 0, // KYC not verified
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->command->info('Virtual user created successfully for lottery system.');
        } else {
            $this->command->info('Virtual user already exists.');
        }
        // Check if main user already exists
        $mainUser = User::where('username', 'mainur_sir')
                          ->orWhere('email', 'mainur22@gmail.com')
                          ->first();
        if (!$mainUser) {
            User::create([
                'firstname' => 'Mainur',
                'lastname' => 'Islam',
                'username' => 'mainur_sir',
                'referral_hash' => hash('sha256', 'mainur_sir'),
                'email' => 'mainur22@gmail.com',
                'email_verified_at' => now(),
                'country' => 'Bangladesh',
                'mobile' => '01738531695',
                'ref_by' => $virtualUser ? $virtualUser->id : 1,
                'deposit_wallet' => 1000.00000000,
                'password' => Hash::make('12345678'), // Use a secure password
                'address' => 'Dhaka, Bangladesh',
                'status' => true,
                'balance' => 0,
                'status' => 1, // Inactive
                'ev' => 1, // Not verified
                'kv' => 1, // KYC not verified
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info('Main user created successfully.');
        } else {
            $this->command->info('Main user already exists.');
        }
    }
}
