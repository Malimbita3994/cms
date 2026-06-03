# Warm Laravel caches for faster Filament admin (run after deploy or large PHP changes).
$ErrorActionPreference = "Stop"
Set-Location (Resolve-Path (Join-Path $PSScriptRoot ".."))

Write-Host "Clearing stale caches..." -ForegroundColor Cyan
php artisan optimize:clear

Write-Host "Building admin assets..." -ForegroundColor Cyan
npm run build

Write-Host "Caching config, routes, views, events, Filament..." -ForegroundColor Cyan
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan filament:cache-components

Write-Host "Done. Restart Octane (composer octane:start) to load PHP changes." -ForegroundColor Green
