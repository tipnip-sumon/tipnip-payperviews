<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\LotterySetting;
use Illuminate\Support\Facades\Hash;

class FixLotteryVirtualUser extends Command
{
    protected $signature = 'lottery:fix-virtual-user';
    protected $description = 'Fix lottery virtual user issue by creating virtual user and updating settings';

    public function handle()
    {
        $this->info('🔧 Fixing lottery virtual user issue...');

        try {
            // Step 1: Create or find virtual user
            $virtualUser = User::where('username', 'lottery_virtual_user')
                              ->orWhere('email', 'virtual@lottery.system')
                              ->first();

            if (!$virtualUser) {
                $this->info('📝 Creating virtual user...');
                $virtualUser = User::create([
                    'firstname' => 'Lottery',
                    'lastname' => 'Virtual System',
                    'username' => 'lottery_virtual_user',
                    'email' => 'virtual@lottery.system',
                    'email_verified_at' => now(),
                    'password' => Hash::make('secure_virtual_password_' . time()),
                    'country_code' => 'XX',
                    'phone' => '+0000000000',
                    'balance' => 0,
                    'status' => 0, // Inactive
                    'verified' => 0, // Not verified
                    'kyc_verified' => 0,
                ]);
                $this->info("✅ Virtual user created with ID: {$virtualUser->id}");
            } else {
                $this->info("✅ Virtual user already exists with ID: {$virtualUser->id}");
            }

            // Step 2: Update or create lottery settings
            $settings = LotterySetting::first();
            if ($settings) {
                $settings->update(['virtual_user_id' => $virtualUser->id]);
                $this->info('✅ Updated existing lottery settings with virtual user ID');
            } else {
                LotterySetting::create([
                    'ticket_price' => 2.00,
                    'draw_day' => 0,
                    'draw_time' => '20:00:00',
                    'is_active' => true,
                    'prize_structure' => [
                        1 => ['name' => 'First Prize', 'percentage' => 50],
                        2 => ['name' => 'Second Prize', 'percentage' => 30],
                        3 => ['name' => 'Third Prize', 'percentage' => 20],
                    ],
                    'max_tickets_per_user' => 100,
                    'min_tickets_for_draw' => 10,
                    'admin_commission_percentage' => 10.00,
                    'auto_draw' => true,
                    'auto_prize_distribution' => true,
                    'ticket_expiry_hours' => 168,
                    'virtual_user_id' => $virtualUser->id,
                ]);
                $this->info('✅ Created new lottery settings with virtual user ID');
            }

            // Clear cache
            \Illuminate\Support\Facades\Cache::forget('lottery_settings');
            $this->info('✅ Cleared lottery settings cache');

            $this->info('🎉 Lottery virtual user issue fixed successfully!');
            $this->info("Virtual User ID: {$virtualUser->id}");
            $this->info("Username: {$virtualUser->username}");

        } catch (\Exception $e) {
            $this->error('❌ Failed to fix lottery virtual user: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
