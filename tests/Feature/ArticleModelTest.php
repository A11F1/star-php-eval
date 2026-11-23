<?php

use App\Models\Article;
use App\Models\Category;

it('has many articles in a category', function () {
    $category = Category::factory()->has(Article::factory()->count(2))->create();

    expect($category->articles)->toHaveCount(2);
});

it('belongs to a category', function () {
    $category = Category::factory()->create();
    $article = Article::factory()->for($category)->create();

    expect($article->category->is($category))->toBeTrue();
});

it('only includes published, already released articles in the published scope', function () {
    $category = Category::factory()->create();
    $live = Article::factory()->for($category)->create();

    Article::factory()->for($category)->draft()->create();
    Article::factory()->for($category)->draft()->create(['published_at' => now()->subDay()]);
    Article::factory()->for($category)->create(['published_at' => now()->addDay()]);
    Article::factory()->for($category)->create(['published_at' => null]);

    $ids = Article::published()->pluck('id');

    expect($ids)->toHaveCount(1)
        ->and($ids->first())->toBe($live->id);
});
