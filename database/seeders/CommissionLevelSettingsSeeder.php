<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CommissionLevelSetting;

class CommissionLevelSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Initialize default commission levels
        CommissionLevelSetting::initializeDefaults();
        
        $this->command->info('Default commission level settings have been seeded.');
    }
}
