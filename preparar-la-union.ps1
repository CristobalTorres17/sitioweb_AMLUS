$ErrorActionPreference = 'Stop'
Set-Location $PSScriptRoot

function Assert-Exit([string]$Step) {
    if ($LASTEXITCODE -ne 0) { throw "$Step falló (código $LASTEXITCODE)." }
}

function Write-EnvFile([string]$File, [System.Collections.IDictionary]$Settings) {
    if (Test-Path $File) { return }
    $Settings.GetEnumerator() | ForEach-Object { "$($_.Key)=$($_.Value)" } |
        Set-Content $File -Encoding utf8
}

$php83 = (Resolve-Path '.local/php83/php.exe').Path
$php74 = (Resolve-Path '.local/php74/php.exe').Path
$mysql = (Resolve-Path '.local/mysql/*/bin/mysql.exe').Path
$mysqld = (Resolve-Path '.local/mysql/*/bin/mysqld.exe').Path
$composer = (Get-Command composer -ErrorAction Stop).Source
$npm = (Get-Command npm.cmd -ErrorAction Stop).Source
$env:Path = "$(Split-Path $php83);$env:Path"

foreach ($php in $php83, $php74) {
    $runtime = Split-Path $php
    $settings = @(
        ('extension_dir="' + (Join-Path $runtime 'ext') + '"'),
        'date.timezone=America/El_Salvador', 'short_open_tag=On',
        'display_errors=Off', 'log_errors=On', 'memory_limit=512M',
        'upload_max_filesize=100M', 'post_max_size=110M'
    )
    foreach ($extension in 'curl','fileinfo','gd','gd2','intl','mbstring','mysqli','openssl','pdo_mysql','sodium','zip','bcmath','exif') {
        if (Test-Path "$runtime/ext/php_$extension.dll") { $settings += "extension=php_$extension.dll" }
    }
    $settings | Set-Content "$runtime/php.ini" -Encoding utf8
    & $php -v
    Assert-Exit 'Verificar PHP'
}

$portal = 'launionsur.gob.sv/public_html'
$cms = 'cms.launionsur.cloud/public_html/app'
$website = "$portal/website"
foreach ($app in $cms, $website) {
    foreach ($suffix in 'bootstrap/cache','storage/app/public','storage/framework/cache/data','storage/framework/sessions','storage/framework/views','storage/logs') {
        New-Item -ItemType Directory -Force "$app/$suffix" | Out-Null
    }
}

foreach ($secret in 'app-password','mysql-root-password') {
    if (!(Test-Path ".local/$secret")) {
        [guid]::NewGuid().ToString('N') | Set-Content ".local/$secret"
    }
}
$appPassword = (Get-Content '.local/app-password' -Raw).Trim()
$rootPassword = (Get-Content '.local/mysql-root-password' -Raw).Trim()
$dbPort = if ($env:LAUNION_MYSQL_PORT) { [int]$env:LAUNION_MYSQL_PORT } else { 3307 }
$mysqlArguments = @('--protocol=TCP','-h127.0.0.1',"-P$dbPort",'-uroot','--connect-timeout=2')
$data = Join-Path $PSScriptRoot '.local/mysql-data'

if (!(Test-Path "$data/mysql")) {
    if (Get-NetTCPConnection -LocalPort $dbPort -State Listen -ErrorAction SilentlyContinue) {
        throw "El puerto $dbPort ya está ocupado. Detén la otra instancia antes de preparar este clon."
    }
    & $mysqld --no-defaults --initialize-insecure "--datadir=$data" --console
    Assert-Exit 'Inicializar la base de datos'
}

if (!(Get-NetTCPConnection -LocalPort $dbPort -State Listen -ErrorAction SilentlyContinue)) {
    $runner = Join-Path $PSScriptRoot 'servidor-local.ps1'
    Start-Process -FilePath (Get-Process -Id $PID).Path -ArgumentList @(
        '-NoProfile', '-File', ('"' + $runner + '"'), '-Sitio', 'mysql'
    ) -WindowStyle Hidden -RedirectStandardOutput '.local/mysql-setup.out' -RedirectStandardError '.local/mysql-setup.err'
}

if (!(Test-Path '.local/mysql-preparado')) {
    $connected = $false
    for ($attempt = 0; $attempt -lt 15; $attempt++) {
        foreach ($password in $rootPassword, '') {
            $env:MYSQL_PWD = $password
            & $mysql @mysqlArguments --raw -N -e 'SELECT @@datadir' 2>$null | ForEach-Object {
                if ($_.TrimEnd('\', '/').Replace('\','/') -ieq $data.Replace('\','/')) { $connected = $true }
            }
            if ($connected) { break }
        }
        if ($connected) { break }
        Start-Sleep -Seconds 1
    }
    if (!$connected) { throw 'MySQL no respondió con el directorio de datos de este clon. Revisa .local/mysql-setup.err.' }

    try {
        @"
CREATE DATABASE IF NOT EXISTS Dev_a1c4lu5v_alcaldia CHARACTER SET utf8mb4;
CREATE DATABASE IF NOT EXISTS Dev_cmslusur CHARACTER SET utf8mb4;
CREATE USER IF NOT EXISTS 'union_local'@'localhost' IDENTIFIED BY '$appPassword';
ALTER USER 'union_local'@'localhost' IDENTIFIED BY '$appPassword';
GRANT ALL ON Dev_a1c4lu5v_alcaldia.* TO 'union_local'@'localhost';
GRANT ALL ON Dev_cmslusur.* TO 'union_local'@'localhost';
"@ | & $mysql @mysqlArguments
        Assert-Exit 'Crear las bases y su usuario'
        foreach ($file in 'esquema-legado.sql','datos-legado.sql') {
            $path = (Resolve-Path "dev/$file").Path.Replace('\','/')
            & $mysql @mysqlArguments Dev_a1c4lu5v_alcaldia -e "source $path"
            Assert-Exit "Importar $file"
        }
        "ALTER USER 'root'@'localhost' IDENTIFIED BY '$rootPassword';" | & $mysql @mysqlArguments
        Assert-Exit 'Configurar la cuenta administrativa local'
        New-Item -ItemType File '.local/mysql-preparado' | Out-Null
    } finally {
        Remove-Item Env:MYSQL_PWD -ErrorAction SilentlyContinue
    }
}

Write-EnvFile "$portal/.env" ([ordered]@{
    DB_HOST = '127.0.0.1'; DB_PORT = $dbPort; DB_NAME = 'Dev_a1c4lu5v_alcaldia'
    DB_USER = 'union_local'; DB_PASS = $appPassword
})

$cmsStorage = (Resolve-Path "$cms/storage/app/public").Path.Replace('\','/')
$publicStorage = "$cms/public/storage"
if (!(Test-Path $publicStorage)) {
    New-Item -ItemType Junction -Path $publicStorage -Target $cmsStorage | Out-Null
}

foreach ($app in $portal, $cms, $website) {
    Push-Location $app
    try {
        if ($app -ne $portal) {
            $port = if ($app -eq $cms) { 8082 } else { 8088 }
            $key = [Convert]::ToBase64String([Security.Cryptography.RandomNumberGenerator]::GetBytes(32))
            Write-EnvFile '.env' ([ordered]@{
                APP_NAME = 'LaUnionSur'; APP_ENV = 'local'; APP_DEBUG = 'false'; APP_KEY = "base64:$key"
                APP_URL = "http://127.0.0.1:$port"; APP_LOCALE = 'es'; APP_FALLBACK_LOCALE = 'en'
                DB_CONNECTION = 'mysql'; DB_HOST = '127.0.0.1'; DB_PORT = $dbPort
                DB_DATABASE = 'Dev_cmslusur'; DB_USERNAME = 'union_local'; DB_PASSWORD = $appPassword
                SESSION_DRIVER = 'file'; SESSION_COOKIE = "launion_${port}_session"; CACHE_STORE = 'file'
                QUEUE_CONNECTION = 'sync'; MAIL_MAILER = 'log'; FILESYSTEM_DISK = 'public'
                CMS_PUBLIC_ROOT = ('"' + $cmsStorage + '"'); CMS_MEDIA_URL = 'http://127.0.0.1:8082/storage'
            })
        }
        & $composer install --no-interaction --prefer-dist
        Assert-Exit "Instalar Composer en $app"
        if ($app -eq $cms) {
            & $php83 artisan migrate --force
            Assert-Exit 'Migrar CMS'
            foreach ($seeder in 'RoleSeeder','SuperAdminSeeder','LocalDemoBlockSeeder') {
                & $php83 artisan db:seed "--class=$seeder" --force
                Assert-Exit "Ejecutar $seeder"
            }
        }
        if ($app -eq $website) {
            & $php83 artisan migrate --path=database/migrations/2026_04_11_000001_add_featured_to_services.php --force
            Assert-Exit 'Migrar los campos de servicios usados por la vista pública'
        }
        if ($app -ne $portal) {
            & $npm ci
            Assert-Exit "Instalar npm en $app"
            & $npm run build
            Assert-Exit "Compilar recursos en $app"
        }
    } finally {
        Pop-Location
    }
}

Write-Output 'Preparación terminada. Ejecuta .\iniciar-la-union.ps1'
