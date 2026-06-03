# Benchmark key CMS endpoints (requires server on http://127.0.0.1:8000).
$port = if ($env:OCTANE_PORT) { $env:OCTANE_PORT } else { '8000' }
$base = "http://127.0.0.1:$port"
$maxSeconds = 2.0

function Measure-Endpoint($label, $url) {
    $sw = [System.Diagnostics.Stopwatch]::StartNew()
    try {
        $null = Invoke-WebRequest -Uri $url -UseBasicParsing -TimeoutSec 30
        $status = 200
    } catch {
        $status = $_.Exception.Response.StatusCode.value__
        if (-not $status) { $status = 'ERR' }
    }
    $sw.Stop()
    $sec = [math]::Round($sw.Elapsed.TotalSeconds, 3)
    $ok = ($sec -le $maxSeconds)
    [PSCustomObject]@{
        Endpoint = $label
        Seconds  = $sec
        Status   = $status
        Pass     = $ok
    }
}

Write-Host "CMS HTTP benchmark (target <= ${maxSeconds}s per request)`n"

$results = @(
    Measure-Endpoint 'health (warmup)' "$base/up"
    Measure-Endpoint 'health' "$base/up"
    Measure-Endpoint 'site API (cold)' "$base/api/v1/site"
    Measure-Endpoint 'site API (cached)' "$base/api/v1/site"
)

$results | Format-Table -AutoSize

$failed = $results | Where-Object { -not $_.Pass }
if ($failed) {
    Write-Host "FAILED: some endpoints exceeded ${maxSeconds}s" -ForegroundColor Red
    exit 1
}

Write-Host "All endpoints within ${maxSeconds}s." -ForegroundColor Green
