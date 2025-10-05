<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Source>
 */
class SourceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'url' => fake()->url(),
            'type' => fake()->randomElement(['news', 'review', 'streaming']),
            'language' => fake()->randomElement(['cs', 'en', 'sk']),
            'active' => fake()->boolean(80), // 80% chance of being active
            'last_checked_at' => fake()->optional(0.7)->dateTimeBetween('-1 week', 'now'),
        ];
    }
}
