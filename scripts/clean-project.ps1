# Remove regenerable caches, logs, and build artifacts from D:\cms.
# Keeps vendor/, node_modules/, rr.exe, .env — run "npm ci" / "composer install" if you removed those manually.
param(
    [switch]$IncludeNodeModules,
    [switch]$IncludeVendor
)

$ErrorActionPreference = "Stop"
$root = Resolve-Path (Join-Path $PSScriptRoot "..")
Set-Location $root

Write-Host "Cleaning: $root" -ForegroundColor Cyan

function Remove-IfExists {
    param([string]$Path)
    if (Test-Path $Path) {
        Remove-Item -Recurse -Force $Path
        Write-Host "  removed $Path" -ForegroundColor DarkGray
    }
}

function Clear-DirContents {
    param([string]$Dir)
    if (Test-Path $Dir) {
        Get-ChildItem $Dir -Force -ErrorAction SilentlyContinue | Where-Object { $_.Name -ne ".gitignore" } | Remove-Item -Recurse -Force -ErrorAction SilentlyContinue
        Write-Host "  cleared $Dir" -ForegroundColor DarkGray
    }
}

# Broken junction from old path (breaks tar/deploy)
$storageLink = Join-Path $root "public\storage"
if (Test-Path $storageLink) {
    cmd /c rmdir "$storageLink" 2>$null | Out-Null
    Remove-Item $storageLink -Force -ErrorAction SilentlyContinue
    Write-Host "  removed broken public/storage link" -ForegroundColor DarkGray
}

Remove-IfExists (Join-Path $root "public\build")
Remove-IfExists (Join-Path $root ".phpunit.cache")
Remove-IfExists (Join-Path $root ".phpunit.result.cache")
Remove-IfExists (Join-Path $root "storage\pail")
Remove-IfExists (Join-Path $root ".rr.yaml")

Clear-DirContents (Join-Path $root "storage\logs")
Clear-DirContents (Join-Path $root "storage\framework\cache")
Clear-DirContents (Join-Path $root "storage\framework\sessions")
Clear-DirContents (Join-Path $root "storage\framework\views")
Clear-DirContents (Join-Path $root "bootstrap\cache\filament")

Get-ChildItem (Join-Path $root "bootstrap\cache") -File -ErrorAction SilentlyContinue | Remove-Item -Force

if ($IncludeNodeModules) { Remove-IfExists (Join-Path $root "node_modules") }
if ($IncludeVendor) { Remove-IfExists (Join-Path $root "vendor") }

if (Get-Command php -ErrorAction SilentlyContinue) {
    foreach ($cmd in @("optimize:clear", "view:clear", "cache:clear")) {
        php artisan $cmd 2>$null | Out-Null
    }
    Write-Host "  artisan optimize:clear" -ForegroundColor DarkGray
}

Write-Host "Done." -ForegroundColor Green
Write-Host "Regenerate: npm run build (admin assets), php artisan storage:link (if needed)" -ForegroundColor DarkYellow
