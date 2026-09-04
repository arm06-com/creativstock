<?php

namespace Database\Factories;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SubscriptionPlan>
 */
class SubscriptionPlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'price' => fake()->randomFloat(2, 5, 100),
            'download_limit' => fake()->numberBetween(10, 1000),
            'user_limit' => fake()->numberBetween(1, 10),
            'features' => [fake()->sentence(), fake()->sentence()],
            'price_id' => 'pri_'.fake()->unique()->bothify('##########'),
            'is_popular' => false,
        ];
    }
}
