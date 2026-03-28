# Starter Kit Laravel

A production-ready Laravel starter kit with **DDD architecture**, **Sanctum authentication**, **React frontend**, and a full **Docker development stack**.

## Stack

| Layer | Technology |
|-------|-----------|
| **Backend** | Laravel 13, PHP 8.3+ |
| **Frontend** | React 19, React Router, TailwindCSS v4, Vite |
| **Auth** | Laravel Sanctum (API tokens + refresh tokens) |
| **Database** | PostgreSQL 18 |
| **Cache/Queue** | Redis |
| **Email** | Mailpit (development) |
| **Docker** | Laravel Sail |
| **Tests** | Pest PHP |
| **Lint** | Laravel Pint (PSR-12 + Laravel preset) |
| **CI/CD** | GitHub Actions (lint + test + build) |

## Quick Start

```bash
# Clone
git clone git@github.com:MichelMLeal/starter-kit.git
cd starter-kit

# Setup
cp .env.example .env
composer install
npm install

# Start Docker services
./vendor/bin/sail up -d

# Run migrations
./vendor/bin/sail artisan migrate

./vendor/bin/sail artisan key:generate

# Dev server (app + queue + logs + vite)
composer dev
```

## Architecture (DDD)

```
app/
├── Domain/                    # Business logic (pure)
│   ├── Auth/
│   │   ├── Models/            # User, RefreshToken
│   │   ├── DTOs/              # LoginDTO, RegisterDTO
│   │   ├── Actions/           # Login, Register, RefreshToken, Logout
│   │   ├── Repositories/      # Interfaces
│   │   └── Exceptions/        # Domain exceptions
│   └── Shared/                # Base contracts, DTOs, exceptions
│
├── Infrastructure/            # Eloquent implementations
│   ├── Auth/Repositories/     # EloquentUserRepository, etc.
│   └── Auth/Providers/        # AuthDomainServiceProvider
│
└── Application/               # HTTP layer
    └── Auth/                  # Controllers, Requests, Resources
```

### Adding a New Domain

1. Create `app/Domain/YourDomain/` (Models, DTOs, Actions, Repositories, Exceptions)
2. Implement in `app/Infrastructure/YourDomain/`
3. Register provider in `bootstrap/providers.php`
4. Add HTTP layer in `app/Application/YourDomain/`

## API Endpoints

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `POST` | `/api/auth/register` | No | Register user |
| `POST` | `/api/auth/login` | No | Login, get tokens |
| `POST` | `/api/auth/refresh` | No | Refresh access token |
| `POST` | `/api/auth/logout` | Yes | Logout, revoke tokens |
| `GET`  | `/api/auth/me` | Yes | Get current user |

### Auth Flow

1. Login → `access_token` + `refresh_token`
2. Use `Authorization: Bearer {access_token}` for authenticated requests
3. On 401 → POST `refresh_token` to `/api/auth/refresh` → new token pair
4. Refresh tokens stored hashed (sha256), expire in 7 days

## Testing

```bash
./vendor/bin/pest                                           # Run all (29 tests)
./vendor/bin/pest --filter=Auth                             # Auth tests only
./vendor/bin/pest tests/Feature/Auth/LoginTest.php          # Single file
./vendor/bin/pest --filter="login with valid credentials"   # Single test by name
```

## Lint

```bash
./vendor/bin/pint --test  # Check
./vendor/bin/pint         # Auto-fix
```

## Copilot Prompts

This project includes reusable prompt skills in `.github/prompts/` for GitHub Copilot Chat. They automate common development workflows following the project's conventions.

### How to use

In Copilot Chat, type `/` and select a prompt from the list, or reference it inline with `#prompt:<name>`:

```
#prompt:branch   Create a user profile edit page, issue #42
#prompt:tests    Generate tests for the new UserProfileAction
#prompt:qa       Run full QA before merging
```

### Available prompts

| Prompt | Description |
|--------|-------------|
| `branch` | Creates a new branch from `main` with naming convention `{type}/{slug}` (e.g., `feature/42-user-profile-edit`) |
| `code-review` | Reviews current changes checking DDD architecture, type safety, security, and frontend patterns |
| `migration-review` | Analyzes migrations for missing indexes, rollback safety, naming, and production lock risks |
| `tests` | Generates Pest tests (Feature + Unit) following project patterns with happy path, validation, and auth scenarios |
| `performance` | Identifies N+1 queries, missing indexes, unnecessary re-renders, bundle size, and memory issues |
| `qa` | Runs lint + tests + build + manual checklist. Reports a pass/fail summary before review |
| `push` | Auto-fixes lint, runs tests, creates Conventional Commits, pushes, and generates a PR description |

### Typical workflow

```
#prompt:branch    → Create feature branch
                  → Implement changes
#prompt:tests     → Generate tests for new code
#prompt:code-review → Review changes before commit
#prompt:qa        → Full quality check
#prompt:push      → Commit, push, and get PR description
```

## CI/CD

GitHub Actions on push/PR to `main`: **Lint** → **Test** → **Build** → **Deploy**

### Automatic Deploy

After CI passes on `main`, the deploy workflow runs automatically using **Laravel Envoy**:

1. Clones the repo on the server
2. Links shared `.env`, `storage/`, `node_modules/`
3. Installs PHP & Node dependencies
4. Builds frontend assets
5. Runs migrations
6. Caches config/routes/views
7. Swaps symlink (zero-downtime)
8. Reloads Octane/Horizon/Reverb if running
9. Cleans old releases (keeps last 5)

### GitHub Secrets Required

| Secret | Description | Example |
|--------|-------------|---------|
| `DEPLOY_SSH_KEY` | Private SSH key for the server | `-----BEGIN OPENSSH PRIVATE KEY-----...` |
| `DEPLOY_HOST` | Server hostname or IP | `192.168.1.100` |
| `DEPLOY_USER` | SSH user | `deploy` |
| `DEPLOY_PATH` | Application path on server | `/var/www/starter-kit` |

### Manual Deploy

```bash
# Deploy
envoy run deploy --DEPLOY_USER=deploy --DEPLOY_HOST=your-server.com --DEPLOY_PATH=/var/www/starter-kit

# Rollback to previous release
envoy run rollback --DEPLOY_USER=deploy --DEPLOY_HOST=your-server.com --DEPLOY_PATH=/var/www/starter-kit
```

### Server Setup (first time)

```bash
# On the server, create the directory structure:
mkdir -p /var/www/starter-kit/{releases,shared/storage}

# Copy your .env to the shared directory:
cp .env /var/www/starter-kit/shared/.env

# Ensure storage structure:
mkdir -p /var/www/starter-kit/shared/storage/{app/public,framework/{cache,sessions,testing,views},logs}
```

## Installed Packages

All packages are installed and configured. Migrations will run on `php artisan migrate`.

### Telescope — Debug Dashboard (dev-only)

Debug assistant for requests, queries, jobs, exceptions, mail, and more.

- **URL**: `/telescope` (local environment only)
- **Config**: `config/telescope.php`
- **Registered**: conditionally in `AppServiceProvider` (only when `APP_ENV=local`)

```bash
php artisan telescope:publish   # Publish latest assets after updating
```

### Horizon — Queue Dashboard

Real-time dashboard and code-driven configuration for Redis queues.

- **URL**: `/horizon`
- **Config**: `config/horizon.php`
- **Run**: `php artisan horizon`

```bash
php artisan horizon              # Start Horizon supervisor
php artisan horizon:pause        # Pause processing
php artisan horizon:continue     # Resume processing
php artisan horizon:terminate    # Graceful shutdown
```

### Pulse — Performance Monitoring

Real-time application performance monitoring (slow queries, requests, exceptions, queue jobs).

- **URL**: `/pulse`
- **Config**: `config/pulse.php`
- **Migrations**: published in `database/migrations/`

### Octane — High-Performance Server

Serves the application using FrankenPHP for dramatically faster response times.

- **Config**: `config/octane.php`
- **Server**: FrankenPHP (binary at `frankenphp`)

```bash
php artisan octane:start                    # Start with FrankenPHP
php artisan octane:start --workers=4        # Custom worker count
php artisan octane:start --watch            # Auto-reload on file changes (dev)
php artisan octane:reload                   # Graceful reload
```

### Passport — OAuth2 Server

Full OAuth2 server implementation. Coexists with Sanctum — use Sanctum for API tokens, Passport for OAuth2 with third-party apps.

- **Config**: `config/passport.php`
- **Guard**: `api-oauth` (configured in `config/auth.php`)
- **Migrations**: published in `database/migrations/`
- **Encryption keys**: generated at `storage/oauth-private.key` and `storage/oauth-public.key`

```bash
php artisan passport:keys                   # Regenerate encryption keys
php artisan passport:client                 # Create a new OAuth client
php artisan passport:client --personal      # Create personal access client
```

> **Note**: Sanctum remains the default `auth:sanctum` guard for API routes. Use `auth:api-oauth` middleware for Passport-protected routes.

### Cashier — Stripe Billing

Subscription billing, one-time charges, invoices, and customer portal powered by Stripe.

- **Config**: managed via `.env` (STRIPE_KEY, STRIPE_SECRET, etc.)
- **Migrations**: published in `database/migrations/`
- **User model**: `Billable` trait already added

```env
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
```

```bash
php artisan cashier:webhook    # Create Stripe webhook endpoint
```

### Socialite — Social Authentication

OAuth login with Google, GitHub, and Facebook. Providers configured in `config/services.php`.

- **Config**: `config/services.php` (github, google, facebook entries)
- **No routes created** — implement your own controllers using:

```php
// Redirect to provider
return Socialite::driver('github')->redirect();

// Handle callback
$user = Socialite::driver('github')->user();
```

```env
GITHUB_CLIENT_ID=
GITHUB_CLIENT_SECRET=
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
FACEBOOK_CLIENT_ID=
FACEBOOK_CLIENT_SECRET=
```

### Reverb — WebSocket Server

Laravel's first-party WebSocket server for real-time broadcasting.

- **Config**: `config/reverb.php`, `config/broadcasting.php`
- **Run**: `php artisan reverb:start`

```bash
php artisan reverb:start                    # Start WebSocket server
php artisan reverb:start --debug            # Start with debug output
```

```env
REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST=localhost
REVERB_PORT=8080
```

Frontend broadcasting is pre-configured with Vite env variables (`VITE_REVERB_*`).

### MCP Server ⚠️ (Pending)

Model Context Protocol server to expose the app to AI assistants. The `php-mcp/laravel` package does **not yet support Laravel 13**. Install when available:

```bash
composer require php-mcp/laravel   # When Laravel 13 support is released
```

---

### Package Summary

| Package | Version | Status | Dashboard |
|---------|---------|--------|-----------|
| Telescope | ^5 | ✅ Installed (dev) | `/telescope` |
| Horizon | ^5 | ✅ Installed | `/horizon` |
| Pulse | ^1 | ✅ Installed | `/pulse` |
| Octane | ^2 | ✅ Installed (FrankenPHP) | — |
| Passport | ^13 | ✅ Installed | — |
| Cashier | ^16 | ✅ Installed (Stripe) | — |
| Socialite | ^5 | ✅ Installed | — |
| Reverb | ^1 | ✅ Installed | — |
| Envoy | ^2 | ✅ Installed (dev) | — |
| MCP Server | — | ⏳ Pending (Laravel 13 compat) | — |

## License

MIT
