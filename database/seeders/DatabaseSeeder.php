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
                    GatewayCurrencySeeder::class, // Gateway currencies for payment methods
            ]);
    }
}
