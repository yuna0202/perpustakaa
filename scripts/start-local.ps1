param(
    [string]$LaragonPath = 'C:\laragon',
    [ValidateRange(1, 65535)]
    [int]$WebPort = 8000
)

$ErrorActionPreference = 'Stop'
$projectPath = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path
Set-Location -LiteralPath $projectPath
$phpExecutable = Get-ChildItem -LiteralPath (Join-Path $LaragonPath 'bin\php') -Filter php.exe -Recurse | Sort-Object FullName -Descending | Select-Object -First 1 -ExpandProperty FullName
if (!$phpExecutable) { throw 'PHP Laragon tidak ditemukan.' }
if (!(Test-Path 'vendor/autoload.php') -or !(Test-Path 'public/build/manifest.json')) { throw 'Jalankan composer install, npm ci, dan npm run build terlebih dahulu.' }
if (!(Test-Path '.env')) { throw 'Salin .env.example ke .env, konfigurasi database, lalu jalankan php artisan key:generate.' }
if ((Get-Content '.env' -Raw) -notmatch '(?m)^APP_ENV=local\s*$') { throw 'Helper ini hanya untuk APP_ENV=local.' }

# Gunakan koneksi yang sama dengan aplikasi. Jangan membuat instance MySQL kedua
# atau menjalankan seeder setiap startup: data yang dihapus admin harus tetap terhapus.
& $phpExecutable artisan config:clear --no-ansi
if ($LASTEXITCODE -ne 0) { throw 'Konfigurasi Laravel tidak dapat dimuat ulang.' }
& $phpExecutable artisan migrate:status --no-ansi
if ($LASTEXITCODE -ne 0) {
    throw 'Database belum siap. Buka Laragon dan klik Start All, lalu cocokkan DB_HOST/DB_PORT/DB_DATABASE di .env. Instalasi baru: jalankan php artisan migrate --seed sekali.'
}
if (!(Test-Path 'public/storage')) {
    New-Item -ItemType Junction -Path (Join-Path $projectPath 'public\storage') -Target (Join-Path $projectPath 'storage\app\public') | Out-Null
}
New-Item -ItemType Directory -Path (Join-Path $projectPath '.runtime') -Force | Out-Null

function Get-ActiveLanAddress {
    $candidates = @(
        foreach ($nic in [System.Net.NetworkInformation.NetworkInterface]::GetAllNetworkInterfaces()) {
            if ($nic.OperationalStatus -ne 'Up' -or $nic.NetworkInterfaceType -in @('Loopback', 'Tunnel')) { continue }
            $properties = $nic.GetIPProperties()
            $hasGateway = @($properties.GatewayAddresses | Where-Object {
                $_.Address.AddressFamily -eq [System.Net.Sockets.AddressFamily]::InterNetwork
            }).Count -gt 0
            foreach ($unicast in $properties.UnicastAddresses) {
                if ($unicast.Address.AddressFamily -ne [System.Net.Sockets.AddressFamily]::InterNetwork) { continue }
                $address = $unicast.Address.IPAddressToString
                $bytes = $unicast.Address.GetAddressBytes()
                try { $usesDhcp = $properties.GetIPv4Properties().IsDhcpEnabled }
                catch { $usesDhcp = $false }
                $private = $bytes[0] -eq 10 -or ($bytes[0] -eq 172 -and $bytes[1] -ge 16 -and $bytes[1] -le 31) -or
                    ($bytes[0] -eq 192 -and $bytes[1] -eq 168)
                if ($private) {
                    [pscustomobject]@{ Address = $address; HasGateway = $hasGateway; UsesDhcp = $usesDhcp }
                }
            }
        }
    )
    $chosen = $candidates | Sort-Object -Property @{ Expression = 'HasGateway'; Descending = $true },
        @{ Expression = 'UsesDhcp'; Descending = $true }, Address | Select-Object -First 1
    return $chosen.Address
}

function Start-PhpServer([string]$address, [string]$name) {
    $router = Join-Path $projectPath 'vendor\laravel\framework\src\Illuminate\Foundation\resources\server.php'
    $public = Join-Path $projectPath 'public'
    return Start-Process -FilePath $phpExecutable -ArgumentList @('-S', "${address}:$WebPort", '-t', $public, $router) `
        -WorkingDirectory $public -WindowStyle Hidden `
        -RedirectStandardOutput (Join-Path $projectPath ".runtime\$name-out.log") `
        -RedirectStandardError (Join-Path $projectPath ".runtime\$name-error.log") -PassThru
}

$activeLan = Get-ActiveLanAddress
if (!$activeLan) { throw 'Tidak ada IPv4 LAN privat aktif. Sambungkan Wi-Fi atau Ethernet lalu jalankan lagi.' }

$monitorPidFile = Join-Path $projectPath '.runtime\lan-monitor.pid'
$lanUrlFile = Join-Path $projectPath '.runtime\active-lan-url'
if (Test-Path -LiteralPath $monitorPidFile) {
    $existingPid = 0
    [void][int]::TryParse((Get-Content -LiteralPath $monitorPidFile -Raw).Trim(), [ref]$existingPid)
    $existingMonitor = if ($existingPid -gt 0) { Get-CimInstance Win32_Process -Filter "ProcessId = $existingPid" -ErrorAction SilentlyContinue }
    if ($existingMonitor -and $existingMonitor.CommandLine -match 'start-local\.ps1') {
        Write-Host "Pemantau LAN sudah berjalan (PID $existingPid)."
        Write-Host "Komputer ini: http://127.0.0.1:$WebPort"
        Write-Host "Jaringan LAN: http://${activeLan}:$WebPort"
        return
    }
    Remove-Item -LiteralPath $monitorPidFile -Force
}

$listeners = @(Get-NetTCPConnection -LocalPort $WebPort -State Listen -ErrorAction SilentlyContinue)
if ($listeners.Count -gt 0) {
    throw "Port $WebPort sudah dipakai server lain. Hentikan server lama sebelum menjalankan pemantau LAN."
}

$localServer = $null
$lanServer = $null
try {
    Set-Content -LiteralPath $monitorPidFile -Value $PID -NoNewline
    $localServer = Start-PhpServer '127.0.0.1' 'local-server'
    $lanServer = Start-PhpServer $activeLan 'lan-server'
    Set-Content -LiteralPath $lanUrlFile -Value "http://${activeLan}:$WebPort" -NoNewline
    Write-Host "Komputer ini: http://127.0.0.1:$WebPort"
    Write-Host "Jaringan LAN: http://${activeLan}:$WebPort"
    Write-Host 'IP LAN dipantau tiap 5 detik. Buka kartu lewat alamat LAN sebelum mencetak QR. Tekan Ctrl+C untuk berhenti.'

    while ($true) {
        Start-Sleep -Seconds 5
        if ($localServer.HasExited) { throw 'Server localhost berhenti. Periksa .runtime/local-server-error.log.' }
        $nextLan = Get-ActiveLanAddress
        if ($nextLan -ne $activeLan) {
            if ($lanServer) {
                if (!$lanServer.HasExited) { Stop-Process -Id $lanServer.Id -Force -ErrorAction SilentlyContinue }
                $lanServer = $null
            }
            $activeLan = $nextLan
            if ($activeLan) {
                $lanServer = Start-PhpServer $activeLan 'lan-server'
                Set-Content -LiteralPath $lanUrlFile -Value "http://${activeLan}:$WebPort" -NoNewline
                Write-Host "LAN berubah: http://${activeLan}:$WebPort"
            } else {
                Remove-Item -LiteralPath $lanUrlFile -Force -ErrorAction SilentlyContinue
                Write-Host 'Koneksi LAN terputus. Menunggu alamat LAN privat baru.'
            }
        } elseif ($lanServer -and $lanServer.HasExited) {
            throw 'Server LAN berhenti. Periksa .runtime/lan-server-error.log.'
        }
    }
} finally {
    if ($lanServer -and !$lanServer.HasExited) { Stop-Process -Id $lanServer.Id -Force -ErrorAction SilentlyContinue }
    if ($localServer -and !$localServer.HasExited) { Stop-Process -Id $localServer.Id -Force -ErrorAction SilentlyContinue }
    Remove-Item -LiteralPath $lanUrlFile -Force -ErrorAction SilentlyContinue
    if ((Test-Path -LiteralPath $monitorPidFile) -and (Get-Content -LiteralPath $monitorPidFile -Raw).Trim() -eq [string]$PID) {
        Remove-Item -LiteralPath $monitorPidFile -Force
    }
}
