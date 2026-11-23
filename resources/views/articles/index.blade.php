@extends('layouts.app')

@section('title', 'Articles')

@section('content')
    <h1 class="mb-4">Articles</h1>

    @if ($articles->isEmpty())
        <p class="text-muted">No articles have been published yet.</p>
    @else
        <div class="mb-4">
            <label for="article-search" class="form-label visually-hidden">Search articles by title</label>
            <input type="search" id="article-search" class="form-control" placeholder="Search articles by title" autocomplete="off">
        </div>

        <div id="article-list" class="row row-cols-1 g-4">
            @foreach ($articles as $article)
                <div class="col article-item" data-title="{{ $article->title }}">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <span class="badge text-bg-primary mb-2">{{ $article->category->name }}</span>
                            <h2 class="h5 card-title">
                                <a href="{{ route('articles.show', $article->permalink) }}" class="text-decoration-none">{{ $article->title }}</a>
                            </h2>
                            <p class="card-text">{{ $article->summary }}</p>
                        </div>
                        <div class="card-footer text-muted small">
                            By {{ $article->author }} &middot; {{ $article->published_at->format('d M Y') }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <p id="no-results" class="d-none text-muted mt-4">No articles match your search.</p>
    @endif
@endsection

@push('scripts')
    <script src="{{ asset('js/article-search.js') }}" defer></script>
@endpush
