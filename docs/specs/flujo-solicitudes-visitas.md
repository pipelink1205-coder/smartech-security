# Spec: flujo Solicitudes → Visitas → Cotizaciones

**Rama:** `feature/flujo-solicitudes-visitas` · **Estimado:** 1,5–2 días · **Estado:** por implementar

## Problema actual

Todo lo que llega de la web (`QuoteForm`, `ServiceLeadForm`, `PhDiagnosticForm`, `ItDiagnosticForm`) se guarda en `quotes` y mezcla tres cosas distintas:

1. Cada solicitud web recibe número `COT-AAAA-NNNN` (`Quote::creating`), aunque nunca se cotice → huecos en la numeración real.
2. Si el cliente elige "Agendar visita", el estado pasa a `visit_scheduled`, pero solo es una **preferencia** sin confirmar; no hay fecha/hora confirmada ni resultado de la visita.
3. No hay agenda: las visitas se ven filtrando la tabla.
4. El correo `QuoteGenerated` tiene asunto "Tu cotización de Smart Tech Security" aunque el cliente solo pidió información.
5. El origen del lead (unidades, IPTV, software…) va escrito dentro de `message`, no en un campo filtrable.
6. 12 estados mezclan etapas de lead, visita y documento.

## Flujo objetivo

```
SOLICITUDES ──► AGENDA DE VISITAS ──► COTIZACIONES ──► FACTURACIÓN (ya existe)
nuevo           por_confirmar          borrador
contactado      confirmada             enviada
descartado      realizada              aceptada
                reprogramada           perdida / vencida / anulada
                cancelada
```

## Cambios

### 0. Arreglo rápido (primer commit)
- `app/Mail/QuoteGenerated.php`: asunto según intención:
  - `info` → "Recibimos su solicitud – {servicio}"
  - `visit` → "Recibimos su solicitud de visita – {servicio}"

### 1. Base de datos (migraciones, sin perder datos)
- `quotes`:
  - `stage` string, valores `lead` | `quote`, índice. Default `lead`.
  - `source` string nullable, índice: `contacto`, `servicio:{slug}`, `ph:{servicio}:{municipio}`, `ph:diagnostico`, `outsourcing`, `admin`.
  - `quote_number` debe poder ser **null** (solo se asigna al pasar a `stage = quote`).
  - `lost_reason` string nullable.
- Nueva tabla `visits`:
  - `id`, `quote_id` (FK, cascade), `scheduled_at` datetime nullable, `slot` nullable (reusar `Quote::VISIT_SLOTS`), `status` (`por_confirmar`, `confirmada`, `realizada`, `reprogramada`, `cancelada`), `address` nullable, `responsable` string nullable (texto libre por ahora; **aún no hay módulo de técnicos**), `result_notes` text nullable, `photos` json nullable, timestamps.
- **Migración de datos existentes:**
  - Quotes **con ítems** o `status` en (`draft`, `quoted`, `sent`, `accepted`, `rejected`, `expired`, `cancelled`, `closed`) → `stage = quote` (conservan su número).
  - El resto → `stage = lead`. Conservan el número que ya tenían (no renumerar históricos), pero los leads nuevos ya no reciben número.
  - Quotes con `intent = visit` y `preferred_visit_date` → crear `visit` con `status = por_confirmar` (o `confirmada` si su estado era `visit_scheduled` y lo decide el dueño: usar `por_confirmar` por defecto).
  - Parsear `source` desde la línea "Página de origen: …" de `message` cuando exista.

### 2. Modelos
- `Quote`:
  - Scopes `leads()` y `formalQuotes()`.
  - Mover la asignación de `quote_number` de `creating` a un método `promoteToQuote()` que asigna número, `stage = quote`, `status = draft`, `valid_until` y términos por defecto.
  - Separar constantes: `LEAD_STATUSES` (`new`, `contacted`, `discarded`) y `QUOTE_STATUSES` (`draft`, `sent`, `accepted`, `lost`, `expired`, `cancelled`). Mantener compatibilidad de lectura con estados viejos.
  - Relación `visits()` (hasMany) y `latestVisit()`.
- Nuevo `Visit` con casts, constante `STATUSES` y relación `quote()`.

### 3. Formularios web
- Todos guardan `stage = lead`, `status = new` y `source` (ver valores arriba). Ya no ponen `visit_scheduled`.
- Si `intent = visit` → crear también un `Visit` con `status = por_confirmar` y la fecha/franja preferida.
- Quitar del mensaje la línea "Página de origen" (ahora va en `source`); dejar la línea de autorización Ley 1581.

### 4. Filament (grupo de navegación "Operaciones")
1. **Solicitudes** (recurso sobre `Quote::leads()`):
   - Columnas: recibida, cliente/empresa, teléfono, servicio, **origen** (badge legible), tipo (info/visita), estado.
   - Filtros: estado, origen, tipo.
   - Badge de navegación con el conteo de `new`.
   - Acciones por fila: **WhatsApp** (reusar la del EditQuote), **Marcar contactado**, **Agendar visita** (modal: fecha, hora, franja, dirección, responsable → crea/actualiza `Visit` como `confirmada`), **Convertir en cotización** (`promoteToQuote()` y redirige al editor de cotización), **Descartar** (con motivo).
2. **Agenda de visitas** (recurso `Visit`):
   - Pestañas: *Hoy*, *Esta semana*, *Por confirmar*, *Todas*.
   - Columnas: fecha/hora, cliente, servicio, dirección, responsable, estado.
   - Acciones: **Confirmar** (fija fecha/hora), **Reprogramar**, **Marcar realizada** (notas y fotos del resultado), **Cancelar**, **Crear cotización** (desde una visita realizada → `promoteToQuote()` en la quote relacionada).
   - Badge con visitas de hoy.
   - Calendario visual: **no en esta fase**. Solo agregarlo después si existe un plugin compatible con Filament 5.
3. **Cotizaciones** (recurso actual `QuoteResource` filtrado a `formalQuotes()`):
   - Sin cambios de editor. Mostrar sección "Visita" (solo lectura) si la quote tiene visitas.
   - Filtro por origen.
4. **Dashboard** (`SiteOverview`): reemplazar "Cotizaciones nuevas" por: *Solicitudes nuevas*, *Visitas hoy*, *Cotizaciones enviadas este mes*, *Tasa de cierre por origen (últimos 90 días)*.

### 5. Pruebas (`tests/Feature`)
- Un formulario web crea un lead **sin** `quote_number`, con `source`; si es visita, crea `Visit` `por_confirmar`.
- `promoteToQuote()` asigna número consecutivo y no reutiliza números existentes.
- La migración de datos clasifica bien quotes con y sin ítems.
- Las acciones de Filament *Agendar visita*, *Marcar realizada* y *Convertir en cotización* cambian estados correctamente.
- Actualizar las pruebas existentes que dependan de `visit_scheduled` o de que cada lead tenga número.

## Criterios de aceptación
- [ ] La numeración `COT-` solo avanza con cotizaciones reales.
- [ ] Un lead de visita aparece en *Agenda → Por confirmar* hasta que alguien lo confirma.
- [ ] Desde una visita realizada se crea la cotización en un clic, con los datos del cliente copiados.
- [ ] Se puede filtrar solicitudes y cotizaciones por origen.
- [ ] El correo al cliente ya no dice "Tu cotización" cuando solo pidió información.
- [ ] `php artisan test` en verde; probado en el panel con datos de la base local.

## Fuera de alcance (por ahora)
- Módulo de técnicos con usuario propio (usar `responsable` como texto).
- Calendario visual tipo Google Calendar.
- Integrar los clics de WhatsApp (`whatsapp_leads`) con las solicitudes.
