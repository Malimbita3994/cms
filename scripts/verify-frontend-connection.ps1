# Verify CMS ↔ Next.js (D:\web) local integration.
$ErrorActionPreference = "Stop"
$root = Resolve-Path (Join-Path $PSScriptRoot "..")
Set-Location $root

$envFile = Join-Path $root ".env"
if (Test-Path $envFile) {
    Get-Content $envFile | ForEach-Object {
        if ($_ -match '^\s*([A-Za-z_][A-Za-z0-9_]*)\s*=\s*(.*)\s*$' -and $_ -notmatch '^\s*#') {
            $name = $Matches[1]
            $value = $Matches[2].Trim().Trim('"').Trim("'")
            Set-Item -Path "env:$name" -Value $value
        }
    }
}

$cmsUrl = if ($env:APP_URL) { $env:APP_URL.TrimEnd("/") } else { "http://127.0.0.1:8000" }
$frontendUrl = if ($env:NEXT_PUBLIC_SITE_URL) { $env:NEXT_PUBLIC_SITE_URL.TrimEnd("/") } else { "http://localhost:3000" }
$portfolioRoot = if ($env:PORTFOLIO_DISK_ROOT) { $env:PORTFOLIO_DISK_ROOT } else { "D:/web/public" }

Write-Host "CMS API:       $cmsUrl" -ForegroundColor Cyan
Write-Host "Next.js site:  $frontendUrl" -ForegroundColor Cyan
Write-Host "Uploads root:  $portfolioRoot" -ForegroundColor Cyan
Write-Host ""

$ok = $true

if (-not (Test-Path $portfolioRoot)) {
    Write-Host "FAIL: PORTFOLIO_DISK_ROOT does not exist: $portfolioRoot" -ForegroundColor Red
    Write-Host "      Set PORTFOLIO_DISK_ROOT=D:/web/public in .env" -ForegroundColor Yellow
    $ok = $false
} else {
    Write-Host "OK   Portfolio disk root exists" -ForegroundColor Green
}

try {
    $site = Invoke-RestMethod -Uri "$cmsUrl/api/v1/site" -TimeoutSec 10
    Write-Host "OK   GET /api/v1/site -> appName=$($site.appName)" -ForegroundColor Green
} catch {
    Write-Host "FAIL GET /api/v1/site - is Octane running? (composer octane:start)" -ForegroundColor Red
    Write-Host "     $($_.Exception.Message)" -ForegroundColor DarkRed
    $ok = $false
}

try {
    $up = Invoke-WebRequest -Uri "$cmsUrl/up" -UseBasicParsing -TimeoutSec 5
    if ($up.StatusCode -eq 200) {
        Write-Host "OK   GET /up" -ForegroundColor Green
    }
} catch {
    Write-Host "WARN GET /up failed" -ForegroundColor Yellow
}

$webEnv = Join-Path "D:\web" ".env.local"
if (-not (Test-Path $webEnv)) {
    Write-Host "WARN D:\web\.env.local missing - copy from D:\web\.env.local.example" -ForegroundColor Yellow
} else {
    $webContent = Get-Content $webEnv -Raw
    if ($webContent -match 'CMS_API_URL\s*=\s*([^\r\n]+)') {
        Write-Host "OK   D:\web\.env.local CMS_API_URL=$($Matches[1].Trim())" -ForegroundColor Green
    }
}

try {
    $next = Invoke-WebRequest -Uri $frontendUrl -UseBasicParsing -TimeoutSec 8
    if ($next.StatusCode -eq 200) {
        Write-Host "OK   Next.js homepage $frontendUrl" -ForegroundColor Green
    }
} catch {
    Write-Host "INFO Next.js not reachable at $frontendUrl - run: cd D:\web; npm run dev" -ForegroundColor DarkYellow
}

if (-not $env:NEXT_REVALIDATE_SECRET) {
    Write-Host "WARN NEXT_REVALIDATE_SECRET unset in cms/.env" -ForegroundColor Yellow
} else {
    Write-Host "OK   NEXT_REVALIDATE_SECRET is set" -ForegroundColor Green
}

Write-Host ""
if ($ok) {
    Write-Host "CMS is ready for the frontend. Start Next with: cd D:\web; npm run dev" -ForegroundColor Cyan
    exit 0
}
exit 1
