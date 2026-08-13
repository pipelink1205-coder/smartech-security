# Facturación electrónica DIAN (módulo portado)

Código extraído del pack de apps Laravel (POS restaurante/gimnasio/farmacia) y adaptado a Smart Tech Security.

## Qué se portó

| Pieza | Ubicación |
|-------|-----------|
| Servicios UBL / XAdES / SOAP / CUFE / QR | `app/Services/Dian/*` |
| Modelos | `Setting`, `DianResolution`, `DianEvent`, `DianCreditNote`, `ElectronicInvoice`, `ElectronicInvoiceItem` |
| Migraciones | `database/migrations/2026_08_12_12000*` |
| Config kill-switch | `config/dian.php` (`DIAN_ENABLED`) |
| Doc original PDF | `docs/dian/Documentacion_Facturacion_Electronica_DIAN.pdf` |

## Qué NO se portó

- POS, mesas, cocina, caja, productos de restaurante
- Controllers UI del pack (`DianBillingController`, etc.)
- Tabla `orders` — reemplazada por `electronic_invoices`

## Estado actual

**Motor DIAN inactivo** (`DIAN_ENABLED=false`). El flujo comercial en admin ya existe:

1. Cotización → **Generar factura** (mapper)
2. Operaciones → **Facturas** (editar adquiriente, PDF)
3. **Enviar al cliente** (correo prellenado + WhatsApp con enlace PDF)
4. Cotización → **Generar cuenta de cobro** (documento comercial, no DIAN)
5. **Emitir a DIAN** solo si `DIAN_ENABLED=true` + cert + resolución

Pendiente antes de producción DIAN:

1. Completar `settings` (`dian_company_*`, software ID/PIN, certificado)
2. Crear resolución activa en `dian_resolutions`
3. Pruebas en ambiente de Habilitación DIAN

## Uso previsto

```php
$invoice = app(\App\Domain\Invoicing\QuoteToElectronicInvoiceMapper::class)->fromQuote($quote);
app(\App\Services\Dian\DianService::class)->sendInvoice($invoice);
```

## Dependencias Composer

- `robrichards/xmlseclibs` — firma XML (XadesSigner)
- `endroid/qr-code` — QR en representación gráfica (QrGenerator)
- Guzzle — ya viene con Laravel (DianSoapClient)
