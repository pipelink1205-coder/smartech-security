# Smart Tech Security — guía para Claude

Sitio web y panel interno de **Smart Tech Security** (Envigado, Antioquia): seguridad electrónica, IPTV para hoteles, redes, energía solar, outsourcing de TI y desarrollo de software. Producción: https://smarttechsecurity.com.co

## Stack

- **Laravel 12** · PHP 8.2+ (producción en IIS con PHP 8.5)
- **Livewire 4** (componentes en `app/Livewire`) + Alpine.js (incluido con Livewire)
- **Filament 5** para el panel `/admin` (`app/Filament/Resources`)
- Blade + CSS propio, **sin Tailwind ni Vite en el front público**: los estilos viven en `public/css/app.css` (global) y `public/css/ph.css` (nicho unidades residenciales). JS en `public/js/app.js`.
- MySQL en local (Laragon) · SQLite en memoria para tests
- Colas: `QUEUE_CONNECTION=database` (los correos se encolan; en local hay que correr `php artisan queue:work`)

## Comandos

```bash
php artisan serve                 # o Laragon: http://smart-tech-security.test
php artisan test                  # correr SIEMPRE antes de commitear
php artisan queue:work            # procesar correos encolados
php artisan config:clear && php artisan route:clear && php artisan view:clear
```

## Mapa del proyecto

| Qué | Dónde |
|---|---|
| Rutas públicas | `routes/web.php` |
| SEO (títulos, canónicas, JSON-LD) | `app/Support/Seo.php`, `config/seo.php`, layout `resources/views/components/app-layout.blade.php` |
| Sitemap | `app/Http/Controllers/SitemapController.php` |
| Menú (desktop / móvil) | `resources/views/components/navbar.blade.php`, `mobile-menu.blade.php`; grupos de servicios en `config/site.php` → `nav_service_groups` |
| Páginas de servicio | `resources/views/pages/servicio-show.blade.php` (genérica) y `pages/servicios/{slug}.blade.php` (landings propias: outsourcing-ti, desarrollo-software) |
| Contacto contextual por servicio | `app/Livewire/ServiceLeadForm.php` + preguntas en **`config/lead-forms.php`** + `components/service-contact.blade.php` |
| Nicho unidades residenciales (PH) | contenido en **`config/propiedad-horizontal.php`**, lógica en `app/Support/PropiedadHorizontal.php`, controlador `PropiedadHorizontalController`, vistas `resources/views/pages/ph/*`, form `app/Livewire/PhDiagnosticForm.php` |
| Barra de acción móvil | `resources/views/components/mobile-cta.blade.php` |
| Leads / cotizaciones | modelo `Quote` (todos los formularios guardan aquí) + recurso Filament `Quotes` |
| Despliegue | `deploy-iis.bat` en el servidor (git pull, composer, migrate, seeders, limpiar caché) |

### Nicho unidades residenciales

- URLs: `/unidades-residenciales`, `/unidades-residenciales/diagnostico-gratuito`, `/unidades-residenciales/{servicio}`, `/unidades-residenciales/{servicio}/{municipio}` (4 servicios × 6 municipios = 24 páginas locales).
- `/conjuntos-residenciales/...` y `/diagnostico-gratuito-conjuntos` redirigen con **301**: no borrar esas rutas.
- Para agregar un municipio o servicio: solo editar `config/propiedad-horizontal.php` (incluir su texto en `enfoque` para que la página sea única). Rutas, sitemap y schema se generan solos.
- En Medellín se usa "**unidad residencial**" (menú, títulos); en el texto conviven "unidades y conjuntos" por SEO.

## Reglas de trabajo

1. **Rama por cambio** (`feature/...`, `fix/...`); no trabajar directo en `master`. El dueño revisa, fusiona y hace `pull` en el servidor.
2. **Nunca** commitear `.env`, credenciales ni contraseñas de correo. El `.env` local usa `MAIL_MAILER=log`; producción usa `smtp`.
3. **Nunca** correr `php artisan queue:retry all` en local: hay correos de cotizaciones viejas (`QuoteGenerated`) que irían a clientes reales.
4. Correr `php artisan test` antes de cada commit. Si cambias el menú o las páginas PH, actualizar `tests/Feature/NavServicesDropdownTest.php` y `PropiedadHorizontalPagesTest.php`.
5. **Móvil primero**: revisar a 390 px de ancho, sin scroll horizontal, botones ≥ 44 px, inputs con `font-size: 16px`. Los cambios móviles van en `@media (max-width: 640px)` / `768px`.
6. Al cambiar `public/js/app.js` o los CSS, el layout ya los versiona por `filemtime`: no hace falta tocar nada más.
7. Textos en **español de Colombia**, trato de "usted" en formularios comerciales. No inventar cifras, clientes ni testimonios: si falta un dato real, dejarlo pendiente.
8. Formularios nuevos: honeypot anti-spam, rate limit, casilla de autorización de datos (**Ley 1581 de 2012**) y guardar el lead en `Quote`.
9. Al editar archivos grandes con scripts, reemplazar por coincidencia exacta y única (no por posición): ya hubo un incidente de secciones duplicadas en `desarrollo-software.blade.php`.

## Pendientes conocidos

- Módulo **Soluciones** en Filament para editar el contenido de `config/propiedad-horizontal.php` y `config/lead-forms.php` sin deploy (pasar a tablas + seeder + caché).
- Filtro por origen/solución en Cotizaciones.
- Plantilla PDF del informe de diagnóstico para consejos de administración.
- Fotos y testimonios reales de unidades residenciales.
- Eventos GA4 para clics de WhatsApp y envíos de formularios.
