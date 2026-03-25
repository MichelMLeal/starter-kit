# Starter Kit Laravel

A production-ready Laravel starter kit with **DDD architecture**, **Sanctum authentication**, **React frontend**, and a full **Docker development stack**.

## Stack

| Layer | Technology |
|-------|-----------|
| **Backend** | Laravel 12, PHP 8.3+ |
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
./vendor/bin/pest               # Run all (19 tests)
./vendor/bin/pest --filter=Auth # Auth tests only
```

## Lint

```bash
./vendor/bin/pint --test  # Check
./vendor/bin/pint         # Auto-fix
```

## CI/CD

GitHub Actions on push/PR to `main`: **Lint** → **Test** → **Build**

## License

MIT
