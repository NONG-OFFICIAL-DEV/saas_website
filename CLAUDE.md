# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Repository layout

This is a monorepo with two independently deployed apps, wired together only at the network/API level (no shared package, no shared types):

- `frontend/` — Vue 3 + Vuetify 3 + Vite marketing/SaaS site ("Nexstack"). Has its own detailed **`frontend/CLAUDE.md`** — read it before working in this directory; it documents the two-backend architecture, CMS data model, routing, and Vuetify theme conventions in depth. Don't duplicate that content here.
- `backend/` — Laravel 13 (PHP 8.3) API. This is the **CMS backend** referenced from `frontend/CLAUDE.md` ("Two backends" section): it serves marketing content (products, solutions, testimonials, blog posts, site content blocks) and the admin panel's auth, and proxies onboarding provisioning to the real product backends (Studio, Smart Store). It is a separate system from the actual POS/Studio product backends.

`docker-compose.yml` at the root builds three containers for production: `backend_service` (Laravel/PHP-FPM), `webserver` (nginx in front of it, `backend/nginx`), and `frontend` (the built Vue site). Deploys go through `.github/workflows/ci-cd.yml`.

## Commands

### Backend (`backend/`, Laravel)

```bash
composer install
cp .env.example .env && php artisan key:generate   # first-time setup
php artisan migrate

composer dev     # runs php artisan serve + queue:listen + pail (logs) + vite, concurrently
composer test    # clears config cache, then php artisan test (PHPUnit)
```

Run a single test: `php artisan test --filter=TestName` or `php artisan test tests/Feature/SomeTest.php`. Only `tests/Feature/ExampleTest.php` and `tests/Unit/ExampleTest.php` exist today (default Laravel scaffolding) — there is no real test coverage of CMS features yet.

Code style: `laravel/pint` is a dev dependency (`vendor/bin/pint` to fix, `vendor/bin/pint --test` to check) but there's no composer script wired up for it.

### Frontend (`frontend/`, Vue 3 + Vite)

```bash
npm run dev       # start Vite dev server
npm run build     # production build to dist/
npm run preview   # preview a production build locally
```

See `frontend/CLAUDE.md` for environment variables and everything else.

## Architecture notes specific to the backend

`routes/api.php` splits cleanly into three groups, matching the `Api/Public`, `Api/Admin`, and top-level `Api` controller namespaces:

- **`v1/public/*`** (no auth) — read-only endpoints consumed by the marketing frontend: `products`, `solutions`, `testimonials`, `blog-posts`, `site-content/{key}`, the onboarding proxy (`onboarding/business-types`, `onboarding/provision`, throttled 6/min), the onboarding activity log write (same `onboarding/provision` call also writes to `onboarding_submissions`, surfaced at `/admin/onboarding` in the frontend), and the documentation system (`documentation-categories` — full published tree in one call, `documentation-articles/{slug}` — article + prev/next/related, `documentation-search?q=`).
- **`v1/auth/login`** — issues a Sanctum token for the admin panel.
- **`v1/admin/*`** (`auth:sanctum` middleware) — full CRUD for the same content types, plus nested resources (`products/{product}/features|screenshots|faqs`), site-content editing, `media` upload/list/delete, `onboarding-submissions` (index/destroy — visibility log only), and `documentation-categories` / `documentation-articles` (full CRUD, draft/published/archived for articles). The public API only ever returns `is_published = true` (or `status = published`) rows; the admin API returns everything including drafts and all locales' raw translation rows.

Translatable content follows a consistent base-table + `_translations`-table pattern throughout (see `frontend/CLAUDE.md`'s "CMS data model" section for the full rationale) — when adding a new CMS-editable entity, match this shape rather than inventing a new one (e.g. jsonb-only translations).
