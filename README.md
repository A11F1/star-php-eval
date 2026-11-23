# Star Media News Portal

A small news portal built with Laravel 13 and SQLite. It has categories and articles, a paginated JSON API, a Blade + Bootstrap article list with live title search, and an article detail page.

## Requirements

- PHP 8.3+ (works on 8.5) with the `pdo_sqlite` extension
- Composer 2.x
- No Node/npm needed: Bootstrap is loaded from a CDN and the search is vanilla JavaScript

## Setup

```sh
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite      # the SQLite database file is created here
php artisan migrate --seed          # seeds 5 categories and 24 articles (18 published, 6 drafts)
php artisan serve
```

Then open http://127.0.0.1:8000.

To start over with fresh sample data at any time, run `php artisan migrate:fresh --seed`.

### Optional: set the site name

The navbar and page titles read `APP_NAME` from `.env` (the default is "Laravel"). To see the portal name in the UI, change it before starting the server:

```dotenv
APP_NAME="Star News"
```

Restart `php artisan serve` after editing `.env` so the new value is picked up.

## Tests and formatting

```sh
php artisan test
./vendor/bin/pint
```

The tests run against an in-memory SQLite database, so they never touch the data in `database/database.sqlite`.

## Routes

| Method | URI                          | Name                 | Description                                              |
|--------|------------------------------|----------------------|----------------------------------------------------------|
| GET    | `/`                          | `articles.index`     | Article list with live title search                      |
| GET    | `/articles/{permalink}`      | `articles.show`      | Article detail page                                      |
| GET    | `/api/articles`              | `api.articles.index` | Paginated JSON, 10 per page, published only, newest first |

### Sample API response

```json
{
  "data": [
    {
      "id": 1,
      "title": "Example headline",
      "summary": "Short summary of the article.",
      "permalink": "example-headline",
      "author": "Jane Doe",
      "published_at": "2026-01-15T09:30:00+00:00",
      "category": { "id": 2, "name": "Technology" }
    }
  ],
  "links": { "first": "...", "last": "...", "prev": null, "next": "..." },
  "meta": { "current_page": 1, "per_page": 10, "total": 18 }
}
```

## Design decisions

- Eager loading with `with('category')` avoids N+1 queries; a query-count test verifies it.
- A `published()` query scope is shared by the API and the web pages.
- Route binding uses the unique `permalink` instead of the numeric id.
- `ArticleResource` and `CategoryResource` control the JSON shape.
- Foreign key from articles to categories with cascade delete, plus an index on `published_at`.
- Extra fields: `author` and `status`.
- Factories provide `draft` and `published` states.
- Search is client-side because the list is small; it needs no extra dependencies.

## With more time

- Server-side search (`?q=`) and a pagination UI
- Caching of list and API responses
- Full-text search for large volumes
- Admin/CRUD with authentication
- API tests for filters

## Original task

Build a news portal with `Category` and `Article` models (a category has many articles), factories and seeders (at least 5 categories and 20 articles), a JSON endpoint that avoids N+1 queries, a Blade/Bootstrap list with real-time title search, and an article detail page. Time limit: 60-90 minutes.
