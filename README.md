# Portfolio CMS (Laravel + Filament)

Backend CMS for the Next.js portfolio in the repository root.

## Local URLs

- Admin panel: `http://127.0.0.1:8000/admin`
- Public API: `http://127.0.0.1:8000/api/v1/site`
- Case studies API: `http://127.0.0.1:8000/api/v1/case-studies`

## Quick Start

```bash
cd cms
composer install
php artisan migrate:fresh --seed
php artisan serve --no-reload
```

## Laravel Octane (recommended for speed)

Octane keeps the app booted in memory so admin clicks and `/api/v1/site` respond much faster.

### Windows (RoadRunner — native)

1. Enable `extension=sockets` in `php.ini` (XAMPP: `C:\xampp\php\php.ini`).
2. Install the RoadRunner binary (once):

```bash
cd cms
composer require spiral/roadrunner-cli --dev
vendor\bin\rr.bat get-binary -n
php artisan octane:install --server=roadrunner
```

3. Stop `php artisan serve` (port 8000 must be free), then start Octane:

```bash
composer octane:start
# or: powershell -File scripts/start-octane-roadrunner.ps1
```

After PHP or Blade changes, stop (Ctrl+C) and start again. (`php artisan octane:reload` requires Linux/WSL.)

Verified on Windows: `/up` ~0.5s, `/api/v1/site` ~0.4–0.6s, `/admin/login` ~0.2s.

### Windows / macOS (Docker + FrankenPHP)

If native RoadRunner is unavailable, use Docker:

1. Install [Docker Desktop](https://www.docker.com/products/docker-desktop/).
2. In `cms/.env`, set `DB_HOST=host.docker.internal` so the container can reach MySQL on your machine.
3. Stop `php artisan serve` if it is running.
4. Start Octane:

```bash
cd cms
composer octane
# or: powershell -File scripts/start-octane.ps1
```

CMS runs at `http://127.0.0.1:8000`. Stop with Ctrl+C, or `composer octane:down`.

### WSL / Linux / macOS (native FrankenPHP)

```bash
php artisan octane:install --server=frankenphp
php artisan octane:start --host=127.0.0.1 --port=8000 --workers=4
```

Reload after code changes: `php artisan octane:reload`

### Benchmark

```bash
composer bench
```

## Default Local Admin Account

- Email: `admin@example.local`
- Password: `password`

Use this only for local development.

## Connect Next.js Frontend

In `d:\web\.env.local`:

```env
CMS_API_URL=http://127.0.0.1:8000
```

Then restart Next.js dev server (`npm run dev`).

## Content Managed in CMS

- Site settings (title, description, URL)
- Profile and contact
- Career timeline
- Skills
- Portfolio projects
- Services
- Insights
- Case studies

## Production deployment

Copy `cms/.env.production.example` to `cms/.env` and follow the root guide:

**[../DEPLOYMENT.md](../DEPLOYMENT.md)**

```bash
# From repo root (Linux VPS)
bash scripts/prepare-hosting.sh

# Or CMS-only optimize after deploy
composer admin:optimize
composer deploy:prepare
```

Docker: `docker compose -f ../docker-compose.prod.yml up -d --build` from repo root.

## Production / Security Checklist

1. Change admin credentials immediately.
2. Set `APP_ENV=production` and `APP_DEBUG=false` in `cms/.env`.
3. Use a strong `APP_KEY` and secure database credentials.
4. Serve over HTTPS behind a trusted domain/reverse proxy (`TRUSTED_PROXIES=*` is set in production example).
5. Restrict who can access `/admin` (network policy, auth hardening).
6. Set frontend `CMS_API_URL` to the production CMS URL.
7. Match `NEXT_REVALIDATE_SECRET` and `NEXT_PUBLIC_SITE_URL` with the Next.js app.
8. Run database backups and keep dependencies updated.
