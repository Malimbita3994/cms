# Upload D:\cms and D:\web to the server as folders (no local zip).
# Requires OpenSSH scp; uses rsync when available (Git/CW/rsync in PATH).
#
# Usage:
#   powershell -File scripts/deploy-scp-folders.ps1
#   powershell -File scripts/deploy-scp-folders.ps1 -SkipBuild
#   powershell -File scripts/deploy-scp-folders.ps1 -CmsOnly
#   powershell -File scripts/deploy-scp-folders.ps1 -WebOnly
#
# Stop php artisan serve / RoadRunner before upload.

param(
    [string]$WebRoot = $(if ($env:WEB_ROOT) { $env:WEB_ROOT } else { "D:\web" }),
    [string]$CmsRoot = $(Resolve-Path (Join-Path $PSScriptRoot "..")),
    [string]$SshHost = $(if ($env:SSH_HOST) { $env:SSH_HOST } else { "famlynva@198.54.116.91" }),
    [string]$SshPort = $(if ($env:SSH_PORT) { $env:SSH_PORT } else { "21098" }),
    [string]$RemoteCms = $(if ($env:REMOTE_CMS) { $env:REMOTE_CMS } else { "~/cms" }),
    [string]$RemoteWeb = $(if ($env:REMOTE_WEB) { $env:REMOTE_WEB } else { "~/web" }),
    [switch]$SkipBuild,
    [switch]$CmsOnly,
    [switch]$WebOnly
)

$ErrorActionPreference = "Stop"
$WebRoot = (Resolve-Path $WebRoot).Path
$CmsRoot = (Resolve-Path $CmsRoot).Path
$ssh = "ssh -p $SshPort"
$scp = "scp -P $SshPort"

function Remove-BrokenStorageLink {
    param([string]$ProjectRoot)
    $link = Join-Path $ProjectRoot "public\storage"
    if (Test-Path $link) {
        cmd /c rmdir "$link" 2>$null | Out-Null
        Remove-Item $link -Force -ErrorAction SilentlyContinue
    }
}

function Test-RsyncAvailable {
    return [bool](Get-Command rsync -ErrorAction SilentlyContinue)
}

function Sync-FolderRsync {
    param(
        [string]$LocalPath,
        [string]$RemotePath,
        [string[]]$Excludes
    )

    $local = $LocalPath.TrimEnd('\') + [IO.Path]::DirectorySeparatorChar
    $args = @(
        "-avz", "--progress", "--delete-delay",
        "-e", "ssh -p $SshPort"
    )
    foreach ($ex in $Excludes) {
        $args += "--exclude=$ex"
    }
    $args += $local, "${SshHost}:${RemotePath}/"

    Write-Host "rsync $($args -join ' ')" -ForegroundColor DarkGray
    & rsync @args
    if ($LASTEXITCODE -ne 0) {
        throw "rsync failed (exit $LASTEXITCODE)"
    }
}

function Sync-CmsScp {
    param([string]$LocalRoot, [string]$RemoteDir)

    $skipNames = @(
        "vendor", "node_modules", ".git", "tests", ".phpunit.cache",
        ".phpunit.result.cache", "rr.exe", "rr", ".env", ".env.local", ".env.backup",
        "deploy-packages"
    )

    Write-Host "Creating remote directory $RemoteDir ..." -ForegroundColor Cyan
    & ssh -p $SshPort $SshHost "mkdir -p $RemoteDir"

    Get-ChildItem -Path $LocalRoot -Force | Where-Object {
        $_.Name -notin $skipNames
    } | ForEach-Object {
        $dest = "${SshHost}:${RemoteDir}/$($_.Name)"
        Write-Host "  scp -> $($_.Name)" -ForegroundColor Cyan
        if ($_.PSIsContainer) {
            if ($_.Name -eq "storage") {
                # Upload storage skeleton only (no logs/cache/sessions)
                & ssh -p $SshPort $SshHost "mkdir -p $RemoteDir/storage/app/public $RemoteDir/storage/framework $RemoteDir/storage/logs"
                foreach ($sub in @("app", "framework")) {
                    $subPath = Join-Path $_.FullName $sub
                    if (Test-Path $subPath) {
                        & scp -P $SshPort -r $subPath $dest 2>&1 | Out-Host
                    }
                }
            } elseif ($_.Name -eq "bootstrap") {
                & ssh -p $SshPort $SshHost "mkdir -p $RemoteDir/bootstrap/cache"
                & scp -P $SshPort -r (Join-Path $_.FullName "app.php") "${dest}/" 2>&1 | Out-Host
                & scp -P $SshPort -r (Join-Path $_.FullName "providers.php") "${dest}/" 2>&1 | Out-Host
                Get-ChildItem (Join-Path $_.FullName "cache") -File -ErrorAction SilentlyContinue | ForEach-Object {
                    if ($_.Name -eq ".gitignore") {
                        & scp -P $SshPort $_.FullName "${dest}/cache/" 2>&1 | Out-Host
                    }
                }
            } else {
                & scp -P $SshPort -r $_.FullName $dest 2>&1 | Out-Host
            }
        } else {
            & scp -P $SshPort $_.FullName $dest 2>&1 | Out-Host
        }
        if ($LASTEXITCODE -ne 0) {
            throw "scp failed for $($_.Name)"
        }
    }
}

function Sync-WebScp {
    param([string]$LocalRoot, [string]$RemoteDir)

    $skipNames = @("node_modules", ".git", "tests", ".env.local", "deploy-packages")

    & ssh -p $SshPort $SshHost "mkdir -p $RemoteDir"
    Get-ChildItem -Path $LocalRoot -Force | Where-Object {
        $_.Name -notin $skipNames
    } | ForEach-Object {
        if ($_.Name -eq ".next") {
            Write-Host "  scp -> .next (standalone + static only)" -ForegroundColor Cyan
            & ssh -p $SshPort $SshHost "mkdir -p $RemoteDir/.next"
            $standalone = Join-Path $_.FullName "standalone"
            $static = Join-Path $_.FullName "static"
            if (Test-Path $standalone) {
                & scp -P $SshPort -r $standalone "${SshHost}:${RemoteDir}/.next/" 2>&1 | Out-Host
            }
            if (Test-Path $static) {
                & scp -P $SshPort -r $static "${SshHost}:${RemoteDir}/.next/" 2>&1 | Out-Host
            }
            Get-ChildItem $_.FullName -File | ForEach-Object {
                & scp -P $SshPort $_.FullName "${SshHost}:${RemoteDir}/.next/" 2>&1 | Out-Host
            }
            return
        }
        $dest = "${SshHost}:${RemoteDir}/$($_.Name)"
        Write-Host "  scp -> $($_.Name)" -ForegroundColor Cyan
        if ($_.PSIsContainer) {
            & scp -P $SshPort -r $_.FullName $dest 2>&1 | Out-Host
        } else {
            & scp -P $SshPort $_.FullName $dest 2>&1 | Out-Host
        }
        if ($LASTEXITCODE -ne 0) {
            throw "scp failed for $($_.Name)"
        }
    }
}

$useRsync = Test-RsyncAvailable
Write-Host "CMS:  $CmsRoot -> $RemoteCms" -ForegroundColor Cyan
Write-Host "Web:  $WebRoot -> $RemoteWeb" -ForegroundColor Cyan
Write-Host "SSH:  $SshHost (port $SshPort)" -ForegroundColor Cyan
Write-Host "Tool: $(if ($useRsync) { 'rsync (fast, excludes vendor/node_modules)' } else { 'scp per folder (slower)' })" -ForegroundColor Cyan

if (Get-Process -Name php, rr -ErrorAction SilentlyContinue) {
    Write-Host "WARN: Stop php artisan serve / RoadRunner to avoid locked files." -ForegroundColor Yellow
}

if (-not $SkipBuild) {
    if (-not $CmsOnly) {
        Push-Location $WebRoot
        try {
            if (-not (Test-Path ".env.local")) { Copy-Item ".env.production.example" ".env.local" }
            npm ci
            npm run build
        } finally { Pop-Location }
    }
    if (-not $WebOnly) {
        Push-Location $CmsRoot
        try {
            if (-not (Test-Path ".env")) { Copy-Item ".env.production.example" ".env" }
            composer install --no-dev --optimize-autoloader --no-interaction
            npm ci
            npm run build
        } finally { Pop-Location }
    }
}

$cmsExcludes = @(
    "vendor", "node_modules", ".git", "tests", ".phpunit.cache", ".phpunit.result.cache",
    "storage/logs", "storage/framework/cache", "storage/framework/sessions",
    "storage/framework/views", "storage/pail", "bootstrap/cache", "rr.exe", "rr",
    "public/storage", ".env", ".env.local", ".env.backup"
)
$webExcludes = @("node_modules", ".git", ".next/cache", "tests", ".env.local")

if (-not $WebOnly) {
    Remove-BrokenStorageLink -ProjectRoot $CmsRoot
    Write-Host "`n==> Upload CMS..." -ForegroundColor Green
    if ($useRsync) {
        Sync-FolderRsync -LocalPath $CmsRoot -RemotePath $RemoteCms -Excludes $cmsExcludes
    } else {
        Sync-CmsScp -LocalRoot $CmsRoot -RemoteDir $RemoteCms
    }
}

if (-not $CmsOnly) {
    Write-Host "`n==> Upload Web..." -ForegroundColor Green
    if ($useRsync) {
        Sync-FolderRsync -LocalPath $WebRoot -RemotePath $RemoteWeb -Excludes $webExcludes
    } else {
        Sync-WebScp -LocalRoot $WebRoot -RemoteDir $RemoteWeb
    }
}

Write-Host "`n==> Upload finished" -ForegroundColor Green
Write-Host @"
On the server:

  cd ~/cms
  composer install --no-dev --optimize-autoloader
  cp .env.production.example .env   # if needed; edit DB_* and URLs
  php artisan key:generate --force
  php artisan migrate --force
  php artisan storage:link
  php artisan config:cache && php artisan route:cache && php artisan view:cache

  cd ~/web
  npm ci --omit=dev && npm run build    # if Node is on the host
  # Or use cPanel Node.js app pointing at ~/web

Set PORTFOLIO_DISK_ROOT=/home/famlynva/web/public in cms/.env
Point cms.famledger.org document root to ~/cms/public

"@ -ForegroundColor DarkGray

if (-not $useRsync) {
    Write-Host "Tip: install rsync (Git for Windows / cwRsync) for faster uploads with excludes." -ForegroundColor Yellow
}
