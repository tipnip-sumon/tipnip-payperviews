<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LotterySettingsSeeder extends Seeder
{
    public function run()
    {
        // Check if settings already exist
        if (DB::table('lottery_settings')->count() > 0) {
            $this->command->info('Lottery settings already exist.');
            return;
        }


        DB::table('lottery_settings')->insert([
            'ticket_price' => 2.00,
            'draw_day' => 0, // Sunday
            'draw_time' => '20:00:00',
            'is_active' => true,
            'prize_structure' => json_encode([
                '1' => ['name' => '1st Prize', 'type' => 'fixed_amount', 'amount' => '1000'],
                '2' => ['name' => '2nd Prize', 'type' => 'fixed_amount', 'amount' => '300'],
                '3' => ['name' => '3rd Prize', 'type' => 'fixed_amount', 'amount' => '10100'],
            ]),
            'max_tickets_per_user' => 100,
            'min_tickets_for_draw' => 10,
            'admin_commission_percentage' => 10.00,
            'auto_draw' => true,
            'auto_prize_distribution' => true,
            'auto_generate_draws' => true,
            'auto_generation_frequency' => 'weekly',
            'auto_generation_schedule' => null, // Use default schedule
            'enable_virtual_tickets' => true,
            'min_virtual_tickets' => 100,
            'max_virtual_tickets' => 1000,
            'virtual_ticket_percentage' => 80.00,
            'enable_manual_winner_selection' => true,
            'default_winner_pool' => null, // Use default pool
            'auto_execute_draws' => true,
            'auto_execute_delay_minutes' => 0,
            'next_auto_draw' => null, // Use default next draw time
            'ticket_expiry_hours' => 1, // 1 hour
            'auto_claim_days' => 30,
            'auto_refund_cancelled' => true,
            'prize_claim_deadline' => 30, // 30 days
            'allow_multiple_winners_per_place' => false,
            'prize_distribution_type' => 'fixed_amount',
            'manual_winner_selection' => false,
            'show_virtual_tickets' => false,
            'virtual_ticket_multiplier' => 0,
            'virtual_ticket_base' => 100,
            'virtual_user_id' => User::where('username', 'lottery_virtual_user')->value('id'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Lottery settings created successfully!');
    }
}
