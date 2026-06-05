# Panel de administración — Smart Tech Security

Guía para editar el contenido del sitio sin tocar código ni MySQL directamente.

---

## Acceso

| Concepto | Valor |
|----------|--------|
| URL local | `http://127.0.0.1:8000/admin` (o el puerto de `php artisan serve`) |
| URL Laragon | `http://smart-tech-security.test/admin` (si el virtual host apunta a este proyecto) |
| Login | `http://…/admin/login` |

Solo los usuarios de la tabla `users` que implementan acceso al panel pueden entrar. En producción **no** dejes usuarios de prueba con contraseñas débiles.

### Crear o recuperar un usuario administrador

Desde la raíz del proyecto:

```bash
php artisan make:filament-user
```

Te pedirá nombre, correo y contraseña (mínimo 8 caracteres). También puedes usar:

```bash
php artisan make:filament-user --name="Tu nombre" --email="tu@correo.com" --password="TuClaveSegura" --no-interaction
```

Gestión adicional de usuarios: menú **Usuarios** dentro del panel.

---

## Requisito PHP: extensión `intl`

Filament y Laravel usan `intl` para formatear números y monedas. Es una extensión del **PHP que ejecuta la app**, no un archivo del proyecto.

### Laragon + `php artisan serve` (caso habitual en Windows)

A veces Laragon tiene `intl` activo, pero la terminal usa **otro PHP** (por ejemplo XAMPP) porque aparece primero en el PATH.

Comprueba cuál PHP usas:

```bash
where php
php --ini
php -m | findstr intl
```

Si `Loaded Configuration File` apunta a `C:\xampp\php\php.ini` y `intl` no sale en `php -m`:

1. Edita ese `php.ini` (no solo el de Laragon).
2. Busca `;extension=intl` y déjalo como `extension=intl` (sin `;`).
3. Guarda, **detén** `php artisan serve` (Ctrl+C) y vuelve a ejecutarlo.

**Alternativa:** abre la terminal desde Laragon (Menú → Terminal) para que use el PHP de `C:\laragon\bin\php\...`, donde `intl` suele venir ya habilitado.

En **producción** debes habilitar `intl` en el PHP del hosting (otro `php.ini`, independiente de tu PC).

### Error: `Unknown database 'db_smart_tech_security'`

La base de datos del `.env` aún no existe en MySQL. En Laragon (terminal):

```sql
CREATE DATABASE IF NOT EXISTS db_smart_tech_security CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Luego en el proyecto:

```bash
php artisan migrate --seed
php artisan make:filament-user
```

(`SESSION_DRIVER=database` requiere las tablas de sesión que crean las migraciones.)

---

## Menú del panel (orden)

| Orden | Sección | Para qué sirve |
|-------|---------|----------------|
| 1 | **Usuarios** | Quién puede iniciar sesión en `/admin` |
| 2 | **Proyectos** | Trabajos realizados, mapa interactivo y galería de fotos |
| 3 | **Servicios** | Tarjetas de la sección de servicios en el sitio |
| 4 | **Cotizaciones** | Solicitudes enviadas desde el cotizador del sitio público |

El número rojo en **Cotizaciones** indica cuántas llevan estado **Nuevo**.

---

## Proyectos (trabajos + mapa)

### Datos generales

- **Título** y **identificador (slug)**: si dejas el slug vacío, se genera del título.
- **Categoría / servicio**: aparece en la tarjeta del portafolio (ej. *Seguridad Corporativa*, *Domótica*).
- **Descripción**: texto del panel lateral al abrir el proyecto.
- **Zona / ciudad**, **Año**, **Destacado**: controlan listado y visibilidad en inicio.
- **Imagen de portada**: opcional; si hay galería, la **primera foto** (menor orden) suele usarse como portada.

### Ubicación en el mapa (Medellín)

| Campo | Uso |
|-------|-----|
| **Dirección** | Texto visible para el equipo |
| **Latitud / Longitud** | Pin en el mapa (copiar desde Google Maps → clic derecho en el lugar) |
| **Comuna (1–16)** | Resalta la comuna en el mapa de Medellín al hacer clic |

Proyectos fuera de Medellín (Envigado, Bello, Itagüí, etc.): deja **Comuna** vacía; el pin igual aparece si hay latitud y longitud.

### Fotos del proyecto (galería en el sitio)

Hay **dos lugares** en el formulario; no es lo mismo que “una sola portada”:

| Bloque | Cuándo usarlo |
|--------|----------------|
| **Fotos del proyecto** (sección con varias imágenes) | Subes **todas** las evidencias. La **primera** de la lista es la portada en la web. |
| **Galería de fotos** (pestaña, solo al **editar**) | Gestionar fotos ya guardadas: orden (arrastrar), pie de foto, borrar o agregar una a una. |

Al **crear** un proyecto solo ves la sección de subida múltiple; al guardar, entras a editar y aparece la pestaña **Galería de fotos**.

Archivos en: `public/images/projects/`.

### Mapa / geolocalizador en el panel

En **Ubicación en el mapa del sitio**:

1. Escribe la **dirección** y pulsa **Buscar dirección en el mapa** (OpenStreetMap).
2. O haz **clic en el mapa** / **arrastra el pin** — se rellenan latitud y longitud solos.
3. **Comuna (1–16)**: solo trabajos en Medellín; fuera de la ciudad déjala vacía.

Más detalle técnico: `public/images/projects/LEEME.txt`.

---

## Servicios

- **Nombre**, **slug**, **icono (emoji)**, **frase destacada**, **descripción**.
- **Características**: lista con etiquetas (Enter tras cada ítem).
- **Imagen**: se guarda en `public/images/services/`.
- **Precio desde**: referencia en COP (el cotizador usa `config/quotes.php` para rangos reales).
- **Orden**: posición en la página (menor = más arriba).
- **Visible en el sitio**: desactiva sin borrar el registro.

---

## Cotizaciones

Las cotizaciones **las crea el visitante** en el sitio; en el panel no hay botón “Crear”.

Puedes:

- Cambiar **estado** (Nuevo → Contactado → Cotizado → Cerrado / Perdido).
- Añadir **notas internas** y rango de precio manual.
- Usar **Abrir WhatsApp** en la ficha para responder al cliente.

Los datos del cliente (nombre, teléfono, mensaje, etc.) son solo lectura en el formulario.

---

## Contenido que NO está en el panel (por diseño)

### Logos “Empresas que confían en nosotros”

Siguen siendo **archivos en carpetas**:

- `public/images/clients/`
- `public/images/projects/Logos empresas servicios/`

Añade o quita imágenes (PNG/JPG/SVG/WebP) y recarga el sitio; el carrusel las detecta solo.

### Precios del cotizador en tiempo real

Siguen en `config/quotes.php` (servicio + recargo por zona). Cambios requieren editar ese archivo o desplegar de nuevo.

### Contacto WhatsApp / teléfono del sitio

`config/contact.php` y variables en `.env` según esté configurado el proyecto.

### Imágenes fijas del diseño (hero, IPTV, etc.)

Rutas en `config/images.php` y archivos bajo `public/images/`.

---

## Subida de archivos

Las imágenes de **proyectos** y **servicios** del panel se guardan directamente en `public/` (disco `public_assets` en `config/filesystems.php`), con rutas tipo:

```
images/projects/nombre-archivo.jpg
images/services/nombre-archivo.png
```

No hace falta ejecutar `php artisan storage:link` para esas carpetas.

---

## Comandos útiles

```bash
# Servidor local
php artisan serve

# Limpiar caché si algo no se actualiza
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Datos de demostración (proyectos, servicios, mapa)
php artisan migrate --seed
```

---

## Resumen rápido

| Quiero cambiar… | Dónde |
|-----------------|--------|
| Trabajo en mapa + fotos | Panel → **Proyectos** |
| Texto/imagen de un servicio | Panel → **Servicios** |
| Seguimiento de un lead | Panel → **Cotizaciones** |
| Quién entra al admin | Panel → **Usuarios** |
| Logo de empresa en la banda | Carpeta `public/images/clients/` |
| Precios del cotizador web | `config/quotes.php` |

---

## Soporte técnico

Stack: Laravel 12, Livewire 4, Filament 5, MySQL.

Si `/admin` responde 403 en servidor: el usuario debe existir en `users` y tener acceso al panel (`FilamentUser` en `app/Models/User.php`).
