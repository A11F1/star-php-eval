<?php

use App\Models\Article;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

it('returns the expected json structure', function () {
    Article::factory()->for(Category::factory())->create();

    $this->getJson(route('api.articles.index'))
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'summary', 'permalink', 'author', 'published_at', 'category' => ['id', 'name']],
            ],
            'links',
            'meta',
        ]);
});

it('does not expose the article body in the list', function () {
    Article::factory()->for(Category::factory())->create();

    $response = $this->getJson(route('api.articles.index'))->assertOk();

    expect($response->json('data.0'))->not->toHaveKey('body');
});

it('excludes draft articles', function () {
    $category = Category::factory()->create();
    Article::factory()->for($category)->create(['title' => 'Live one']);
    Article::factory()->for($category)->draft()->create(['title' => 'Hidden draft']);

    $this->getJson(route('api.articles.index'))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'Live one');
});

it('orders articles by newest published date first', function () {
    $category = Category::factory()->create();
    Article::factory()->for($category)->create(['title' => 'Oldest', 'published_at' => now()->subDays(3)]);
    Article::factory()->for($category)->create(['title' => 'Newest', 'published_at' => now()->subDay()]);
    Article::factory()->for($category)->create(['title' => 'Middle', 'published_at' => now()->subDays(2)]);

    $this->getJson(route('api.articles.index'))
        ->assertOk()
        ->assertJsonPath('data.0.title', 'Newest')
        ->assertJsonPath('data.1.title', 'Middle')
        ->assertJsonPath('data.2.title', 'Oldest');
});

it('paginates ten articles per page', function () {
    Article::factory()->count(12)->for(Category::factory())->create();

    $this->getJson(route('api.articles.index'))
        ->assertOk()
        ->assertJsonCount(10, 'data')
        ->assertJsonPath('meta.total', 12);

    $this->getJson(route('api.articles.index', ['page' => 2]))
        ->assertOk()
        ->assertJsonCount(2, 'data');
});

it('does not run extra queries as the number of articles grows', function () {
    $countQueries = function (): int {
        DB::flushQueryLog();
        DB::enableQueryLog();
        $this->getJson(route('api.articles.index'))->assertOk();
        $total = count(DB::getQueryLog());
        DB::disableQueryLog();

        return $total;
    };

    Article::factory()
        ->count(3)
        ->sequence(fn () => ['category_id' => Category::factory()->create()->id])
        ->create();
    $small = $countQueries();

    Article::factory()
        ->count(7)
        ->sequence(fn () => ['category_id' => Category::factory()->create()->id])
        ->create();
    $large = $countQueries();

    expect($large)->toBe($small);
});
