# Candidate Technical Evaluation - Star Media Group

## Time Limit: 60 - 90 Minutes

### Prerequisites
- PHP 8.5+ installed
- Composer 2.x installed

### Quick Setup Instructions
1. Clone this repository to your local machine.
2. Run `composer install`
3. Copy the environment file: `cp .env.example .env`
4. Generate app key: `php artisan key:generate`
5. Create SQLite file if needed (see database/ folder) and run migrations: `php artisan migrate`
6. Start dev server: `php artisan serve`

### Tasks
#### 1. **Migrations & Models:** Create the Article and Category models with standard Eloquent relationships (a Category has many Articles).
- `Category` Model Fields:
  - `id` (primary key)
  - `name` (string)
  - `Article` Model Fields:
    - `title` (string)
    - `summary` (string)
    - `body` (text)
    - `published_at` (timestamp, nullable)
    - `permalink` (string)
    - `category_id` (foreign key referencing `categories`)
    - _(Optional)_: Add 1 or 2 extra properties you feel are essential for a news portal article (e.g., `status`, `thumbnail_url`, `author`).
#### 2. **Factories & Database Seeding**
- Create an `ArticleFactory` using Faker for titles, summary, and body content.
- Configure `DatabaseSeeder.php` so running `php artisan db:seed` automatically populates the database with at least 5 categories and 20 articles.
#### 3. **Controller & API Endpoint:** Create a GET controller method returning articles formatted as JSON. Prevent N+1 database queries using eager loading (`with`).
#### 4. **Frontend Integration:**
- **Article List View:** Render a list of articles using standard Blade views styled with Bootstrap (or Livewire).
- **Search Filter:** Implement a simple real-time JavaScript (or Livewire) search filter to filter articles by title on the list page.
- **Article Detail Page:** Create a single article view page that displays the full title, body, category, and published date when an article from the list is clicked.

### Submission Guidelines
- Commit code cleanly with clear commit messages.
- Push your completed work back to your repository and share the repository link with us.

---

# Candidate Solution: Star Media News Portal

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
- The article list page is intentionally not paginated so the client-side search covers every article; only the JSON API is paginated (10 per page).

## With more time

- Server-side search (`?q=`) and a pagination UI
- Caching of list and API responses
- Full-text search for large volumes
- Admin/CRUD with authentication
- API tests for filters
