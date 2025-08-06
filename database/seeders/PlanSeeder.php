<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if plans already exist
        if (Plan::count() > 0) {
            $this->command->info('Plans already exist. Updating existing plans with proper time values...');
            
            // Update existing plans to have proper time values
            Plan::whereNull('time')->orWhere('time', '')->update(['time' => '24']);
            
            // Update plans with string time values to numeric hours
            $plans = Plan::all();
            foreach ($plans as $plan) {
                if (!is_numeric($plan->time)) {
                    // Convert time string to hours
                    $hours = $this->convertTimeToHours($plan->time);
                    $plan->update(['time' => $hours]);
                }
            }
            
            $this->command->info('Existing plans updated successfully.');
            return;
        }

        // Create default plans if none exist
        Plan::create([
            'name' => 'Starter Plan',
            'minimum' => 25.00,
            'maximum' => 25.99,
            'fixed_amount' => 25.00,
            'interest' => 0.10,
            'interest_type' => 1, // Percentage
            'time' => '24', // 24 hours
            'time_name' => 'hours',
            'status' => 1,
            'featured' => 0,
            'capital_back' => 1,
            'lifetime' => 0,
            'repeat_time' => '30',
            'daily_video_limit' => 5,
            'description' => 'Perfect for beginners. Watch 5 videos daily and earn steady income.',
            'video_earning_rate' => 0.1000,
            'video_access_enabled' => 1
        ]);

        Plan::create([
            'name' => 'Basic Plan',
            'minimum' => 50.00,
            'maximum' => 50.00,
            'fixed_amount' => 50.00,
            'interest' => 0.10,
            'interest_type' => 1, // Percentage
            'time' => '24', // 24 hours
            'time_name' => 'hours',
            'status' => 1,
            'featured' => 0,
            'capital_back' => 1,
            'lifetime' => 0,
            'repeat_time' => '30',
            'daily_video_limit' => 10,
            'description' => 'Great for regular users. Watch 10 videos daily with better earning rates.',
            'video_earning_rate' => 0.1000,
            'video_access_enabled' => 1
        ]);

        Plan::create([
            'name' => 'Premium Plan',
            'minimum' => 100.00,
            'maximum' => 100.00,
            'fixed_amount' => 100.00,
            'interest' => 0.10,
            'interest_type' => 1, // Percentage
            'time' => '12', // 12 hours
            'time_name' => 'hours',
            'status' => 1,
            'featured' => 1,
            'capital_back' => 1,
            'lifetime' => 0,
            'repeat_time' => '30',
            'daily_video_limit' => 15,
            'description' => 'Premium features with faster returns. Watch 15 videos daily.',
            'video_earning_rate' => 0.1000,
            'video_access_enabled' => 1
        ]);

        Plan::create([
            'name' => 'VIP Plan',
            'minimum' => 200.00,
            'maximum' => 200.00,
            'fixed_amount' => 200.00,
            'interest' => 0.10,
            'interest_type' => 1, // Percentage
            'time' => '6', // 6 hours
            'time_name' => 'hours',
            'status' => 1,
            'featured' => 1,
            'capital_back' => 1,
            'lifetime' => 0,
            'repeat_time' => '30',
            'daily_video_limit' => 25,
            'description' => 'VIP access with highest earning potential. Watch 25 videos daily.',
            'video_earning_rate' => 0.10,
            'video_access_enabled' => 1
        ]);
        $this->command->info('Plans seeded successfully.');
    }

    /**
     * Convert time string to hours
     */
    private function convertTimeToHours($timeString)
    {
        // Handle various time formats
        $timeString = strtolower(trim($timeString));
        
        if (is_numeric($timeString)) {
            return $timeString;
        }
        
        // Extract number and unit
        preg_match('/(\d+)\s*(hour|day|week|month|year)s?/i', $timeString, $matches);
        
        if (count($matches) >= 3) {
            $number = (int)$matches[1];
            $unit = strtolower($matches[2]);
            
            switch ($unit) {
                case 'hour':
                    return $number;
                case 'day':
                    return $number * 24;
                case 'week':
                    return $number * 24 * 7;
                case 'month':
                    return $number * 24 * 30;
                case 'year':
                    return $number * 24 * 365;
                default:
                    return 24; // Default to 24 hours
            }
        }
        
        return 24; // Default to 24 hours if parsing fails
    }
}
