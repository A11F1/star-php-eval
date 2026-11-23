<?php

use App\Models\Article;
use App\Models\Category;

it('lists published articles but not drafts', function () {
    $category = Category::factory()->create();
    Article::factory()->for($category)->create(['title' => 'Visible article']);
    Article::factory()->for($category)->draft()->create(['title' => 'Secret draft']);

    $this->get(route('articles.index'))
        ->assertOk()
        ->assertSee('Visible article')
        ->assertDontSee('Secret draft');
});

it('renders the search input and no-results element', function () {
    $this->get(route('articles.index'))
        ->assertOk()
        ->assertSee('id="article-search"', false)
        ->assertSee('id="no-results"', false);
});

it('shows an empty state when there are no articles', function () {
    $this->get(route('articles.index'))
        ->assertOk()
        ->assertDontSee('class="article-item', false)
        ->assertSee('No articles');
});

it('links list items to the detail page', function () {
    $article = Article::factory()->for(Category::factory())->create();

    $this->get(route('articles.index'))
        ->assertOk()
        ->assertSee(route('articles.show', $article->permalink), false);
});

it('shows a published article', function () {
    $category = Category::factory()->create(['name' => 'Science']);
    $article = Article::factory()->for($category)->create([
        'title' => 'Black holes explained',
        'body' => 'Plenty of gravity here.',
        'author' => 'Ada Lovelace',
    ]);

    $this->get(route('articles.show', $article->permalink))
        ->assertOk()
        ->assertSee('Black holes explained')
        ->assertSee('Plenty of gravity here.')
        ->assertSee('Science')
        ->assertSee('Ada Lovelace');
});

it('returns 404 for a draft article', function () {
    $article = Article::factory()->for(Category::factory())->draft()->create();

    $this->get(route('articles.show', $article->permalink))->assertNotFound();
});

it('returns 404 for an unknown permalink', function () {
    $this->get('/articles/does-not-exist')->assertNotFound();
});
