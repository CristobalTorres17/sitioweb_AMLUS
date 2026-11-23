# Portal de La Unión Sur

Entorno de desarrollo del portal municipal y sus paneles de administración. La instalación local usa Windows, dos versiones de PHP y MySQL.

| Aplicación | URL local | Código principal |
| --- | --- | --- |
| Portal PHP | http://127.0.0.1:8084/ | `launionsur.gob.sv/public_html` |
| Panel administrativo de noticias | http://127.0.0.1:8083/admin/ | `sistema.launionsur.cloud/public_html/admin` |
| Panel CMS Laravel | http://127.0.0.1:8082/login | `cms.launionsur.cloud/public_html/app` |
| Vista pública Laravel del CMS | http://127.0.0.1:8088/ | `launionsur.gob.sv/public_html/website` |

El portal PHP lee las noticias que el panel antiguo guarda en la tabla `eventos`. Las fotos se sirven desde el panel. El CMS y su vista pública Laravel comparten otra base de datos; los bloques de portada del CMS se revisan en el puerto 8088.

## Requisitos en Windows x64

| Componente | Instalación |
| --- | --- |
| Git y PowerShell 7.4 o superior | Instalar y comprobar `git --version` y `pwsh --version`. [Git](https://git-scm.com/download/win) · [PowerShell](https://learn.microsoft.com/powershell/scripting/install/install-powershell-on-windows) |
| Visual C++ Redistributable v14 x64 | Instalar el paquete de Microsoft. [Descarga](https://learn.microsoft.com/cpp/windows/latest-supported-vc-redist) |
| PHP 8.3 NTS x64 | Extraer el ZIP en `.local/php83`. Ahí debe quedar `php.exe`. [Descarga](https://www.php.net/~windows/releases/latest/) |
| PHP 7.4.33 NTS x64 | Extraer `php-7.4.33-nts-Win32-vc15-x64.zip` en `.local/php74`. El panel antiguo queda en la interfaz local. [Archivo oficial](https://www.php.net/~windows/releases/) |
| MySQL Community Server 8.0 x64 | Extraer el ZIP en `.local/mysql`, conservando la carpeta del paquete. Deben quedar `.local/mysql/mysql-8.0.x-winx64/bin/mysql.exe` y `mysqld.exe`. [Descarga](https://dev.mysql.com/downloads/mysql/8.0.html) |
| Composer 2 y Node.js 22 LTS con npm | Instalar y comprobar `composer --version`, `node --version` y `npm.cmd --version`. Para Vite usa Node 22.12 o posterior dentro de la serie 22. [Composer](https://getcomposer.org/download/) · [Node.js](https://nodejs.org/en/download) |

Los sitios usan el servidor de desarrollo de PHP. MySQL escucha en `127.0.0.1:3307`. Los puertos `3307`, `8082`, `8083`, `8084` y `8088` deben estar disponibles para el clon. Si ya hay una copia de este mismo proyecto en ejecución, detenla antes de preparar otra.

## Clonar e instalar

Acepta la invitación al repositorio del equipo en GitHub.

```powershell
git clone https://github.com/CristobalTorres17/sitioweb_AMLUS.git
cd sitioweb_AMLUS
New-Item -ItemType Directory -Force .local
```

Extrae los ZIP de PHP y MySQL en las rutas de la tabla. Desde PowerShell 7 ejecuta:

```powershell
.\preparar-la-union.ps1
.\iniciar-la-union.ps1
```

El primer script crea las bases locales, genera credenciales y archivos `.env`, instala las dependencias de Composer, migra el CMS, carga datos de ejemplo y compila ambos frontends Laravel. También crea un enlace de directorio para los archivos públicos del CMS. La instalación necesita acceso a los registros de Composer y npm. En arranques posteriores ejecuta `iniciar-la-union.ps1`.

## Cuentas y prueba inicial

Estas cuentas pertenecen a las bases ficticias que genera el instalador.

| Panel | Usuario | Contraseña local |
| --- | --- | --- |
| Panel de noticias | `practicante` | `unionlocal123!` |
| CMS Laravel | `admin@example.com` | `password` |

1. Abre el portal en el puerto 8084. Comprueba que “Noticia de prueba La Unión Sur” tenga foto y que su enlace abra el detalle.
2. En el panel antiguo, entra a Eventos y crea una noticia con una imagen local. Recarga el portal y abre la nueva noticia.
3. En el CMS, abre el bloque “Bienvenida local” y cambia su texto. Comprueba el nuevo texto en la vista pública del puerto 8088. Para comprobar los bloques de portada directamente, abre `http://127.0.0.1:8082/api/cms-blocks/home`.

La base antigua incluye el esquema de los módulos del panel y pocos registros ficticios. El archivo documental y las galerías de producción requieren archivos de prueba propios para trabajar en esos módulos.

## Dónde programar

| Trabajo | Rutas |
| --- | --- |
| Portada, noticias y trámites en PHP | `launionsur.gob.sv/public_html/index.php`, `blog.php`, `blog-details.php`, `short-circuit-repair.php`, `parts/` y `assets/` |
| Formularios y consultas del panel antiguo | `sistema.launionsur.cloud/public_html/admin/eventos/` y `admin/libreria_php/` |
| Backend del CMS | Dentro de `cms.launionsur.cloud/public_html/app`, las carpetas `app/`, `routes/` y `database/migrations/` |
| Frontend del CMS | Dentro del mismo proyecto, `resources/` |
| Vista pública Laravel | Dentro de `launionsur.gob.sv/public_html/website`, las carpetas `app/`, `routes/` y `resources/` |

Después de cambiar recursos de Laravel, ejecuta `npm.cmd run build` desde la aplicación que modificaste y recarga la página. Para incorporar cambios de esquema del CMS, usa `artisan migrate` desde el CMS con `.local/php83/php.exe`. Los cambios de PHP y de plantillas del portal antiguo se prueban recargando la página.

## Flujo de trabajo en GitHub

```powershell
git switch main
git pull --ff-only
git switch -c fix/mi-tarea
# Edita y prueba el cambio en local.
git status --short
git add -- ruta/al/archivo
git diff --cached --check
git commit -m "Describe el cambio"
git push -u origin fix/mi-tarea
```

Abre un *pull request* a `main` con la descripción, los pasos de prueba y capturas cuando cambie la interfaz. Versiona el código, las migraciones y los archivos de bloqueo. Mantén los respaldos, datos reales, archivos subidos, credenciales y dependencias instaladas fuera del repositorio.

El responsable del repositorio puede invitar a cada practicante desde la configuración de acceso. [Invitar colaboradores](https://docs.github.com/en/repositories/managing-your-repositorys-settings-and-features/repository-access-and-collaboration/inviting-collaborators-to-a-personal-repository)

## Diagnóstico

- Si falla la instalación, revisa primero el mensaje del script y `.local/mysql-setup.err`. Para el arranque, revisa `.local/*-arranque.err`.
- Si Composer detecta PHP 5.6 de XAMPP, ejecuta la instalación desde el script, que agrega PHP 8.3 al `PATH` del proceso.
- Si falta `public/build/manifest.json`, entra a la aplicación Laravel correspondiente y ejecuta `npm.cmd ci` y `npm.cmd run build`.
- Si hay un error de puerto ocupado, identifica el proceso con `Get-NetTCPConnection -LocalPort 3307,8082,8083,8084,8088 -State Listen`. Detén únicamente la instancia que reconoces antes de volver a arrancar.
- Si un cambio de configuración de Laravel no se refleja, ejecuta `artisan config:clear` con PHP 8.3 desde la aplicación correspondiente.
