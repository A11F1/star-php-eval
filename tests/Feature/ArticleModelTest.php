<?php

use App\Models\Article;
use App\Models\Category;

function makeArticle(Category $category, array $attributes = []): Article
{
    static $count = 0;
    $count++;

    return Article::create(array_merge([
        'category_id' => $category->id,
        'title' => "Article {$count}",
        'summary' => 'Summary',
        'body' => 'Body',
        'permalink' => "article-{$count}",
        'author' => 'Jane Doe',
        'status' => Article::STATUS_PUBLISHED,
        'published_at' => now()->subDay(),
    ], $attributes));
}

it('has many articles in a category', function () {
    $category = Category::create(['name' => 'Tech']);
    makeArticle($category);
    makeArticle($category);

    expect($category->articles)->toHaveCount(2);
});

it('belongs to a category', function () {
    $category = Category::create(['name' => 'Tech']);
    $article = makeArticle($category);

    expect($article->category->is($category))->toBeTrue();
});

it('only includes published, already released articles in the published scope', function () {
    $category = Category::create(['name' => 'Tech']);
    $live = makeArticle($category);
    makeArticle($category, ['status' => Article::STATUS_DRAFT, 'published_at' => null]);
    makeArticle($category, ['status' => Article::STATUS_DRAFT, 'published_at' => now()->subDay()]);
    makeArticle($category, ['published_at' => now()->addDay()]);
    makeArticle($category, ['published_at' => null]);

    $ids = Article::published()->pluck('id');

    expect($ids)->toHaveCount(1)
        ->and($ids->first())->toBe($live->id);
});

it('uses the permalink as route key', function () {
    expect((new Article)->getRouteKeyName())->toBe('permalink');
});
