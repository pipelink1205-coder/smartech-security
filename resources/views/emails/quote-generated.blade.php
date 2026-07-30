<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"/><style>
body{font-family:Arial,sans-serif;background:#f9fafb;margin:0;padding:32px 0;}
.card{max-width:560px;margin:0 auto;background:white;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08);}
.top{background:#178f82;padding:28px 32px;color:white;}
.top h1{margin:0;font-size:20px;}
.top p{margin:6px 0 0;opacity:.85;font-size:13px;}
.body{padding:28px 32px;}
.greeting{font-size:16px;font-weight:700;margin-bottom:8px;}
p{color:#374151;line-height:1.6;margin:0 0 16px;}
.steps{background:#f8fafc;border-radius:8px;padding:16px 20px;margin:16px 0;}
.steps li{color:#374151;font-size:13px;margin-bottom:8px;}
.btn{display:block;background:#178f82;color:white;text-align:center;padding:13px;border-radius:8px;text-decoration:none;font-weight:700;font-size:14px;margin-top:24px;}
.footer{text-align:center;padding:20px;color:#9ca3af;font-size:11px;}
</style></head>
<body>
<div class="card">
  <div class="top">
    <h1>Smart Tech Security</h1>
    <p>Proveedor de Sistemas de Seguridad · Medellín</p>
  </div>
  <div class="body">
    <div class="greeting">Hola, {{ $quote->name }}</div>
    <p>Recibimos tu {{ ($quote->intent ?? 'info') === 'visit' ? 'solicitud de visita técnica' : 'solicitud de información' }} sobre <strong>{{ $quote->service }}</strong>. Un asesor te contactará en menos de 2 horas hábiles.</p>

    @if(($quote->intent ?? 'info') === 'visit' && $quote->preferred_visit_summary)
    <p>Preferencia indicada: <strong>{{ $quote->preferred_visit_summary }}</strong>. Te confirmaremos el horario según disponibilidad.</p>
    @endif

    <p>¿Qué sigue?</p>
    <div class="steps">
      <ol>
        <li>Te llamamos o escribimos por WhatsApp para confirmar detalles</li>
        <li>Agendamos una visita técnica gratuita en tu propiedad</li>
        <li>Tras el diagnóstico entregamos la cotización formal detallada</li>
        <li>Instalación con garantía y soporte técnico</li>
      </ol>
    </div>

    <a href="https://wa.me/{{ config('contact.whatsapp') }}" class="btn">
      Escríbenos por WhatsApp
    </a>
  </div>
  <div class="footer">
    Smart Tech Security · {{ config('contact.email') }}<br/>
    {{ config('contact.admin_email') }} · Envigado, Antioquia, Colombia
  </div>
</div>
</body>
</html>
