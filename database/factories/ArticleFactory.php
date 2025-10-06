<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(8);
        $content = $this->faker->paragraphs(10, true);

        return [
            'title' => $title,
            'slug' => \Illuminate\Support\Str::slug($title),
            'content' => $content,
            'excerpt' => $this->faker->paragraph(2),
            'seo_title' => $title,
            'seo_description' => $this->faker->paragraph(1),
            'published_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'view_count' => $this->faker->numberBetween(0, 10000),
            'author_id' => \App\Models\Author::factory(),
        ];
    }
}
