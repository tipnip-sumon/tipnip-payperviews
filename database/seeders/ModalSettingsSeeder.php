<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModalSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modalSettings = [
            [
                'modal_name' => 'web_install_suggestion',
                'title' => 'Install PayPerViews App',
                'subtitle' => 'Get the best mobile experience',
                'heading' => 'Install Our PWA App',
                'description' => 'Get faster access, offline capabilities, and native app experience right from your browser!',
                'is_active' => true,
                'target_users' => 'all',
                'show_frequency' => 'daily',
                'max_shows' => 7,
                'delay_seconds' => 3,
                'additional_settings' => json_encode([
                    'show_on_mobile_only' => false,
                    'show_on_desktop_only' => false,
                    'minimum_session_time' => 30, // seconds
                    'exclude_routes' => ['login', 'register'],
                    'include_routes' => [], // empty means all routes
                    'custom_css' => '',
                    'custom_js' => ''
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'modal_name' => 'welcome_new_user',
                'title' => 'Welcome to PayPerViews!',
                'subtitle' => 'Let\'s get you started',
                'heading' => 'Complete Your Setup',
                'description' => 'Follow these quick steps to maximize your earnings and get the most out of our platform.',
                'is_active' => true,
                'target_users' => 'new_users',
                'show_frequency' => 'once',
                'max_shows' => 1,
                'delay_seconds' => 5,
                'additional_settings' => json_encode([
                    'show_after_registration' => true,
                    'show_after_email_verification' => true,
                    'required_actions' => ['complete_profile', 'first_deposit', 'first_investment'],
                    'completion_reward' => 5.00
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'modal_name' => 'investment_reminder',
                'title' => 'Start Earning Today!',
                'subtitle' => 'Your first investment awaits',
                'heading' => 'Ready to Invest?',
                'description' => 'You have funds available in your wallet. Start earning passive income with our investment plans!',
                'is_active' => false, // Disabled by default
                'target_users' => 'verified',
                'show_frequency' => 'weekly',
                'max_shows' => 3,
                'delay_seconds' => 10,
                'additional_settings' => json_encode([
                    'minimum_balance' => 10.00,
                    'exclude_if_has_investments' => true,
                    'show_on_dashboard_only' => true
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        foreach ($modalSettings as $setting) {
            DB::table('modal_settings')->updateOrInsert(
                ['modal_name' => $setting['modal_name']],
                $setting
            );
        }

        $this->command->info('Modal settings seeded successfully!');
    }
}
