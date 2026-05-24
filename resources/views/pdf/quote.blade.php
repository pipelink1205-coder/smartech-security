<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8"/>
<style>
  body { font-family: DejaVu Sans, sans-serif; color: #111; font-size: 13px; }
  .header { background: linear-gradient(135deg, #178f82, #2ec4a0); color: white; padding: 20px 32px; display: table; width: 100%; }
  .header-logo { height: 52px; vertical-align: middle; }
  .header-text { vertical-align: middle; padding-left: 16px; }
  .header h1 { font-size: 18px; margin: 0; letter-spacing: .04em; }
  .header p  { margin: 4px 0 0; font-size: 11px; opacity: .85; }
  .body { padding: 32px; }
  .badge { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 16px 24px; margin-bottom: 24px; }
  .badge .num { font-size: 24px; font-weight: 800; color: #178f82; }
  .badge .label { font-size: 11px; color: #6b7280; }
  table { width: 100%; border-collapse: collapse; margin: 16px 0; }
  th { background: #f1f5f9; text-align: left; padding: 8px 12px; font-size: 11px; color: #475569; text-transform: uppercase; }
  td { padding: 10px 12px; border-bottom: 1px solid #e2e8f0; }
  .green { color: #178f82; font-weight: 700; }
  .footer-note { margin-top: 32px; padding: 16px; background: #fafafa; border-left: 3px solid #178f82; font-size: 11px; color: #6b7280; }
  .footer-bar { margin-top: 40px; text-align: center; font-size: 10px; color: #9ca3af; }
</style>
</head>
<body>

<div class="header">
  <table cellpadding="0" cellspacing="0" width="100%"><tr>
    <td width="60"><img src="{{ public_path('images/logo.png') }}" class="header-logo" alt="Smart Tech Security" /></td>
    <td class="header-text">
      <h1>SMART TECH SECURITY</h1>
      <p>Proveedor de Sistemas de Seguridad · {{ config('contact.email') }} · {{ config('contact.admin_email') }}</p>
    </td>
  </tr></table>
</div>

<div class="body">
  <h2 style="margin-bottom:4px">Cotización Preliminar #{{ str_pad($quote->id, 5, '0', STR_PAD_LEFT) }}</h2>
  <p style="color:#6b7280;font-size:11px">Generada el {{ $quote->created_at->format('d/m/Y') }} · Válida por 30 días</p>

  {{-- Rango de precio --}}
  <div class="badge">
    <div class="num">{{ $quote->price_range }}</div>
    <div class="label">Estimado preliminar en COP (precio final tras visita técnica gratuita)</div>
  </div>

  {{-- Datos del cliente --}}
  <table>
    <tr><th colspan="2">Datos del cliente</th></tr>
    <tr><td>Nombre</td><td><strong>{{ $quote->name }}</strong></td></tr>
    <tr><td>Teléfono</td><td>{{ $quote->phone }}</td></tr>
    @if($quote->email)
    <tr><td>Email</td><td>{{ $quote->email }}</td></tr>
    @endif
    @if($quote->zone)
    <tr><td>Zona</td><td>{{ $quote->zone }}</td></tr>
    @endif
  </table>

  {{-- Servicio --}}
  <table>
    <tr><th colspan="2">Servicio solicitado</th></tr>
    <tr><td>Servicio</td><td class="green">{{ $quote->service }}</td></tr>
    @if($quote->message)
    <tr><td>Descripción</td><td>{{ $quote->message }}</td></tr>
    @endif
  </table>

  {{-- Precio estimado --}}
  @if($quote->price_min)
  <table>
    <tr><th colspan="2">Estimado de inversión</th></tr>
    <tr><td>Precio mínimo estimado</td><td class="green">${{ number_format($quote->price_min, 0, ',', '.') }} COP</td></tr>
    <tr><td>Precio máximo estimado</td><td class="green">${{ number_format($quote->price_max, 0, ',', '.') }} COP</td></tr>
    <tr><td colspan="2" style="font-size:10px;color:#9ca3af">* Los precios incluyen mano de obra e instalación. Materiales y equipos según especificación final.</td></tr>
  </table>
  @endif

  <div class="footer-note">
    <strong>Próximos pasos:</strong> Un asesor le contactará en menos de 2 horas para agendar una visita técnica sin costo.
    Durante la visita se entregará la cotización definitiva detallada con todos los materiales y tiempos de instalación.
  </div>
</div>

<div class="footer-bar">
  {{ config('contact.email') }} · {{ config('contact.admin_email') }} · WhatsApp: +57 {{ config('contact.whatsapp_formatted') }}@if(config('contact.whatsapp_secondary')) / {{ config('contact.whatsapp_secondary_formatted') }}@endif · Envigado, Colombia
</div>

</body>
</html>
