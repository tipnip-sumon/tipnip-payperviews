<?php

namespace Database\Seeders;

use App\Models\CommissionLevelSetting;
use App\Models\Plan;
use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed production default data first
                $this->call([
                    UserSeeder::class, // Ensure users are created first
                    GeneralSettingsSeeder::class, // General settings
                    PlanSeeder::class, // Plans
                    LotterySettingsSeeder::class, // Lottery settings
                    CommissionLevelSettingsSeeder::class, // Commission levels
                    VideoLinkSeeder::class, // Video links data
                    GatewaySeeder::class, // Payment gateways like NOWPayments, CoinPayments etc.
                    GatewayCurrencySeeder::class, // Gateway currencies for payment methods
                    MarkdownFileSeeder::class, // Markdown files
                    HistoricalLotteryDrawSeeder::class, // Historical lottery draws
                    ModalSettingsSeeder::class, // Modal settings
                    DemoModalSeeder::class, // Demo modals
                    WithdrawMethodSeeder::class,
                    ModalSettingsSeeder::class,
            ]);
    }
}
