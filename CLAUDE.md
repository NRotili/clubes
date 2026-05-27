# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Tech Stack

- **Framework**: Laravel 13.8 with PHP 8.3
- **Database**: SQLite (default, configurable to MySQL)
- **Frontend**: Vite 8.0, Tailwind CSS 4.0, Blade templates
- **Build Tool**: Vite with laravel-vite-plugin
- **Testing**: PHPUnit 12.5
- **Package Managers**: Composer (PHP), npm (Node.js)

## Project Structure

```
clubes/
├── app/                    # Application code
│   ├── Http/Controllers/   # Route controllers
│   ├── Models/             # Eloquent models
│   └── Providers/          # Service providers
├── bootstrap/              # Bootstrap the application
│   └── app.php            # Application configuration/bootstrapping
├── config/                 # Configuration files
├── database/
│   ├── factories/          # Model factories for testing
│   ├── migrations/         # Database migrations
│   └── seeders/            # Database seeders
├── public/                 # Web root (served by web server)
├── resources/
│   ├── css/                # Tailwind CSS entry point (app.css)
│   ├── js/                 # JavaScript entry point (app.js)
│   └── views/              # Blade templates (.blade.php)
├── routes/
│   ├── web.php            # Web routes
│   └── console.php         # Console/artisan commands
├── storage/                # Application storage (logs, cache, compiled views)
├── tests/                  # Test files (Unit and Feature)
├── vendor/                 # Composer dependencies
├── .env                    # Environment configuration (git-ignored)
├── .env.example            # Environment template
├── composer.json           # PHP dependencies
├── package.json            # Node.js dependencies
├── vite.config.js          # Vite configuration
└── phpunit.xml             # PHPUnit configuration
```

## Architecture Overview

This is a modern Laravel application following the default Laravel 13 structure with server-side rendering via Blade templates and client-side assets (CSS/JS) compiled via Vite.

**Key architectural decisions:**
- **Database-first approach**: Uses SQLite by default with migrations managed through Laravel's migration system
- **Asset compilation**: CSS and JS are compiled via Vite during development (`npm run dev`) and production (`npm run build`)
- **Session & Cache**: Both use the database by default (`SESSION_DRIVER=database`, `CACHE_STORE=database`)
- **Queue system**: Uses database queue driver (`QUEUE_CONNECTION=database`)
- **MVC Pattern**: Routes → Controllers → Models → Views
- **Blade Templating**: Server-side templating in `resources/views/` with Tailwind CSS v4 for styling

## Development Commands

### Setup
```bash
composer install              # Install PHP dependencies
npm install                   # Install Node.js dependencies
php artisan key:generate      # Generate APP_KEY (if .env doesn't exist)
php artisan migrate           # Run database migrations
npm run build                 # Build frontend assets for development
```

Or use the unified setup script:
```bash
composer setup                # Runs all setup steps above
```

### Running the Application

**Development mode** (runs Laravel server, queue worker, logs, and Vite dev server concurrently):
```bash
composer run dev
```

This command uses `concurrently` to run:
- Laravel dev server on default port (8000)
- Queue listener with 1 try, no timeout
- Pail logs (real-time log streaming)
- Vite dev server for hot module replacement

**Production build**:
```bash
npm run build                 # Compiles CSS/JS for production
```

### Testing

**Run all tests**:
```bash
composer test
```

This clears the config cache and runs `php artisan test`. Tests are configured in `phpunit.xml` with:
- Unit tests in `tests/Unit/`
- Feature tests in `tests/Feature/`
- SQLite in-memory database for test isolation
- Test environment variables set in `phpunit.xml`

### Code Quality

**Format code** (Laravel Pint):
```bash
./vendor/bin/pint            # Fix code style
./vendor/bin/pint --test     # Check code style without fixing
```

**Clear caches**:
```bash
php artisan config:clear     # Clear config cache
php artisan cache:clear      # Clear application cache
php artisan view:clear       # Clear compiled views
```

## Database

**Default**: SQLite at `database/database.sqlite`

**Switch to MySQL**: Edit `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=clubes
DB_USERNAME=root
DB_PASSWORD=
```

**Migrations**: Located in `database/migrations/`. Run with:
```bash
php artisan migrate           # Run all pending migrations
php artisan migrate:rollback  # Rollback last batch
php artisan migrate:fresh     # Drop all tables and re-run migrations
```

**Current schema**: 
- `users` table with email, password, timestamps
- `password_reset_tokens` table
- `sessions` table (database session storage)
- `cache` / `cache_locks` tables (database cache storage)
- `jobs` / `job_batches` / `failed_jobs` tables (database queue)

## Frontend Assets

**Vite Configuration** (`vite.config.js`):
- Input: `resources/css/app.css`, `resources/js/app.js`
- Output: `public/build/` (manifest.json created)
- Auto-refresh on `.blade.php`, `.js` changes
- Uses `@tailwindcss/vite` plugin (v4 JIT)
- Custom font: Instrument Sans via Bunny CDN

**Blade Template Usage**:
- Reference Vite assets in views with `@vite(['resources/css/app.css', 'resources/js/app.js'])`
- Tailwind CSS v4 uses `@import 'tailwindcss'` in CSS (no separate config needed)
- `@fonts` tag loads Bunny fonts defined in vite.config.js

**Tailwind CSS v4**:
- Configured via `vite.config.js` with `@tailwindcss/vite` plugin
- CSS scans `resources/**/*.blade.php` and `resources/**/*.js` for classes
- Custom font family set via `@theme` in CSS

## Environment Configuration

Key `.env` settings:
- `APP_ENV=local` (production in live)
- `APP_DEBUG=true` (false in production)
- `DB_CONNECTION=sqlite` (configurable)
- `SESSION_DRIVER=database`
- `QUEUE_CONNECTION=database`
- `CACHE_STORE=database`
- `MAIL_MAILER=log` (logs to storage/logs/ for testing)

See `.env.example` for all available options.

## Artisan Commands

Common Laravel artisan commands for this project:

```bash
php artisan serve              # Start development server
php artisan tinker             # Interactive PHP shell with app loaded
php artisan make:model Post    # Generate a new Model
php artisan make:controller PostController  # Generate a controller
php artisan make:migration create_posts_table  # Generate migration
php artisan queue:listen       # Listen for queued jobs
php artisan pail               # Stream logs in real-time
```

## Important Notes

1. **Vite Hot Module Replacement**: When running `composer run dev`, Vite serves CSS/JS with HMR (hot reload). If changes don't appear, check the Vite console or `npm run build` for production.

2. **Database in-memory for tests**: `phpunit.xml` uses `:memory:` SQLite for fast test isolation. Migrations run automatically before tests.

3. **Service Provider**: `app/Providers/AppServiceProvider.php` is minimal (empty). Add service registrations here if needed.

4. **Configuration**: All config values can be overridden via `.env` file. Check `config/` directory for available options.

5. **Blade vs JavaScript**: This is a server-rendered app (Blade templates with server-side logic). JavaScript is used for progressive enhancement, not as a SPA framework.

