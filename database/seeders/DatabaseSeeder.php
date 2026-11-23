<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $categories = Category::factory()
            ->count(5)
            ->sequence(...collect(['Politics', 'Business', 'Sports', 'Technology', 'Entertainment'])
                ->map(fn (string $name) => ['name' => $name])
                ->all())
            ->create();

        foreach (range(1, 24) as $i) {
            $article = Article::factory()->for($categories[$i % $categories->count()]);

            // Every fourth article stays a draft (6 of 24).
            ($i % 4 === 0 ? $article->draft() : $article->published())->create();
        }
    }
}
