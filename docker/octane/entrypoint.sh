#!/bin/sh
set -e

cd /app

if [ ! -f vendor/autoload.php ]; then
    echo "Installing Composer dependencies (first run)..."
    composer install --no-interaction --prefer-dist
fi

if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    php artisan key:generate --force --no-interaction 2>/dev/null || true
fi

exec php artisan octane:frankenphp \
    --host="${OCTANE_HOST:-0.0.0.0}" \
    --port="${OCTANE_PORT:-8000}" \
    --workers="${OCTANE_WORKERS:-4}" \
    --max-requests="${OCTANE_MAX_REQUESTS:-500}" \
    "$@"
