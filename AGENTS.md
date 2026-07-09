# AGENTS.md — ERP Kuamanga

## NOT full Laravel

Uses standalone `illuminate/*` components + custom `App\Core\Application` bootstrap.
No `artisan`, no full Laravel HTTP Kernel. Entry: `public/index.php` → `bootstrap/app.php`.

## Console

```sh
php console migrate                    # Phinx migrations (supports --seed, --target, --date, --dry-run, --fake)
php console migrate:rollback           # rollback last migration
php console migrate:status             # show migration status
php console migrate:breakpoint         # set/clear migration breakpoint
php console db:seed                    # Phinx seed:run (all seeders, now idempotent)
php console db:seed -s EmployeeSeeder  # run specific seeder only
php console db:create                  # create database
php console db:drop                    # drop database
php console serve                      # start PHP dev server
php console make:model Name            # app/Models/Name.php
php console make:controller C          # app/Http/Controllers/Modules/User/{C}Controller.php
php console make:migration Name        # create Phinx migration
php console make:seed Name             # create Phinx seeder
php console make:service Name          # app/Services/Modules/...
php console make:repository Name       # app/Repositories/Modules/...
php console make:middleware Name       # app/Http/Middleware/...
php console make:view Name             # resources/views/...
php console test                       # runs Pest (no tests exist yet; see below)
```

## Frontend build

```sh
npm run build       # production (Tailwind CLI + Esbuild)
npm run dev         # watch mode (both)
```

Assets: `resources/css/app.css` + `resources/js/app.js` → `public/css/` + `public/js/`.

## Architecture (module-oriented)

```
app/
  Http/Controllers/Modules/{Module}/
  Services/Modules/{Module}/         + Contracts/*Interface
  Repositories/Modules/{Module}/     + Contracts/*Interface
  Providers/Modules/{Module}/
resources/views/{module}/
routes/                              → loaded via require in providers' boot()
```

Every service & repository has an **interface**. Providers bind interfaces → concretions in `register()`. Routes loaded in `boot()`.

## Views

`eftec/bladeone/BladeOne` (standalone Blade, NOT Laravel Blade). Controllers render via:
```php
$html = $this->blade->run('view.name', $data);
return response($html);
```
Some modules (Accounting) return `$this->blade->run()` directly instead — keep consistent with the module you are editing.

Tailwind CSS `darkMode: 'class'` + CSS custom properties for theming. Alpine.js for interactivity.

## Auth

Custom session-based (no Laravel auth guards). `AuthMiddleware` checks `$_SESSION['user_id']`.
Flash messages via `$_SESSION['flash_success']` / `$_SESSION['flash_error']` (manually unset).

## Database

**Phinx** for migrations/seeds (`database/migrations/`, `database/seeds/`).
Config in `phinx.php`, reads `.env` vars. Default: MySQL.

**Eloquent** ORM via `illuminate/database` Capsule. Models in `app/Models/`.
Password hashing: `password_hash($value, PASSWORD_BCRYPT)` in `setPasswordAttribute` mutator.

## Multi-empresa

`Empresa` model. Session stores `empresa_id`. Switchable via `/company/switch`.
All accounting scoped by `empresa_id`. Helper: `current_empresa()`, `all_empresas()`.

## Config

`config/app.php` — providers list, session path (`storage/cache`), timezone (`Africa/Luanda`).
`config/database.php` — MySQL + SQLite fallback.
Helpers: `app()`, `config()`, `response()`, `redirect()`, `request()`, `session()`, `view()`, `back()` — all custom.

## Testing

`pestphp/pest` ^2.36 is a dev dependency but:
- No `phpunit.xml` or `Pest.php` config exists
- `tests/` directory is empty
- `php console test` will fail until configured
- Do not assume tests exist or run out of the box

## Notable quirks

- `MakeControllerCommand` hardcodes output to `Modules/User/` — verify module before scaffolding
- `AccountController` uses `header('Location: ...'); exit;` instead of `redirect()` helper
- No CI, no lint/format/static analysis config present in repo
- `storage/cache/` for BladeOne compiled views + session files (both `.gitignore`d)
- Locale: Portuguese (`pt_BR`), timezone: `Africa/Luanda`
