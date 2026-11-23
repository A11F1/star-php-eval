@extends('layouts.app')

@section('title', $article->title)

@section('content')
    <a href="{{ route('articles.index') }}" class="text-decoration-none">&larr; Back to articles</a>

    <article class="mt-3">
        <span class="badge text-bg-primary mb-2">{{ $article->category->name }}</span>
        <h1>{{ $article->title }}</h1>
        <p class="text-muted">By {{ $article->author }} &middot; {{ $article->published_at->format('d M Y') }}</p>

        <p class="lead">{{ $article->summary }}</p>

        @foreach (preg_split('/\R{2,}/', trim($article->body)) as $paragraph)
            <p>{!! nl2br(e($paragraph)) !!}</p>
        @endforeach
    </article>
@endsection
