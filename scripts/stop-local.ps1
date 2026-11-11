$ErrorActionPreference = 'Stop'
$projectPath = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path
$pidFile = Join-Path $projectPath '.runtime\lan-monitor.pid'

if (!(Test-Path -LiteralPath $pidFile)) {
    Write-Host 'Pemantau LAN proyek tidak sedang berjalan.'
    return
}

$monitorPid = 0
[void][int]::TryParse((Get-Content -LiteralPath $pidFile -Raw).Trim(), [ref]$monitorPid)
$monitor = if ($monitorPid -gt 0) { Get-CimInstance Win32_Process -Filter "ProcessId = $monitorPid" -ErrorAction SilentlyContinue }
$expectedScript = (Join-Path $projectPath 'scripts\start-local.ps1').Replace('\', '/')
if (!$monitor) {
    Remove-Item -LiteralPath $pidFile -Force
    Remove-Item -LiteralPath (Join-Path $projectPath '.runtime\active-lan-url') -Force -ErrorAction SilentlyContinue
    Write-Host 'Pemantau LAN lama sudah berhenti; file status dibersihkan.'
    return
}
if ($monitor.CommandLine.Replace('\', '/').IndexOf($expectedScript, [StringComparison]::OrdinalIgnoreCase) -lt 0) {
    throw 'PID pemantau tidak cocok dengan proyek ini. Tidak ada proses yang dihentikan.'
}

$expectedRouter = (Join-Path $projectPath 'vendor\laravel\framework\src\Illuminate\Foundation\resources\server.php').Replace('\', '/')
$servers = @(Get-CimInstance Win32_Process | Where-Object { $_.ParentProcessId -eq $monitorPid -and $_.Name -eq 'php.exe' })
foreach ($server in $servers) {
    if ($server.CommandLine.Replace('\', '/').IndexOf($expectedRouter, [StringComparison]::OrdinalIgnoreCase) -lt 0) {
        throw "Proses PHP $($server.ProcessId) tidak cocok dengan proyek ini. Tidak ada proses yang dihentikan."
    }
}

Stop-Process -Id $monitorPid -Force
foreach ($server in $servers) { Stop-Process -Id $server.ProcessId -Force -ErrorAction SilentlyContinue }
Remove-Item -LiteralPath $pidFile -Force
Remove-Item -LiteralPath (Join-Path $projectPath '.runtime\active-lan-url') -Force -ErrorAction SilentlyContinue
Write-Host 'Pemantau LAN dan server proyek dihentikan.'
