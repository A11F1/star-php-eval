<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ArticleController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        // Eager load the category to prevent an N+1 query per article.
        $articles = Article::query()
            ->with('category')
            ->published()
            ->latest('published_at')
            ->latest('id') // stable order when timestamps tie, so pages never overlap
            ->paginate(10);

        return ArticleResource::collection($articles);
    }
}
