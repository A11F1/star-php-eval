<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Article>
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
        $title = rtrim(fake()->sentence(), '.');

        return [
            'category_id' => Category::factory(),
            'title' => $title,
            'summary' => fake()->sentences(fake()->numberBetween(1, 2), true),
            'body' => implode("\n\n", fake()->paragraphs(4)),
            'permalink' => Str::slug($title).'-'.Str::lower(Str::random(6)),
            'author' => fake()->name(),
            'status' => Article::STATUS_PUBLISHED,
            'published_at' => fake()->dateTimeBetween('-60 days', 'now'),
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'status' => Article::STATUS_PUBLISHED,
            'published_at' => fake()->dateTimeBetween('-60 days', 'now'),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'status' => Article::STATUS_DRAFT,
            'published_at' => null,
        ]);
    }
}
