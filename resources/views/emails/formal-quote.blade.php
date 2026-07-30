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
        <p>Cotización {{ $quote->quote_number }}</p>
    </div>
    <div class="body">
        <p>Hola <strong>{{ $quote->name }}</strong>,</p>
        <p>Adjuntamos la cotización preparada para {{ $quote->project_title ?: $quote->service }}.</p>
        <div class="summary">
            <div>Total de la propuesta</div>
            <div class="total">${{ number_format((float) $quote->grand_total, 0, ',', '.') }} {{ $quote->currency }}</div>
            @if($quote->valid_until)
                <div>Válida hasta el {{ $quote->valid_until->format('d/m/Y') }}</div>
            @endif
        </div>
        <p>También puede descargar el documento mediante el siguiente enlace, disponible durante 30 días:</p>
        <p><a class="btn" href="{{ $downloadUrl }}">Descargar cotización</a></p>
        <p>Si desea aprobarla o necesita algún ajuste, puede responder este correo o comunicarse por WhatsApp.</p>
    </div>
    <div class="foot">
        {{ config('contact.email') }} · +57 {{ config('contact.whatsapp_formatted') }}<br>
        {{ config('contact.address') }}
    </div>
</div>
</body>
</html>
