@echo off
setlocal

rem Produccion IIS: C:\inetpub\wwwroot\smartech
rem Ejecutar como Administrador o con permisos de escritura en la carpeta.

echo === Smart Tech Security - deploy IIS ===
echo Ruta: %~dp0
cd /d "%~dp0"

if /i not "%~dp0"=="C:\inetpub\wwwroot\smartech\" (
    echo AVISO: este script esta pensado para C:\inetpub\wwwroot\smartech
)

rem Preservar web.config del servidor (no sobrescribir con git)
if exist "public\web.config" (
    echo Respaldo web.config del servidor...
    copy /Y "public\web.config" "public\web.config.server" >nul
)

echo [1/8] git pull...
git pull
if errorlevel 1 exit /b 1

if exist "public\web.config.server" (
    echo Restaurando web.config del servidor...
    copy /Y "public\web.config.server" "public\web.config" >nul
    del "public\web.config.server"
)

echo [2/8] composer install...
composer install --no-dev --optimize-autoloader --no-interaction
if errorlevel 1 (
    echo AVISO: composer fallo ^(revisar ext-intl y ext-zip en php.ini^).
    echo Continuando si vendor ya existia...
)

echo [3/8] coordenadas del mapa (si faltan)...
php artisan db:seed --class=ProjectMapCoordinatesSeeder --no-interaction --force 2>nul

echo [4/8] migraciones...
php artisan migrate --force --no-interaction

echo [5/8] servicios (updateOrCreate por slug, no duplica)...
php artisan db:seed --class=ServiceSeeder --no-interaction --force

echo [6/8] storage link...
php artisan storage:link 2>nul

echo [7/8] limpiar cache...
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

echo [8/8] verificar archivos del mapa...
if not exist "public\data\comunas-medellin.geojson" (
    echo ERROR: falta public\data\comunas-medellin.geojson
    exit /b 1
)
if not exist "public\web.config" (
    echo AVISO: falta public\web.config en IIS
)

echo.
echo Listo.
echo Probar:
echo   https://smarttechsecurity.com.co/mapa/comunas.geojson
echo   https://smarttechsecurity.com.co/ ^(seccion Proyectos^)
endlocal
