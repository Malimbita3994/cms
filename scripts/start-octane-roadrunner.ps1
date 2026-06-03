# Start Laravel Octane via RoadRunner on Windows (bypasses Artisan signal handling).
$ErrorActionPreference = "Stop"
$root = Resolve-Path (Join-Path $PSScriptRoot "..")
Set-Location $root

# Stop leftover RoadRunner from a previous run (frees ports 8000, 6001, etc.).
Get-Process -Name rr -ErrorAction SilentlyContinue | ForEach-Object {
    Write-Host "Stopping stale RoadRunner (PID $($_.Id))..." -ForegroundColor DarkYellow
    Stop-Process -Id $_.Id -Force -ErrorAction SilentlyContinue
}
Start-Sleep -Milliseconds 400

$php = (Get-Command php).Source
$rr = Join-Path $root "rr.exe"
if (-not (Test-Path $rr)) {
    $rr = Join-Path $root "rr"
}
if (-not (Test-Path $rr)) {
    Write-Host "RoadRunner binary missing. Run:" -ForegroundColor Red
    Write-Host "  vendor\bin\rr.bat get-binary -n" -ForegroundColor Yellow
    exit 1
}

$hostAddr = if ($env:OCTANE_HOST) { $env:OCTANE_HOST } else { "127.0.0.1" }
$port = if ($env:OCTANE_PORT) { $env:OCTANE_PORT } else { "8000" }
# Fewer workers locally = fewer cold boots when Octane restarts (override with OCTANE_WORKERS).
$workers = if ($env:OCTANE_WORKERS) {
    $env:OCTANE_WORKERS
} elseif ($env:APP_ENV -eq "production") {
    "4"
} else {
    "2"
}
$maxJobs = if ($env:OCTANE_MAX_REQUESTS) { $env:OCTANE_MAX_REQUESTS } else { "500" }
$rpcPort = [int]$port - 1999
$workerScript = Join-Path $root "vendor\bin\roadrunner-worker"
$public = Join-Path $root "public"
$maxExec = if ($env:OCTANE_MAX_EXECUTION_TIME) { "$($env:OCTANE_MAX_EXECUTION_TIME)s" } else { "120s" }
$rrConfig = Join-Path $root "rr.octane.yaml"
$configCache = Join-Path $root "bootstrap\cache\config.php"

$env:APP_ENV = if ($env:APP_ENV) { $env:APP_ENV } else { "local" }
$env:APP_BASE_PATH = $root
$env:LARAVEL_OCTANE = "1"

Write-Host "Octane (RoadRunner) at http://${hostAddr}:${port} (${workers} workers, exec_ttl=${maxExec})" -ForegroundColor Cyan
Write-Host "Stop with Ctrl+C" -ForegroundColor DarkGray
if (-not (Test-Path $configCache)) {
    Write-Host "Tip: first admin request can be slow without caches. Run: composer admin:optimize" -ForegroundColor DarkYellow
}

& $rr `
    -c $rrConfig `
    -o "version=3" `
    -o "http.address=${hostAddr}:${port}" `
    -o "server.command=${php},${workerScript}" `
    -o "http.pool.num_workers=${workers}" `
    -o "http.pool.max_jobs=${maxJobs}" `
    -o "rpc.listen=tcp://${hostAddr}:${rpcPort}" `
    -o "http.pool.supervisor.exec_ttl=${maxExec}" `
    -o "http.static.dir=${public}" `
    -o "http.middleware=static" `
    -o "logs.mode=production" `
    -o "logs.output=stdout" `
    -o "logs.level=warn" `
    serve
