<?php

namespace Database\Factories;

use App\Enums\TaskPriority;
use App\Models\Category;
use App\Models\Plan;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->words(2, true),
            'description' => fake()->text(),
            'task_date' => fake()->dateTimeBetween('today', '+1 month')->format('Y-m-d'),
            'estimated_minutes' => fake()->numberBetween(1, 1200),
            'priority' => fake()->randomElement(TaskPriority::cases()),
            'done' => fake()->boolean(),
            'day_before_alarm' => fake()->numberBetween(0, 30),
            'user_id' => User::factory(),
            'plan_id' => Plan::factory(),
            'category_id' => Category::factory(),
        ];
    }
}
