<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
body{margin:0;padding:32px 12px;background:#f1f5f9;font-family:Arial,sans-serif;color:#0f172a}
.card{max-width:600px;margin:auto;background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 8px 30px rgba(15,23,42,.08)}
.head{background:#0f766e;color:#fff;padding:28px 32px}.head h1{margin:0;font-size:21px}.head p{margin:6px 0 0;opacity:.9}
.body{padding:30px 32px}.body p{line-height:1.6;color:#334155}.summary{background:#f8fafc;border-left:4px solid #0f766e;padding:14px 18px;margin:20px 0}
.total{font-size:20px;font-weight:700;color:#0f766e}.btn{display:inline-block;background:#0f766e;color:#fff!important;text-decoration:none;padding:13px 20px;border-radius:8px;font-weight:700}
.foot{padding:20px 32px;background:#f8fafc;color:#64748b;font-size:12px}
</style>
</head>
<body>
<div class="card">
    <div class="head">
        <h1>Smart Tech Security</h1>
        <p>Cuenta de cobro {{ $account->number }}</p>
    </div>
    <div class="body">
        <p>Hola <strong>{{ $account->client_name }}</strong>,</p>
        <p>Adjuntamos la cuenta de cobro
            @if($account->quote?->quote_number)
                correspondiente a la cotización {{ $account->quote->quote_number }}
            @endif
            .
        </p>
        <div class="summary">
            <div>Total a pagar</div>
            <div class="total">${{ number_format((float) $account->total, 0, ',', '.') }} COP</div>
        </div>
        <p>También puede descargar el documento con este enlace (válido 30 días):</p>
        <p><a class="btn" href="{{ $downloadUrl }}">Descargar cuenta de cobro</a></p>
        <p>Este documento no es factura electrónica. Si necesita factura DIAN, indíquenoslo por este mismo correo.</p>
    </div>
    <div class="foot">
        {{ config('contact.email') }} · +57 {{ config('contact.whatsapp_formatted') }}<br>
        {{ config('contact.address') }}
    </div>
</div>
</body>
</html>
