# Clears the CMS site API cache so Next.js receives fresh services/skills with images.
$ErrorActionPreference = "Stop"
Set-Location (Join-Path $PSScriptRoot "..")
php artisan cache:clear
Write-Host "Site API cache cleared. Restart Octane (Ctrl+C, then composer octane:start) if images still missing." -ForegroundColor Cyan
