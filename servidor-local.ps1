param(
    [Parameter(Mandatory)]
    [ValidateSet('mysql', 'portal', 'cms', 'sistema', 'website')]
    [string]$Sitio
)

$ErrorActionPreference = 'Stop'
Set-Location $PSScriptRoot

if ($Sitio -eq 'mysql') {
    $mysql = (Resolve-Path '.local/mysql/*/bin/mysqld.exe').Path
    $data = (Resolve-Path '.local/mysql-data').Path
    $dbPort = if ($env:LAUNION_MYSQL_PORT) { [int]$env:LAUNION_MYSQL_PORT } else { 3307 }
    & $mysql --no-defaults --datadir="$data" --bind-address=127.0.0.1 "--port=$dbPort" --mysqlx=OFF --max-allowed-packet=512M --console
    exit $LASTEXITCODE
}

$config = @{
    portal  = @{ Port = 8084; PHP = 'php83'; Root = 'launionsur.gob.sv/public_html' }
    cms     = @{ Port = 8082; PHP = 'php83'; Root = 'cms.launionsur.cloud/public_html/app/public' }
    sistema = @{ Port = 8083; PHP = 'php74'; Root = 'sistema.launionsur.cloud/public_html' }
    website = @{ Port = 8088; PHP = 'php83'; Root = 'launionsur.gob.sv/public_html/website/public' }
}[$Sitio]

$phpDir = (Resolve-Path ".local/$($config.PHP)").Path
$router = (Resolve-Path 'router-local.php').Path
$publicRoot = (Resolve-Path $config.Root).Path
$sessionDir = (New-Item -ItemType Directory -Force ".local/sessions/$Sitio").FullName
$env:LAUNION_LOCAL_SITE = $Sitio
if ($Sitio -eq 'sistema') {
    $env:LAUNION_LOCAL_DB_PASSWORD = (Get-Content '.local/app-password' -Raw).Trim()
}

Set-Location $publicRoot
& "$phpDir/php.exe" -c "$phpDir/php.ini" -d "session.save_path=$sessionDir" -S "127.0.0.1:$($config.Port)" -t $publicRoot $router
exit $LASTEXITCODE
