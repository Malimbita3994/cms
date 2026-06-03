#!/usr/bin/env bash
# Warm Laravel caches for faster Filament admin (run after deploy or large PHP changes).
set -euo pipefail
cd "$(dirname "$0")/.."

echo "Clearing stale caches..."
php artisan optimize:clear

echo "Building admin assets..."
npm run build

echo "Caching config, routes, views, events..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan filament:cache-components

echo "Done. Restart Octane to load PHP changes."
