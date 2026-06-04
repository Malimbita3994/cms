# Package D:\cms + D:\web for upload to cPanel-style shared hosting (SSH).
# Usage (from D:\cms):
#   powershell -File scripts/deploy-shared-hosting.ps1
#   powershell -File scripts/deploy-shared-hosting.ps1 -SkipBuild   # zip only
#
# Stop php artisan serve / RoadRunner before packaging (avoids locked files).

param(
    [string]$WebRoot = $(if ($env:WEB_ROOT) { $env:WEB_ROOT } else { "D:\web" }),
    [string]$CmsRoot = $(Resolve-Path (Join-Path $PSScriptRoot "..")),
    [string]$OutDir = $(if ($env:DEPLOY_OUT) { $env:DEPLOY_OUT } else { "D:\deploy-packages" }),
    [switch]$SkipBuild
)

$ErrorActionPreference = "Stop"
$WebRoot = (Resolve-Path $WebRoot).Path
$CmsRoot = (Resolve-Path $CmsRoot).Path
$OutDir = (New-Item -ItemType Directory -Force -Path $OutDir).FullName
$stamp = Get-Date -Format "yyyyMMdd-HHmm"

Write-Host "CMS:  $CmsRoot" -ForegroundColor Cyan
Write-Host "Web:  $WebRoot" -ForegroundColor Cyan
Write-Host "Out:  $OutDir" -ForegroundColor Cyan

$locked = Get-Process -Name php, rr -ErrorAction SilentlyContinue
if ($locked) {
    Write-Host "`nWARN: Stop php artisan serve / RoadRunner before packaging to avoid locked files." -ForegroundColor Yellow
    $locked | Format-Table Id, ProcessName -AutoSize
}

if (-not $SkipBuild) {
    Write-Host "`n==> Build Next.js..." -ForegroundColor Cyan
    Push-Location $WebRoot
    try {
        if (-not (Test-Path ".env.local")) {
            Copy-Item ".env.production.example" ".env.local"
            Write-Host "    Created web/.env.local — edit CMS_API_URL and URLs before production." -ForegroundColor Yellow
        }
        npm ci
        npm run build
    } finally {
        Pop-Location
    }

    Write-Host "`n==> Build CMS (composer + vite)..." -ForegroundColor Cyan
    Push-Location $CmsRoot
    try {
        if (-not (Test-Path ".env")) {
            Copy-Item ".env.production.example" ".env"
            Write-Host "    Created cms/.env — edit DB and URLs on the server." -ForegroundColor Yellow
        }
        composer install --no-dev --optimize-autoloader --no-interaction
        npm ci
        npm run build
    } finally {
        Pop-Location
    }
}

function New-DeployArchive {
    param(
        [string]$Name,
        [string]$Source,
        [string[]]$TarExcludes
    )

    if (-not (Get-Command tar -ErrorAction SilentlyContinue)) {
        throw "tar.exe not found (requires Windows 10+). Use WSL or install bsdtar."
    }

    $zip = Join-Path $OutDir "$Name-$stamp.zip"
    if (Test-Path $zip) { Remove-Item -Force $zip }

    Write-Host "`n==> Package $Name..." -ForegroundColor Cyan

    # Broken storage junction (old path) breaks Windows tar.
    $storageLink = Join-Path $Source "public\storage"
    if (Test-Path $storageLink) {
        cmd /c rmdir "$storageLink" 2>$null | Out-Null
        Remove-Item $storageLink -Force -ErrorAction SilentlyContinue
    }

    Push-Location $Source
    try {
        $tarArgs = @("-acf", $zip)
        foreach ($pattern in $TarExcludes) {
            $tarArgs += "--exclude=$pattern"
        }
        $tarArgs += "*"

        & tar @tarArgs
        if ($LASTEXITCODE -ne 0) {
            throw "tar failed for $Name (exit $LASTEXITCODE)"
        }
    } finally {
        Pop-Location
    }

    $mb = [math]::Round((Get-Item $zip).Length / 1MB, 1)
    Write-Host "    $zip ($mb MB)" -ForegroundColor Green
    return $zip
}

$cmsExcludes = @(
    "vendor",
    "node_modules",
    ".git",
    "tests",
    ".phpunit.cache",
    ".phpunit.result.cache",
    "storage/logs",
    "storage/framework/cache",
    "storage/framework/sessions",
    "storage/framework/views",
    "storage/pail",
    "bootstrap/cache",
    "rr.exe",
    "rr",
    "public/storage",
    ".env",
    ".env.local",
    ".env.backup"
)

$webExcludes = @(
    "node_modules",
    ".git",
    ".next/cache",
    "tests",
    ".env.local"
)

$cmsZip = New-DeployArchive -Name "cms" -Source $CmsRoot -TarExcludes $cmsExcludes
$webZip = New-DeployArchive -Name "web" -Source $WebRoot -TarExcludes $webExcludes

$sshHost = if ($env:SSH_HOST) { $env:SSH_HOST } else { "famlynva@198.54.116.91" }
$sshPort = if ($env:SSH_PORT) { $env:SSH_PORT } else { "21098" }

Write-Host "`n==> Packages ready" -ForegroundColor Green
Write-Host ""
Write-Host "Upload:" -ForegroundColor Cyan
Write-Host "  scp -P $sshPort `"$cmsZip`" ${sshHost}:~/cms-deploy.zip"
Write-Host "  scp -P $sshPort `"$webZip`" ${sshHost}:~/web-deploy.zip"
Write-Host ""
Write-Host "Server:" -ForegroundColor Cyan
Write-Host "  unzip -qo ~/cms-deploy.zip -d ~/cms"
Write-Host "  unzip -qo ~/web-deploy.zip -d ~/web"
Write-Host "  bash ~/cms/scripts/server-setup-shared-hosting.sh"
