#!/usr/bin/env bash
# Produccion en VPS: /var/www/smartech (Nginx + PHP-FPM).
# Uso: sudo bash deploy.sh
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$ROOT"

APP_USER="${APP_USER:-www-data}"
PHP_BIN="${PHP_BIN:-php}"

if [[ "$(id -u)" -ne 0 ]]; then
  echo "Ejecuta como root: sudo bash deploy.sh"
  exit 1
fi

echo "=== Smart Tech Security - deploy VPS ==="
echo "Ruta: $ROOT"

if [[ ! -f .env ]]; then
  echo "ERROR: falta .env. Completa produccion (APP_KEY del IIS, base de datos, correo) antes de desplegar."
  exit 1
fi

if ! git config --global --get-all safe.directory 2>/dev/null | grep -qx "$ROOT"; then
  git config --global --add safe.directory "$ROOT"
fi

echo "[1/8] git pull..."
git pull --ff-only

echo "[2/8] composer install..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "[3/8] assets (Vite)..."
if ! command -v npm >/dev/null 2>&1; then
  echo "ERROR: npm no esta instalado. public/build no viaja en git."
  exit 1
fi
npm ci
npm run build

echo "[4/8] carpetas de escritura..."
mkdir -p \
  storage/app/upload-tmp \
  storage/app/private/livewire-tmp \
  storage/app/private/dian/certs \
  storage/framework/cache/data \
  storage/framework/sessions \
  storage/framework/views \
  storage/logs \
  bootstrap/cache \
  public/images/projects \
  public/images/services

echo "[5/8] migraciones y datos..."
"$PHP_BIN" artisan migrate --force --no-interaction
"$PHP_BIN" artisan db:seed --class=ProjectMapCoordinatesSeeder --force --no-interaction
"$PHP_BIN" artisan db:seed --class=ServiceSeeder --force --no-interaction

echo "[6/8] storage link..."
"$PHP_BIN" artisan storage:link || true

echo "[7/8] permisos..."
chown -R "$APP_USER:$APP_USER" \
  storage \
  bootstrap/cache \
  public/images/projects \
  public/images/services
if [[ -L public/storage ]]; then
  chown -h "$APP_USER:$APP_USER" public/storage || true
fi
find storage bootstrap/cache -type d -exec chmod 775 {} \;
find storage bootstrap/cache -type f -exec chmod 664 {} \;

echo "[8/8] cache de produccion..."
"$PHP_BIN" artisan config:cache
"$PHP_BIN" artisan route:cache
"$PHP_BIN" artisan view:cache
"$PHP_BIN" artisan event:cache
"$PHP_BIN" artisan queue:restart || true

for svc in php8.4-fpm php8.3-fpm php8.2-fpm; do
  if systemctl is-active --quiet "$svc"; then
    systemctl reload "$svc"
    break
  fi
done

if systemctl is-enabled --quiet smartech-queue 2>/dev/null; then
  systemctl restart smartech-queue
fi

if [[ ! -f public/data/comunas-medellin.geojson ]]; then
  echo "ERROR: falta public/data/comunas-medellin.geojson"
  exit 1
fi

echo
echo "Listo."
echo "Probar:"
echo "  https://smarttechsecurity.com.co/up"
echo "  https://smarttechsecurity.com.co/mapa/comunas.geojson"
echo "  https://smarttechsecurity.com.co/admin"
