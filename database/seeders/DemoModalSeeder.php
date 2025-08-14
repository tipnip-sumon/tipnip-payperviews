<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoModalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing demo modals
        DB::table('modal_settings')->where('modal_name', 'LIKE', 'demo_%')->delete();

        // Insert demo modals
        $demoModals = [
            [
                'modal_name' => 'demo_welcome',
                'title' => 'Welcome to TipNip!',
                'subtitle' => 'Your journey to financial freedom starts here',
                'heading' => 'Get Started Today',
                'description' => '<p>Welcome to our platform! We\'re excited to have you join our community.</p><p>Here are some quick tips to get you started:</p><ul><li>Complete your profile setup</li><li>Explore our investment plans</li><li>Join our community discussions</li></ul>',
                'is_active' => true,
                'target_users' => 'new_users',
                'show_frequency' => 'once',
                'max_shows' => 1,
                'delay_seconds' => 3,
                'additional_settings' => json_encode([
                    'show_on_mobile_only' => false,
                    'show_on_desktop_only' => false,
                    'minimum_session_time' => 5,
                    'exclude_routes' => ['/login', '/register'],
                    'include_routes' => [],
                    'custom_css' => '',
                    'custom_js' => ''
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'modal_name' => 'demo_promotion',
                'title' => 'Special Offer!',
                'subtitle' => 'Limited time promotion',
                'heading' => '🎉 Bonus Investment Returns',
                'description' => '<div class="text-center"><h5>Get <strong>20% Extra Returns</strong> on your first investment!</h5><p>This exclusive offer is available for new investors only.</p><p class="text-muted small">Terms and conditions apply. Offer valid until end of month.</p></div>',
                'is_active' => true,
                'target_users' => 'all',
                'show_frequency' => 'daily',
                'max_shows' => 3,
                'delay_seconds' => 10,
                'additional_settings' => json_encode([
                    'show_on_mobile_only' => false,
                    'show_on_desktop_only' => false,
                    'minimum_session_time' => 30,
                    'exclude_routes' => ['/admin', '/login', '/register'],
                    'include_routes' => ['/dashboard', '/invest', '/'],
                    'custom_css' => '.modal-header-custom { background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%); }',
                    'custom_js' => ''
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'modal_name' => 'demo_mobile_app',
                'title' => 'Download Our Mobile App',
                'subtitle' => 'Trade on the go',
                'heading' => '📱 Mobile Trading Made Easy',
                'description' => '<div class="text-center"><p>Get our mobile app for seamless trading experience!</p><div class="d-flex justify-content-center gap-3 mt-3"><button class="btn btn-dark"><i class="fab fa-apple"></i> App Store</button><button class="btn btn-success"><i class="fab fa-google-play"></i> Play Store</button></div></div>',
                'is_active' => true,
                'target_users' => 'all',
                'show_frequency' => 'weekly',
                'max_shows' => 2,
                'delay_seconds' => 15,
                'additional_settings' => json_encode([
                    'show_on_mobile_only' => true,
                    'show_on_desktop_only' => false,
                    'minimum_session_time' => 60,
                    'exclude_routes' => ['/admin'],
                    'include_routes' => [],
                    'custom_css' => '.modal-header-custom { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }',
                    'custom_js' => ''
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('modal_settings')->insert($demoModals);

        $this->command->info('Demo modals seeded successfully!');
    }
}
