<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    @page { margin: 100px 34px 64px; }
    * { box-sizing: border-box; }
    html, body, table, td, th, div, span, p, strong {
        font-family: Arial, Helvetica, sans-serif !important;
    }
    body { color: #172033; font-size: 9px; line-height: 1.35; }
    .page-header { position: fixed; top: -84px; left: 0; right: 0; height: 70px; border-bottom: 1.5px solid #168c80; }
    .page-footer { position: fixed; bottom: -48px; left: 0; right: 0; height: 36px; border-top: 1px solid #d9e2e8; padding-top: 6px; color: #64748b; font-size: 7.5px; }
    .logo { height: 52px; width: auto; }
    .header-table, .meta-table, .items, .totals { width: 100%; border-collapse: collapse; }
    .company { text-align: right; vertical-align: middle; font-size: 7.5px; color: #526174; }
    .company strong { display: block; font-size: 11px; color: #0f766e; margin-bottom: 2px; }
    .document-title { display: table; width: 100%; margin-bottom: 14px; }
    .document-title .left, .document-title .right { display: table-cell; vertical-align: middle; }
    .document-title h1 { margin: 0; color: #0f766e; font-size: 18px; font-weight: 700; }
    .document-title .right { width: 42%; text-align: right; }
    .number-box { display: inline-block; padding: 8px 12px; background: #f3faf9; border-left: 3px solid #168c80; text-align: left; }
    .number-box strong { display: block; font-size: 12px; color: #0f766e; }
    .section-title { margin: 14px 0 6px; color: #0f766e; font-size: 9px; text-transform: uppercase; letter-spacing: .08em; border-bottom: 1px solid #b9d8d4; padding-bottom: 3px; }
    .meta-wrap { background: #f7fafb; border-left: 3px solid #b7dcd7; padding: 10px 12px; }
    .meta-table td { padding: 2px 5px; vertical-align: top; }
    .meta-table .label { width: 18%; color: #64748b; }
    .meta-table .value { width: 32%; font-weight: 700; }
    .items { margin-top: 12px; }
    .items th { background: #0f766e; color: #fff; padding: 7px 5px; font-size: 7px; text-transform: uppercase; }
    .items td { padding: 7px 5px; border-bottom: 1px solid #dfe7eb; vertical-align: top; }
    .items tbody tr:nth-child(even) td { background: #f8fafb; }
    .right { text-align: right; }
    .center { text-align: center; }
    .totals { width: 43%; margin-left: auto; margin-top: 10px; }
    .totals td { padding: 4px 7px; }
    .totals .grand td { background: #0f766e; color: #fff; font-size: 11px; font-weight: 700; padding: 7px; }
    .cufe { margin-top: 14px; font-size: 7px; color: #526174; word-break: break-all; }
    .note { margin-top: 12px; color: #64748b; font-size: 7.5px; }
</style>
</head>
<body>
@php
    $invoice->loadMissing(['details', 'quote']);
    $company = config('quotes.company');
    $number = $invoice->full_number ?: ('BORRADOR-'.$invoice->id);
    $logo = public_path('images/logo-transparent.png');
    $quoteRef = $invoice->quote?->quote_number;
@endphp

@if(is_readable($logo))
<div class="page-header">
    <table class="header-table">
        <tr>
            <td style="width:72px;vertical-align:middle;"><img class="logo" src="{{ $logo }}" alt="Logo"></td>
            <td class="company">
                <strong>{{ $company['legal_name'] ?? 'Smart Tech Security' }}</strong>
                @if(!empty($company['tax_id'])) NIT {{ $company['tax_id'] }}<br>@endif
                {{ $company['address'] ?? '' }}<br>
                {{ $company['city'] ?? '' }}
            </td>
        </tr>
    </table>
</div>
@endif

<div class="page-footer">
    Representación gráfica de factura · Smart Tech Security
    @if($invoice->cufe) · CUFE disponible abajo @endif
    · Página <span class="page"></span>
</div>

<div class="document-title">
    <div class="left"><h1>Factura electrónica de venta</h1></div>
    <div class="right">
        <div class="number-box">
            <span style="color:#64748b;font-size:7px;text-transform:uppercase;">Número</span>
            <strong>{{ $number }}</strong>
            <span style="color:#64748b;">{{ $invoice->created_at?->format('d/m/Y H:i') }}</span>
        </div>
    </div>
</div>

<div class="section-title">Adquiriente</div>
<div class="meta-wrap">
    <table class="meta-table">
        <tr>
            <td class="label">Nombre</td>
            <td class="value">{{ $invoice->client_name }}</td>
            <td class="label">Documento</td>
            <td class="value">
                {{ $invoice->client_tipo_documento ?: '—' }}
                {{ $invoice->client_document ?: 'Pendiente' }}
                @if($invoice->client_dv) - {{ $invoice->client_dv }}@endif
            </td>
        </tr>
        <tr>
            <td class="label">Correo</td>
            <td class="value">{{ $invoice->client_email ?: '—' }}</td>
            <td class="label">Teléfono</td>
            <td class="value">{{ $invoice->client_phone ?: '—' }}</td>
        </tr>
        <tr>
            <td class="label">Dirección</td>
            <td class="value" colspan="3">{{ $invoice->client_address ?: '—' }}</td>
        </tr>
        @if($quoteRef)
        <tr>
            <td class="label">Cotización</td>
            <td class="value" colspan="3">{{ $quoteRef }}</td>
        </tr>
        @endif
    </table>
</div>

<div class="section-title">Detalle</div>
<table class="items">
    <thead>
        <tr>
            <th style="width:8%">#</th>
            <th>Descripción</th>
            <th style="width:12%" class="center">Cant.</th>
            <th style="width:18%" class="right">Precio c/IVA</th>
            <th style="width:18%" class="right">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoice->details as $i => $line)
            <tr>
                <td class="center">{{ $i + 1 }}</td>
                <td>{{ $line->description }}</td>
                <td class="center">{{ number_format((float) $line->quantity, 2, ',', '.') }}</td>
                <td class="right">${{ number_format((float) $line->price, 0, ',', '.') }}</td>
                <td class="right">${{ number_format((float) $line->quantity * (float) $line->price, 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<table class="totals">
    <tr>
        <td class="label">Base gravable</td>
        <td class="value right">${{ number_format((float) $invoice->subtotal, 0, ',', '.') }}</td>
    </tr>
    @if((float) $invoice->descuento_total > 0)
    <tr>
        <td class="label">Descuentos</td>
        <td class="value right">-${{ number_format((float) $invoice->descuento_total, 0, ',', '.') }}</td>
    </tr>
    @endif
    <tr>
        <td class="label">IVA</td>
        <td class="value right">${{ number_format((float) $invoice->iva, 0, ',', '.') }}</td>
    </tr>
    <tr class="grand">
        <td>Total a pagar</td>
        <td class="right">${{ number_format((float) $invoice->total_a_pagar ?: $invoice->total, 0, ',', '.') }} COP</td>
    </tr>
</table>

@if($invoice->cufe)
<p class="cufe"><strong>CUFE:</strong> {{ $invoice->cufe }}</p>
@if($invoice->qr_url)
<p class="cufe"><strong>Consulta DIAN:</strong> {{ $invoice->qr_url }}</p>
@endif
@endif

<p class="note">
    Estado DIAN: {{ $invoice->dian_status }}
    @if($invoice->dian_status === 'PENDING')
        · Documento interno / borrador. La validez fiscal requiere aceptación DIAN.
    @endif
</p>
</body>
</html>
