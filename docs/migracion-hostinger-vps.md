# Migrar el sitio de IIS a un VPS de Hostinger

El sitio es Laravel 12 (PHP 8.3, Nginx, MariaDB). En IIS vivía en `C:\inetpub\wwwroot\smartech` y salía por Cloudflare hacia `https://smarttechsecurity.com.co`. Esta guía deja el mismo dominio en un VPS Ubuntu 24.04.

No hace falta cambiar código de la aplicación para que Nginx la sirva. `bootstrap/app.php` ya confía en los encabezados de Cloudflare, y `APP_URL` con `https://` fuerza el esquema correcto.

## Qué hay que llevar del servidor Windows

Git no trae el `.env` ni los archivos que el panel fue guardando en producción.

| Qué | Dónde está en IIS | Por qué |
|-----|-------------------|---------|
| `APP_KEY` | `.env` | Cifra el documento de los empleados y la clave del certificado DIAN. Si cambia, esos datos no se pueden leer. |
| Base de datos | MySQL de ese servidor | Usuarios del panel, cotizaciones, facturas, proyectos, configuración DIAN. |
| `storage\app\private\` | disco `local` | Certificado `.p12`, XML de facturas, fotos y firmas de empleados. |
| Fotos subidas por el panel | `public\images\projects\`, `public\images\services\`, `public\images\portadas\` y `public\images\clients\` | El repo tiene el diseño base; las cargas de Filament solo están en el disco de IIS. |

`public\web.config` no se copia. En Linux lo reemplaza `deploy/hostinger/nginx-smartech.conf`.

## 1. VPS en Hostinger

1. En hPanel, crea un VPS con **Ubuntu 24.04** (sistema limpio, sin plantilla de panel).
2. Anota la IPv4 pública y entra por SSH como `root`.
3. Si el plan tiene 1 GB de RAM, crea swap antes de compilar los assets. Composer y `npm run build` se quedan sin memoria si no:

```bash
fallocate -l 2G /swapfile
chmod 600 /swapfile
mkswap /swapfile
swapon /swapfile
echo '/swapfile none swap sw 0 0' >> /etc/fstab
```

4. Abre SSH, HTTP y HTTPS:

```bash
apt update
apt install -y ufw
ufw allow OpenSSH
ufw allow 'Nginx Full'
ufw --force enable
```

## 2. Instalar Nginx, PHP 8.3, MariaDB y Node

Ubuntu 24.04 ya trae PHP 8.3. Node del sistema se queda corto para Vite 7: hace falta Node 22.

```bash
apt install -y nginx mariadb-server git unzip curl ca-certificates \
  php8.3-fpm php8.3-cli php8.3-mysql php8.3-mbstring php8.3-xml php8.3-curl \
  php8.3-zip php8.3-gd php8.3-intl php8.3-bcmath php8.3-soap php8.3-readline \
  php8.3-opcache

curl -fsSL https://deb.nodesource.com/setup_22.x | bash -
apt install -y nodejs

curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
```

Comprueba `php -v` (8.3) y `node -v` (22).

## 3. Base de datos

En el VPS:

```bash
mysql
```

```sql
CREATE DATABASE db_smart_tech_security CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'smartech'@'localhost' IDENTIFIED BY 'una-clave-larga';
GRANT ALL PRIVILEGES ON db_smart_tech_security.* TO 'smartech'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

En el servidor IIS, con el MySQL de producción (ajusta usuario y ruta de `mysqldump`):

En este IIS el servicio es MySQL 8.0, la base es `db_smart_tech_security`, el usuario es `root` y no tiene contraseña. El programa está en `C:\Program Files\MySQL\MySQL Server 8.0\bin\mysqldump.exe`. No pongas `-p`: sin eso MySQL se queda esperando una clave.

```powershell
& "C:\Program Files\MySQL\MySQL Server 8.0\bin\mysqldump.exe" --host=127.0.0.1 --port=3306 --user=root --default-character-set=utf8mb4 --single-transaction --routines --triggers --result-file=C:\temp\smartech-migracion\smartech.sql db_smart_tech_security
```

Súbelo e impórtalo. No ejecutes `migrate:fresh` ni un seed completo: borrarían cotizaciones y usuarios.

```bash
scp C:\temp\smartech.sql root@IP_DEL_VPS:/root/smartech.sql
mysql -u smartech -p db_smart_tech_security < /root/smartech.sql
```

El usuario administrador es el que ya existe en la tabla `users`. No hace falta `make:filament-user` si el volcado entró bien.

## 4. Código

```bash
mkdir -p /var/www
git clone https://github.com/pipelink1205-coder/smartech-security.git /var/www/smartech
cd /var/www/smartech
cp .env.example .env
```

Edita `/var/www/smartech/.env`. Lo mínimo respecto al ejemplo local:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://smarttechsecurity.com.co

# La misma cadena que en el .env de IIS. No generes otra.
APP_KEY=base64:...

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_smart_tech_security
DB_USERNAME=smartech
DB_PASSWORD=una-clave-larga

# sync envía el correo en la misma petición (como un IIS sin worker).
# database exige la unidad systemd del paso 7.
QUEUE_CONNECTION=sync

SESSION_DRIVER=database
CACHE_STORE=database
```

Copia el resto de correo, WhatsApp, DIAN y datos bancarios desde el `.env` de IIS. Deja `DIAN_ENABLED` como esté hoy.

Trae los archivos que no están en Git. En el IIS, en PowerShell, el prompt tiene que ser `PS C:\...>`. Si aparece `>>`, pulsa Ctrl+C: esa línea todavía no se ejecutó.

Crea la carpeta y comprueba que existe:

```powershell
New-Item -ItemType Directory -Force -Path C:\temp\smartech-migracion
Test-Path C:\temp\smartech-migracion
```

Tiene que imprimir `True`. En PowerShell pega **una línea**, pulsa Enter y espera a que vuelva `PS C:\...>`. Si aparece `>>`, pulsa Ctrl+C y no pegues el bloque entero.

```powershell
cd C:\inetpub\wwwroot\smartech
Copy-Item -Force .env C:\temp\smartech-migracion\.env
tar -a -c -f C:\temp\smartech-migracion\private.zip -C storage\app private
tar -a -c -f C:\temp\smartech-migracion\imagenes.zip -C public\images projects services portadas clients
```

Si el último `tar` dice que falta una carpeta, repítelo nombrando solo las que existan en `public\images` (`projects`, `services`, `portadas`, `clients`).

`mysqldump` no está en el PATH de IIS. Localízalo y lee el nombre de la base sin mostrar la clave:

```powershell
Get-ChildItem -Path 'C:\Program Files','C:\Program Files (x86)','C:\laragon','C:\xampp' -Filter mysqldump.exe -Recurse -ErrorAction SilentlyContinue | Select-Object -ExpandProperty FullName
Select-String -Path .env -Pattern '^DB_(HOST|PORT|DATABASE|USERNAME)='
```

El volcado lo escribe el propio `mysqldump` (`--result-file`). En PowerShell, redirigir con `>` guarda el SQL en UTF-16 y luego no importa en Linux.

```powershell
& 'C:\RUTA\mysqldump.exe' -u USUARIO -p --single-transaction --routines --triggers --result-file=C:\temp\smartech-migracion\smartech.sql NOMBRE_BD
```

`USUARIO` y `NOMBRE_BD` salen de `DB_USERNAME` y `DB_DATABASE` en ese `.env`. El sitio puede seguir recibiendo visitas: este paquete es una foto del momento. Justo antes de cambiar el DNS se repite el volcado para no perder cotizaciones nuevas.

En el VPS:

```bash
unzip -o private.zip -d /var/www/smartech/storage/app/
unzip -o imagenes.zip -d /var/www/smartech/public/images/
```

`storage/app/private/` debe quedar con `dian/certs`, fotos de empleados y XML. No subas esa carpeta a Git.

Límites de subida (el fallo `Path cannot be empty` de IIS era la carpeta temporal de PHP):

```bash
cp deploy/hostinger/php-smartech.ini /etc/php/8.3/mods-available/smartech.ini
phpenmod smartech
```

Con el volcado ya importado, primer arranque y cada deploy posterior:

```bash
bash deploy.sh
```

Ese script hace `git pull`, `composer install --no-dev`, `npm ci && npm run build`, migraciones, los seeders que ya usa IIS (coordenadas del mapa y textos de servicios, sin pisar fotos), permisos de `www-data` y la caché de producción.

## 5. Nginx y HTTPS

```bash
cp deploy/hostinger/nginx-smartech.conf /etc/nginx/sites-available/smartech
ln -sfn /etc/nginx/sites-available/smartech /etc/nginx/sites-enabled/smartech
rm -f /etc/nginx/sites-enabled/default
nginx -t
systemctl reload nginx
```

Prueba el PHP antes de mover el dominio. En tu PC, o con `curl` en el propio VPS:

```bash
curl -I --resolve smarttechsecurity.com.co:80:127.0.0.1 http://smarttechsecurity.com.co/up
```

Tiene que responder 200. `/up` es el health check de Laravel.

Certificado (Let's Encrypt). Si Cloudflare está en naranja, pon el registro en **solo DNS** unos minutos, o el reto HTTP puede fallar:

```bash
apt install -y certbot python3-certbot-nginx
certbot --nginx -d smarttechsecurity.com.co -d www.smarttechsecurity.com.co
```

Cuando el dominio ya apunte a este VPS y Cloudflare vuelva a estar en proxy, instala las IP reales de Cloudflare para que los registros y el mapa no vean la IP del proxy:

```bash
bash deploy/hostinger/install-cloudflare-realip.sh
```

En Cloudflare deja SSL en **Full (strict)** y el mismo `APP_URL` que usa la gente (con o sin `www`, pero uno solo). Si el navegador entra por el otro host, Livewire rechaza la subida con 401.

## 6. Cola y cron

Con `QUEUE_CONNECTION=sync` no actives el worker. Si pasas la cola a `database`:

```bash
cp deploy/hostinger/smartech-queue.service /etc/systemd/system/
systemctl daemon-reload
systemctl enable --now smartech-queue
```

Hoy no hay tareas programadas. Deja el cron de Laravel para cuando existan:

```bash
crontab -u www-data -e
```

```cron
* * * * * cd /var/www/smartech && php artisan schedule:run >> /dev/null 2>&1
```

## 7. Cortar el dominio

1. En IIS, pon el sitio en solo lectura o saca el sitio del balance un rato: un lead guardado después del volcado no viaja solo.
2. Repite el `mysqldump` y vuelve a importarlo si hubo movimientos desde el paso 3. Copia otra vez `storage\app\private` y las fotos nuevas.
3. En Cloudflare (o en el DNS de Hostinger, si no usas Cloudflare) cambia el A de `@` y de `www` a la IPv4 del VPS. Baja el TTL a 300 antes, si puedes.
4. En el VPS: `bash deploy.sh`.
5. Cuando `https://smarttechsecurity.com.co/up` responda desde el VPS, apaga el sitio en IIS. No dejes los dos escribiendo la misma marca.

## 8. Comprobar

- Inicio, una página de servicio y el mapa: `https://smarttechsecurity.com.co/mapa/comunas.geojson`.
- Login en `/admin` con el usuario que ya existía. Si la clave no entra, el volcado no es el de producción.
- Sube una foto de proyecto. Si responde 401, `APP_URL` no coincide con el host del navegador. Si responde 500 con ruta vacía, revisa `upload_tmp_dir` y que `www-data` escriba en `storage/app/upload-tmp`.
- Abre un empleado y confirma que el documento se ve. Si Laravel dice que no puede descifrar, el `APP_KEY` no es el de IIS.
- Genera un PDF de cotización.
- Envía un correo de prueba desde el panel o una cotización de prueba.
- En configuración DIAN, el certificado debe seguir marcado como presente (archivo en `storage/app/private/dian/certs`).

Logs:

```bash
tail -f /var/www/smartech/storage/logs/laravel.log
journalctl -u php8.3-fpm -u nginx -u smartech-queue -f
```

## Deploys siguientes

En el VPS, desde `/var/www/smartech`:

```bash
bash deploy.sh
```

El script actualiza textos de servicios desde `database/seeders/data/services.php` y no reemplaza `image`, `is_active` ni `show_on_home`.

## Problemas frecuentes

| Síntoma | Qué revisar |
|---------|-------------|
| 502 en todo el sitio | `systemctl status php8.3-fpm`. El socket de Nginx debe ser `/run/php/php8.3-fpm.sock`. |
| CSS sin estilos | `npm run build` no corrió. `public/build` no está en Git. |
| 401 al subir en el admin | `APP_URL` exacto, `php artisan config:cache` después de editar `.env`, Cloudflare en Full (strict). |
| Permiso denegado al guardar imagen | `storage/` y `public/images/projects` pertenecen a `www-data`. Vuelve a correr `deploy.sh`. |
| Correos quietos | `QUEUE_CONNECTION=database` sin el servicio `smartech-queue`, o la clave de aplicación de Gmail. |
| `composer` pide otra versión de PHP | Tiene que ser 8.3 o 8.4. En IIS se forzaba 8.5 con `--ignore-platform-req`; aquí no hace falta. |
