# Smart Tech Security – Laravel

Sitio web completo con cotizador automático para Smart Tech Security.

---

## ⚡ Instalación rápida en Windows

### 1. Instalar Laragon (recomendado)
Descarga **Laragon Full** desde https://laragon.org/download/
Incluye PHP, MySQL, Composer y Node.js — todo en un clic.

### 2. Crear el proyecto Laravel
Abre la terminal de Laragon y ejecuta:
```bash
composer create-project laravel/laravel smart-tech-security
cd smart-tech-security
```

### 3. Instalar dependencias del proyecto
```bash
composer require livewire/livewire
composer require filament/filament:"^3.0" -W
composer require barryvdh/laravel-dompdf

npm install
```

### 4. Copiar los archivos de este proyecto
Copia las carpetas:
- `app/`          → sobre `smart-tech-security/app/`
- `resources/`    → sobre `smart-tech-security/resources/`
- `routes/`       → sobre `smart-tech-security/routes/`
- `database/`     → sobre `smart-tech-security/database/`
- `config/quotes.php` → en `smart-tech-security/config/`
- `.env.example`  → cópialo como `.env`

### 5. Configurar entorno
```bash
# Copia el .env
copy .env.example .env

# Genera la APP_KEY
php artisan key:generate
```

Edita el `.env` con tus datos de base de datos y correo.

### 6. Crear base de datos
En Laragon: clic derecho → MySQL → Crear base de datos → `smart_tech_security`

O desde terminal MySQL:
```sql
CREATE DATABASE smart_tech_security CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 7. Ejecutar migraciones y datos iniciales
```bash
php artisan migrate --seed
```

### 8. Instalar panel de administración (Filament 5)
```bash
php artisan filament:install --panels
php artisan make:filament-user  # Crear usuario admin
```

Guía completa del panel: **[ADMIN.md](ADMIN.md)**

### 9. ¡Lanzar!
```bash
# Terminal 1
php artisan serve

# Terminal 2
npm run dev
```

Abre http://localhost:8000 en tu navegador.
Panel admin: http://localhost:8000/admin

---

## 📁 Estructura del proyecto

```
app/
├── Http/Controllers/
│   ├── HomeController.php      ← Carga las páginas
│   └── QuoteController.php     ← Procesa cotizaciones + genera PDF
├── Livewire/
│   └── QuoteForm.php           ← Formulario reactivo con precios en tiempo real
├── Mail/
│   ├── QuoteGenerated.php      ← Email al cliente
│   └── NewLeadAlert.php        ← Alerta interna
└── Models/
    ├── Quote.php               ← Cotizaciones
    ├── Service.php             ← Servicios del catálogo
    └── Project.php             ← Portafolio de proyectos

config/
└── quotes.php                  ← Precios por servicio y recargos por zona

resources/
├── views/
│   ├── layouts/app.blade.php   ← Layout principal
│   ├── welcome.blade.php       ← Página de inicio
│   ├── components/             ← Secciones (navbar, hero, servicios…)
│   ├── livewire/               ← Formulario reactivo
│   ├── emails/                 ← Templates de email
│   └── pdf/                    ← Template del PDF de cotización
├── css/app.css                 ← Todo el CSS del sitio
└── js/app.js                   ← JavaScript (menú, animaciones, contadores)
```

---

## 💰 Configurar precios

Edita `config/quotes.php`:

```php
'pricing' => [
    'Cámaras de Seguridad 4K' => [800_000, 3_500_000],  // [min, max] en COP
    'Energía Solar'            => [4_000_000, 20_000_000],
    // ...
],
'zone_surcharge' => [
    'Medellín'   => 0,    // sin recargo
    'Caldas'     => 12,   // +12%
    // ...
],
```

El formulario Livewire actualiza el precio estimado **en tiempo real** al seleccionar servicio + zona.

---

## 📬 Configurar emails (Gmail)

1. En Gmail → Cuenta → Seguridad → **Contraseñas de aplicación**
2. Genera una contraseña de app
3. En `.env`:
```
MAIL_USERNAME=tu-email@gmail.com
MAIL_PASSWORD=xxxx-xxxx-xxxx-xxxx   ← contraseña de app
ADMIN_EMAIL=tu-email@gmail.com
```

---

## 🛡️ Panel de administración

Accede en `/admin` con el usuario creado con `make:filament-user`.

**Documentación:** [ADMIN.md](ADMIN.md) — usuarios, proyectos (mapa + galería), servicios, cotizaciones y qué sigue siendo por carpetas/archivos de config.

---

## 🚀 Siguientes pasos para escalar

| Feature                  | Comando / Paquete                              |
|--------------------------|------------------------------------------------|
| Gestión visual de leads  | `make:filament-resource Lead --generate`       |
| WhatsApp automático      | Twilio SDK o Evolution API                     |
| Firma de PDFs            | `setasign/fpdi`                                |
| Pagos anticipos          | `mercadopago/sdk-php`                          |
| Cola de emails           | `php artisan queue:work` + Redis               |
| Blog / SEO               | `artesaos/seotools` + sección Blog             |
| Multi-idioma             | `php artisan lang:publish`                     |

---

## 🆘 Soporte

Cualquier error, revisar:
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```
