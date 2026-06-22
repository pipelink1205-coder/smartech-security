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

echo [1/6] git pull...
git pull
if errorlevel 1 exit /b 1

echo [2/6] composer install...
composer install --no-dev --optimize-autoloader --no-interaction
if errorlevel 1 exit /b 1

echo [3/6] migraciones...
php artisan migrate --force --no-interaction

echo [4/6] storage link...
php artisan storage:link 2>nul

echo [5/6] limpiar cache...
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

echo [6/6] verificar archivos del mapa...
if not exist "public\data\comunas-medellin.geojson" (
    echo ERROR: falta public\data\comunas-medellin.geojson
    exit /b 1
)
if not exist "public\web.config" (
    echo ERROR: falta public\web.config ^(requerido en IIS^)
    exit /b 1
)

echo.
echo Listo.
echo Probar en el navegador:
echo   https://TU-DOMINIO/mapa/comunas.geojson
echo   https://TU-DOMINIO/ ^(seccion Proyectos^)
endlocal
