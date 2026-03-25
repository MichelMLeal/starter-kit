# Copilot Instructions

## Build, Test & Lint

```bash
# Development (runs PHP server, queue worker, log tail, and Vite concurrently)
composer dev

# Build frontend
npm run build

# Run all tests
./vendor/bin/pest

# Run a single test file
./vendor/bin/pest tests/Feature/Auth/LoginTest.php

# Run tests matching a name
./vendor/bin/pest --filter="login with valid credentials"

# Lint PHP (check only)
./vendor/bin/pint --test

# Lint PHP (auto-fix)
./vendor/bin/pint
```

Tests use SQLite in-memory — no database setup needed.

## Architecture

This is a Laravel 13 + React 19 SPA starter kit using **Domain-Driven Design (DDD)** with three layers:

- **Domain** (`app/Domain/`) — Pure business logic: Actions, Models, DTOs, Repository interfaces, Exceptions. No framework dependencies.
- **Infrastructure** (`app/Infrastructure/`) — Eloquent implementations of domain repository interfaces. Service providers bind interfaces to implementations.
- **Application** (`app/Application/`) — HTTP layer: Controllers, Form Requests, API Resources. Controllers are thin — they validate input, build a DTO, call a domain Action, and return a Resource.

Each domain is a self-contained module (e.g., `Auth`). To add a new domain, mirror this structure under all three layers and register bindings in a new service provider added to `bootstrap/providers.php`.

### Auth Flow

API-first authentication using Laravel Sanctum with access + refresh tokens:

1. Login/Register → returns `access_token` + `refresh_token`
2. Requests use `Authorization: Bearer {access_token}`
3. On 401 → frontend automatically attempts token refresh via Axios interceptor
4. Refresh tokens are stored hashed (sha256) with 7-day expiry

### Frontend

React 19 SPA served via a Blade catch-all route (`/{any?}`). Key structure:

- `resources/js/contexts/AuthContext.jsx` — Global auth state (React Context API), tokens in localStorage
- `resources/js/services/api.js` — Axios client with auth header injection and 401 refresh interceptor
- `resources/js/layouts/` — `GuestLayout` (redirects authed users) and `AuthLayout` (navbar + protected routes)
- `resources/js/pages/` — Route-level page components

Routing uses React Router v7. Tailwind CSS v4 for styling.

## Conventions

### PHP

- `declare(strict_types=1)` on all files
- Full type hints on all method parameters and return types
- `readonly` properties on DTOs; `final` on Actions
- Controllers are invokable (single `__invoke` method)
- Naming: `{Verb}Action`, `{Verb}Controller`, `{Verb}DTO`, `{Model}RepositoryInterface`, `Eloquent{Model}Repository`

### Code Style (Pint)

Laravel preset with: alphabetically sorted imports, no unused imports, trailing commas in multiline arrays/arguments/parameters.

### Testing (Pest)

- **Feature tests** — Full HTTP request cycle with `postJson()`/`getJson()`, use `RefreshDatabase` trait, factory-based data setup
- **Unit tests** — Domain actions in isolation
- Mirror the domain structure: `tests/Feature/Auth/`, `tests/Unit/Auth/`

### API Routes

All API routes live in `routes/api.php` under `/api/auth/`. Public routes have `throttle` middleware for brute-force protection. Protected routes use `auth:sanctum`.

### Docker (compose.yaml)

PostgreSQL 18, Redis, Mailpit. PHP app exposed on port 80, Vite on 5173.

## Installed Packages

### Telescope (dev-only)
Debug dashboard at `/telescope`. Registered only when `APP_ENV=local` (see `AppServiceProvider`). Config: `config/telescope.php`.

### Horizon
Redis queue dashboard at `/horizon`. Run with `php artisan horizon`. Config: `config/horizon.php`.

### Pulse
Performance monitoring at `/pulse`. Tracks slow queries, requests, exceptions. Config: `config/pulse.php`.

### Octane (FrankenPHP)
High-performance server. Run with `php artisan octane:start`. Config: `config/octane.php`.

### Passport (OAuth2)
Coexists with Sanctum. Use guard `api-oauth` for Passport-protected routes, `auth:sanctum` for API tokens. Config: `config/passport.php`.

### Cashier (Stripe)
Billing/subscriptions. `Billable` trait on User model. Set `STRIPE_KEY`/`STRIPE_SECRET` in `.env`.

### Socialite
Social login with Google, GitHub, Facebook configured in `config/services.php`. No routes pre-created.

### Reverb
WebSocket server. Run with `php artisan reverb:start`. Broadcasting configured for Reverb. Config: `config/reverb.php`.

### MCP Server
⚠️ Not yet installed — `php-mcp/laravel` does not support Laravel 13. Install when available.
