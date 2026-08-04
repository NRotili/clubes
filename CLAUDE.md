# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Tech Stack

- **Framework**: Laravel 13.8 with PHP 8.3
- **Database**: SQLite (default, configurable to MySQL)
- **Frontend**: Vite 8.0, Tailwind CSS 4.0, Blade templates
- **Build Tool**: Vite with laravel-vite-plugin
- **Testing**: PHPUnit 12.5
- **Package Managers**: Composer (PHP), npm (Node.js)

## Domain Overview

This is a management system for an Argentine sports/social club ("club de barrio"). Core entities:

- **Socios** (`Socio`): club members. Have a `numero_socio`, QR-verifiable status, monthly dues (`CuotaMensual`), payments (`Pago`/`PagoItem`), and can be suspended for debt.
- **Disciplinas** (`Disciplina`): sport/activity classes with schedules (`DisciplinaHorario`), inscription tracking (`DisciplinaInscripcionLog`), and per-discipline attendance (`AsistenciaDisciplina`).
- **Actividades / Turnos** (`Actividad`, `ActividadFranja`, `ActividadTurno`): bookable facilities (courts, rooms) with availability slots and a reservation/approval workflow (`ActividadDisponibilidadService`, `ActividadReservaService`).
- **Profesores** (`Profesor`): teachers linked to disciplinas; a `profesor`-role user can view "Mis clases" and take attendance.
- **Finanzas**: `Ingreso`/`Egreso` (general income/expense) plus cuotas/pagos for membership dues, with debtor tracking (`DeudoresController`).
- **Comunicación**: `Comunicacion` (targeted messages) and `Noticia` (news board), both pushed via `PushNotificationService`.
- **ClubConfig**: key/value store for club branding (name, logo, contact) and cuota settings, shared to all views via `AppServiceProvider` as `$club`.

**Roles** (`users.rol` enum): `desarrollador` (super-admin/dev-only features), `administracion`, `profesor`, `socio`. Route access is guarded by a custom `rol:` middleware (`App\Http\Middleware\CheckRol`), e.g. `Route::middleware('rol:administracion,desarrollador')`. Helper methods on `User` (`esDesarrollador()`, `esAdministracion()`, `esProfesor()`, `esSocio()`, `puedeGestionarSocios()`) are used both in middleware and Blade `@if` checks (sidebar menu, feature gating). `desarrollador`-only areas include user management (`usuarios.*`) and the Artisan command console (`artisan.*`).

**Mobile app**: a companion Expo/React Native app (separate repo) consumes `routes/api.php` via Sanctum token auth (`/api/login`, `/api/register`, then bearer-token endpoints for `me`, `cuotas`, `noticias`, `disciplinas`, `calendario`, `actividades`/`turnos`, push token registration, etc). When changing API responses, keep the mobile client's expectations in mind.

**Admin Artisan console** (`ArtisanController`, desarrollador-only, `/artisan`): runs a fixed whitelist of safe commands from the web UI (cache/config/route/view clears, `migrate --force`, `queue:restart`, and the custom `socios:notificar-vencimientos` / `socios:suspender-deudores` commands). It does **not** accept arbitrary command strings — new commands must be added to the whitelist in the controller.

**Scheduled commands** (`app/Console/Commands/`, wired in `routes/console.php`):
- `NotificarVencimientosCommand` (`socios:notificar-vencimientos`) — push notifications for cuotas due soon/today/overdue.
- `SuspenderDeudoresCommand` (`socios:suspender-deudores {--dry-run}`) — suspends members with unpaid debt.

## Project Structure

```
clubes/
├── app/                    # Application code
│   ├── Http/Controllers/   # Web route controllers
│   │   └── Api/            # JSON API controllers (mobile app)
│   ├── Http/Middleware/    # CheckRol and other middleware
│   ├── Models/             # Eloquent models
│   ├── Services/           # Domain services (disponibilidad/reserva de turnos, push notifications)
│   ├── Console/Commands/   # Scheduled artisan commands
│   └── Providers/          # Service providers (shares $club config to all views)
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
│   └── views/              # Blade templates (.blade.php), organized by feature (socios, actividades, artisan, manual, configuracion, etc.)
├── routes/
│   ├── web.php            # Web routes (session auth, role-gated groups)
│   ├── api.php             # JSON API routes for the mobile app (Sanctum)
│   └── console.php         # Console/artisan commands + schedule
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

This is a Laravel 13 application with server-side rendering via Blade templates for the web admin/socio panel, plus a JSON API (Sanctum) consumed by a separate mobile app.

**Key architectural decisions:**
- **Database-first approach**: Uses SQLite by default with migrations managed through Laravel's migration system
- **Asset compilation**: CSS and JS are compiled via Vite during development (`npm run dev`) and production (`npm run build`)
- **Session & Cache**: Both use the database by default (`SESSION_DRIVER=database`, `CACHE_STORE=database`)
- **Queue system**: Uses database queue driver (`QUEUE_CONNECTION=database`)
- **MVC Pattern**: Routes → Controllers → Models → Views
- **Blade Templating**: Server-side templating in `resources/views/` with Tailwind CSS v4 for styling
- **Sidebar menu**: `resources/views/layouts/app.blade.php` has separate desktop (`<aside>`) and mobile (collapsible) navs — both must be updated when adding a menu item, gated with the same `esDesarrollador()`/`puedeGestionarSocios()` checks used in routes

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

**Current schema**: beyond Laravel's defaults (`users`, `password_reset_tokens`, `sessions`, `cache`/`cache_locks`, `jobs`/`job_batches`/`failed_jobs`), the schema covers the domain entities described in **Domain Overview** above (socios, cuotas/pagos, disciplinas, actividades/turnos, profesores, comunicaciones/noticias, club config). Check `database/migrations/` for the authoritative, up-to-date table list — don't rely on this file for schema details.

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

3. **Service Provider**: `app/Providers/AppServiceProvider.php` shares `ClubConfig::todos()` to all views as `$club` and registers API rate limiters. Its try/catch fallback (used when the config table isn't ready) must keep the same array shape as `ClubConfig::todos()` (all keys present, including `logo_url`), or `layouts/app.blade.php` throws on `$club['logo_url']`.

4. **Configuration**: All config values can be overridden via `.env` file. Check `config/` directory for available options.

5. **Blade vs JavaScript**: This is a server-rendered app (Blade templates with server-side logic). JavaScript is used for progressive enhancement, not as a SPA framework.

6. **Running the full test suite**: `layouts/app.blade.php` declares a plain PHP function (`mobileSection`) inline via `@php`. Running many feature tests that render the layout in the same PHPUnit process can crash with "Cannot redeclare mobileSection()". If `php artisan test` (full suite) crashes with that error, run affected test files individually instead — it's a pre-existing quirk, not a sign your change broke something.

