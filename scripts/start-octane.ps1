# Start CMS with Laravel Octane (FrankenPHP in Docker). Works on Windows.
$ErrorActionPreference = "Stop"
Set-Location (Join-Path $PSScriptRoot "..")

if (-not (Get-Command docker -ErrorAction SilentlyContinue)) {
    Write-Host "Docker is required for Octane on Windows. Install Docker Desktop, then run:" -ForegroundColor Red
    Write-Host "  composer octane" -ForegroundColor Yellow
    exit 1
}

Write-Host "Starting Laravel Octane (FrankenPHP) at http://127.0.0.1:8000" -ForegroundColor Cyan
Write-Host "Stop with Ctrl+C. Use: composer octane:down" -ForegroundColor DarkGray

docker compose -f docker-compose.octane.yml up --build
