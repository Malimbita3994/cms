#!/bin/sh
set -e

cd /app

if [ -z "$APP_KEY" ]; then
    echo "ERROR: APP_KEY is not set. Run: php artisan key:generate" >&2
    exit 1
fi

php artisan migrate --force --no-interaction

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan filament:cache-components

exec php artisan octane:frankenphp \
    --host="${OCTANE_HOST:-0.0.0.0}" \
    --port="${OCTANE_PORT:-8000}" \
    --workers="${OCTANE_WORKERS:-4}" \
    --max-requests="${OCTANE_MAX_REQUESTS:-500}" \
    "$@"
