$ErrorActionPreference = 'Stop'
Set-Location $PSScriptRoot

if (!(Test-Path '.local/app-password')) {
    throw 'La restauración de las bases locales aún no se ha completado.'
}

$powerShell = (Get-Process -Id $PID).Path
$script = Join-Path $PSScriptRoot 'servidor-local.ps1'
$dbPort = if ($env:LAUNION_MYSQL_PORT) { [int]$env:LAUNION_MYSQL_PORT } else { 3307 }
$servicios = [ordered]@{ mysql = $dbPort; portal = 8084; cms = 8082; sistema = 8083; website = 8088 }
foreach ($nombre in $servicios.Keys) {
    $port = $servicios[$nombre]
    if (!(Get-NetTCPConnection -LocalAddress 127.0.0.1 -LocalPort $port -State Listen -ErrorAction SilentlyContinue)) {
        Start-Process -FilePath $powerShell -ArgumentList @('-NoProfile', '-File', ('"' + $script + '"'), '-Sitio', $nombre) -WindowStyle Hidden `
            -RedirectStandardOutput ".local/$nombre-arranque.out" -RedirectStandardError ".local/$nombre-arranque.err"
    }
}

Write-Output 'Portal   http://127.0.0.1:8084/'
Write-Output 'CMS      http://127.0.0.1:8082/login'
Write-Output 'Sistema  http://127.0.0.1:8083/admin/'
Write-Output 'Vista del CMS  http://127.0.0.1:8088/'
