<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Plan;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plan>
 */
class PlanFactory extends Factory
{
    protected $model = Plan::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'minimum'=> $this->faker->numberBetween(1, 10),
            'maximum' => $this->faker->numberBetween(11, 100),
            'fixed_amount' => $this->faker->numberBetween(100, 1000),
            'interest' => $this->faker->randomFloat(2, 1, 20), // percentage
            'interest_type' => 0,
            'time'=> $this->faker->numberBetween(1, 365), // in days
            'time_name' => $this->faker->randomElement(['days', 'weeks', 'months']),
            'status' => 0,
            'featured' => $this->faker->boolean(50), // 50% chance of being featured
            'capital_back' => $this->faker->boolean(50), // 50% chance of capital back
            'lifetime' => $this->faker->boolean(50), // 50% chance of lifetime
            'repeat_time' => $this->faker->numberBetween(1, 12), // in months
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
