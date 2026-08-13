# Facturación electrónica DIAN (módulo portado)

Código extraído del pack de apps Laravel (POS restaurante/gimnasio/farmacia) y adaptado a Smart Tech Security.

## Qué se portó

| Pieza | Ubicación |
|-------|-----------|
| Servicios UBL / XAdES / SOAP / CUFE / QR | `app/Services/Dian/*` |
| Modelos | `Setting`, `DianResolution`, `DianEvent`, `DianCreditNote`, `ElectronicInvoice`, `ElectronicInvoiceItem` |
| Migraciones | `database/migrations/2026_08_12_12000*` y `2026_08_13_184000_seed_dian_enabled_setting.php` |
| Config kill-switch | `config/dian.php` (`DIAN_ENABLED`) |
| Admin | Administración → **Configuración DIAN** y **Resoluciones DIAN** |
| Doc original PDF | `docs/dian/Documentacion_Facturacion_Electronica_DIAN.pdf` |

## Qué NO se portó

- POS, mesas, cocina, caja, productos de restaurante
- Controllers UI del pack (`DianBillingController`, etc.)
- Tabla `orders` — reemplazada por `electronic_invoices`

## Estado actual

**Motor DIAN inactivo** hasta cumplir las dos llaves de envío y el checklist del admin.

1. Cotización → **Generar factura** (mapper)
2. Operaciones → **Facturas** (editar adquiriente, PDF)
3. **Enviar al cliente** (correo prellenado + WhatsApp con enlace PDF)
4. Cotización → **Generar cuenta de cobro** (documento comercial, no DIAN)
5. **Emitir a DIAN** solo si `DIAN_ENABLED=true` + interruptor del panel + cert + resolución

## Configuración en admin

1. Administración → **Configuración DIAN**: empresa, software ID/PIN, TestSetId, certificado `.p12`, impuestos.
2. Administración → **Resoluciones DIAN**: prefijo, rango, vigencia, clave técnica, ambiente, activa.
3. En el servidor: `DIAN_ENABLED=true` en `.env` (kill switch). Quédate en `DIAN_ENVIRONMENT=2` (habilitación) hasta pasar pruebas.
4. El botón **Emitir a DIAN** aparece en la factura cuando las dos llaves están encendidas.

El certificado se guarda en `storage/app/private/dian/certs` (disco `local`), no en `public/`.

## Uso previsto

```php
$invoice = app(\App\Domain\Invoicing\QuoteToElectronicInvoiceMapper::class)->fromQuote($quote);
app(\App\Services\Dian\DianService::class)->sendInvoice($invoice);
```

## Dependencias Composer

- `robrichards/xmlseclibs` — firma XML (XadesSigner)
- `endroid/qr-code` — QR en representación gráfica (QrGenerator)
- Guzzle — ya viene con Laravel (DianSoapClient)
