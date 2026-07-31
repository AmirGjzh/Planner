<?php

namespace Database\Factories;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Plan>
 */
class PlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(4, true),
            'description' => fake()->text(),
            'start_date' => fake()->dateTimeBetween('-1 month', '+1 month')->format('Y-m-d'),
            'finish_date' => fn (array $attrs) => fake()->dateTimeBetween($attrs['start_date'] ?? 'now', '+3 months')->format('Y-m-d'),
            'done' => fake()->boolean(),
            'user_id' => User::factory(),
        ];
    }
}
