<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(): View
    {
        // Eager load the category to avoid an N+1 query per article card.
        $articles = Article::query()
            ->with('category')
            ->published()
            ->latest('published_at')
            ->get();

        return view('articles.index', compact('articles'));
    }

    public function show(string $permalink): View
    {
        $article = Article::query()
            ->with('category')
            ->published()
            ->where('permalink', $permalink)
            ->firstOrFail();

        return view('articles.show', compact('article'));
    }
}
