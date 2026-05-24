<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"/><style>
body{font-family:Arial,sans-serif;background:#f9fafb;margin:0;padding:32px 0;}
.card{max-width:520px;margin:0 auto;background:white;border-radius:10px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08);}
.top{background:#0f172a;padding:20px 28px;color:white;}
.top h1{margin:0;font-size:17px;}
.body{padding:24px 28px;}
table{width:100%;border-collapse:collapse;}
td{padding:9px 12px;border-bottom:1px solid #e2e8f0;font-size:13px;}
td:first-child{color:#6b7280;font-weight:600;width:40%;}
.green{color:#16a34a;font-weight:700;}
.btn{display:block;background:#16a34a;color:white;text-align:center;padding:12px;border-radius:7px;text-decoration:none;font-weight:700;font-size:13px;margin-top:20px;}
.btn-wa{display:block;background:#25d366;color:white;text-align:center;padding:12px;border-radius:7px;text-decoration:none;font-weight:700;font-size:13px;margin-top:10px;}
</style></head>
<body>
<div class="card">
  <div class="top">
    <h1>🔔 Nuevo lead recibido</h1>
  </div>
  <div class="body">
    <table>
      <tr><td>Nombre</td><td><strong>{{ $quote->name }}</strong></td></tr>
      <tr><td>Teléfono</td><td>{{ $quote->phone }}</td></tr>
      @if($quote->email)
      <tr><td>Email</td><td>{{ $quote->email }}</td></tr>
      @endif
      <tr><td>Servicio</td><td class="green">{{ $quote->service }}</td></tr>
      @if($quote->zone)
      <tr><td>Zona</td><td>{{ $quote->zone }}</td></tr>
      @endif
      @if($quote->price_min)
      <tr><td>Estimado</td><td class="green">{{ $quote->price_range }}</td></tr>
      @endif
      @if($quote->message)
      <tr><td>Mensaje</td><td>{{ $quote->message }}</td></tr>
      @endif
      <tr><td>Hora</td><td>{{ $quote->created_at->format('d/m/Y H:i') }}</td></tr>
    </table>

    <a href="{{ url('/admin/quotes/'.$quote->id) }}" class="btn">Ver en panel de administración →</a>
    <a href="{{ $quote->whatsapp_link }}" class="btn-wa">Contactar por WhatsApp 💬</a>
  </div>
</div>
</body>
</html>
