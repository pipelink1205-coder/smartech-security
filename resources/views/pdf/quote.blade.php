<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    @page { margin: 118px 34px 72px; }
    * { box-sizing: border-box; }
    html, body, table, td, th, div, span, p, strong {
        font-family: Arial, Helvetica, sans-serif !important;
    }
    body { color: #172033; font-size: 9px; line-height: 1.35; }
    .page-header { position: fixed; top: -98px; left: 0; right: 0; height: 82px; border-bottom: 1.5px solid #168c80; }
    .page-footer { position: fixed; bottom: -52px; left: 0; right: 0; height: 38px; border-top: 1px solid #d9e2e8; padding-top: 8px; color: #64748b; font-size: 7.5px; }
    .watermark { position: fixed; top: 34%; left: 30%; width: 40%; opacity: .035; z-index: -1; }
    .logo-cell { width: 72px; vertical-align: middle; }
    .logo { height: 62px; width: auto; }
    .header-table, .meta-table, .items, .totals, .signature-table { width: 100%; border-collapse: collapse; }
    .company { text-align: right; vertical-align: middle; font-size: 7.5px; color: #526174; }
    .company strong { display: block; font-size: 11px; color: #0f766e; margin-bottom: 2px; }
    .document-title { display: table; width: 100%; margin-bottom: 16px; }
    .document-title .left, .document-title .right { display: table-cell; vertical-align: middle; }
    .document-title h1 { margin: 0; color: #0f766e; font-size: 21px; letter-spacing: .025em; font-weight: 700; }
    .document-title .right { width: 38%; text-align: right; }
    .number-box { display: inline-block; padding: 9px 13px; background: #f3faf9; border-left: 3px solid #168c80; }
    .number-box strong { display: block; font-size: 12px; color: #0f766e; }
    .section-title { margin: 15px 0 6px; color: #0f766e; font-size: 9px; text-transform: uppercase; letter-spacing: .08em; border-bottom: 1px solid #b9d8d4; padding-bottom: 3px; }
    .meta-wrap { background: #f7fafb; border-left: 3px solid #b7dcd7; padding: 10px 12px; }
    .meta-table td { padding: 2px 5px; vertical-align: top; }
    .meta-table .label { width: 18%; color: #64748b; }
    .meta-table .value { width: 32%; font-weight: 700; }
    .items { margin-top: 12px; table-layout: fixed; }
    .items thead { display: table-header-group; }
    .items tr { page-break-inside: avoid; }
    .items th { background: #0f766e; color: #fff; padding: 7px 5px; font-size: 7px; text-transform: uppercase; letter-spacing: .025em; }
    .items td { padding: 7px 5px; border-bottom: 1px solid #dfe7eb; vertical-align: top; }
    .items tbody tr:nth-child(even) td { background: #f8fafb; }
    .concept { font-weight: 700; color: #172033; margin-bottom: 2px; }
    .description { color: #566577; font-size: 8px; white-space: pre-line; }
    .center { text-align: center; }
    .right { text-align: right; }
    .type { color: #0f766e; font-size: 7px; text-transform: uppercase; }
    .totals-wrap { margin-top: 10px; width: 100%; }
    .totals { width: 43%; margin-left: auto; }
    .totals td { padding: 4px 7px; }
    .totals .label { color: #526174; }
    .totals .value { text-align: right; font-weight: 700; }
    .totals .grand td { background: #0f766e; color: #fff; font-size: 11px; font-weight: 700; padding: 7px; }
    .conditions { page-break-inside: avoid; margin-top: 16px; }
    .condition-grid { width: 100%; border-collapse: separate; border-spacing: 6px 0; margin-left: -6px; }
    .condition-grid td { width: 33.33%; vertical-align: top; background: #f7fafb; border: 1px solid #dce5e9; padding: 8px; white-space: pre-line; font-size: 7.5px; color: #526174; }
    .condition-grid strong { display: block; color: #0f766e; font-size: 8px; text-transform: uppercase; margin-bottom: 4px; }
    .signature { margin-top: 22px; page-break-inside: avoid; width: 46%; }
    .signature-line { border-top: 1px solid #526174; padding-top: 5px; }
    .signature-name { font-weight: 700; color: #172033; }
    .note { margin-top: 15px; color: #64748b; font-size: 7.5px; }
</style>
</head>
<body>
@php
    $quote->loadMissing('items');
    $company = config('quotes.company');
    $number = $quote->quote_number ?: ('COT-'.$quote->id);
    $types = \App\Models\QuoteCatalogItem::TYPES;
    $units = \App\Models\QuoteCatalogItem::UNITS;
    $logo = public_path('images/logo-transparent.png');
    $commercialSections = collect([
        ['title' => 'Condiciones comerciales', 'content' => $quote->terms],
        ['title' => 'Forma de pago', 'content' => $quote->payment_terms],
        ['title' => 'Garantía', 'content' => $quote->warranty_terms],
    ])->filter(function (array $section): bool {
        $content = trim((string) ($section['content'] ?? ''));

        return $content !== '' && $content !== '0';
    })->values();
    $commercialSectionWidth = $commercialSections->isNotEmpty()
        ? 100 / $commercialSections->count()
        : 100;
@endphp

@if(is_readable($logo))
    <img class="watermark" src="{{ $logo }}" alt="">
@endif

<div class="page-header">
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                @if(is_readable($logo))
                    <img class="logo" src="{{ $logo }}" alt="Smart Tech Security">
                @else
                    <strong>SMART TECH SECURITY</strong>
                @endif
            </td>
            <td class="company">
                <strong>{{ $company['legal_name'] }}</strong>
                @if($company['tax_id']) NIT {{ $company['tax_id'] }}<br>@endif
                {{ $company['address'] }}<br>
                {{ config('contact.email') }} · +57 {{ config('contact.whatsapp_formatted') }}<br>
                {{ $company['website'] }}
            </td>
        </tr>
    </table>
</div>

<div class="page-footer">
    {{ $company['legal_name'] }} · {{ $company['city'] }} · {{ config('contact.email') }}
    <span style="float:right">Cotización {{ $number }}</span>
</div>

<div class="document-title">
    <div class="left">
        <h1>COTIZACIÓN</h1>
        <div>Propuesta comercial para {{ $quote->project_title ?: $quote->service }}</div>
    </div>
    <div class="right">
        <div class="number-box">
            <span>Número</span>
            <strong>{{ $number }}</strong>
            <span>{{ ($quote->issued_at ?: $quote->created_at)->format('d/m/Y') }}</span>
        </div>
    </div>
</div>

<div class="meta-wrap">
    <table class="meta-table">
        <tr>
            <td class="label">Cliente</td>
            <td class="value">{{ $quote->company ?: $quote->name }}</td>
            <td class="label">Contacto</td>
            <td class="value">{{ $quote->name }}</td>
        </tr>
        <tr>
            <td class="label">Teléfono</td>
            <td class="value">{{ $quote->phone }}</td>
            <td class="label">Correo</td>
            <td class="value">{{ $quote->email ?: '—' }}</td>
        </tr>
        <tr>
            <td class="label">Proyecto</td>
            <td class="value">{{ $quote->project_title ?: $quote->service }}</td>
            <td class="label">Vigencia</td>
            <td class="value">{{ $quote->valid_until ? $quote->valid_until->format('d/m/Y') : '—' }}</td>
        </tr>
        @if($quote->client_address || $quote->zone)
        <tr>
            <td class="label">Ubicación</td>
            <td class="value" colspan="3">{{ $quote->client_address ?: $quote->zone }}</td>
        </tr>
        @endif
    </table>
</div>

<div class="section-title">Conceptos cotizados</div>
<table class="items">
    <thead>
        <tr>
            <th style="width:9%">Tipo</th>
            <th style="width:39%">Concepto y descripción</th>
            <th style="width:8%">Cant.</th>
            <th style="width:9%">Unidad</th>
            <th style="width:13%">Vr. unitario</th>
            <th style="width:8%">Dcto.</th>
            <th style="width:14%">Total</th>
        </tr>
    </thead>
    <tbody>
        @forelse($quote->items as $item)
        <tr>
            <td class="type">{{ $types[$item->type] ?? $item->type }}</td>
            <td>
                <div class="concept">{{ $item->concept }}</div>
                @if($item->description && $item->description !== $item->concept)
                    <div class="description">{{ $item->description }}</div>
                @endif
            </td>
            <td class="center">{{ rtrim(rtrim(number_format((float) $item->quantity, 2, ',', '.'), '0'), ',') }}</td>
            <td class="center">{{ $units[$item->unit] ?? $item->unit }}</td>
            <td class="right">${{ number_format((float) $item->unit_price, 0, ',', '.') }}</td>
            <td class="center">{{ (float) $item->discount_percent > 0 ? rtrim(rtrim(number_format((float) $item->discount_percent, 2, ',', '.'), '0'), ',').'%' : '—' }}</td>
            <td class="right">${{ number_format((float) $item->line_total, 0, ',', '.') }}</td>
        </tr>
        @empty
        <tr><td colspan="7" class="center">Esta cotización todavía no contiene conceptos.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="totals-wrap">
    <table class="totals">
        <tr><td class="label">Subtotal bruto</td><td class="value">${{ number_format((float) $quote->subtotal, 0, ',', '.') }}</td></tr>
        @if((float) $quote->discount_total > 0)
            <tr><td class="label">Descuentos</td><td class="value">-${{ number_format((float) $quote->discount_total, 0, ',', '.') }}</td></tr>
        @endif
        <tr><td class="label">IVA</td><td class="value">${{ number_format((float) $quote->tax_total, 0, ',', '.') }}</td></tr>
        <tr class="grand"><td>Total {{ $quote->currency }}</td><td class="right">${{ number_format((float) $quote->grand_total, 0, ',', '.') }}</td></tr>
    </table>
</div>

@if($commercialSections->isNotEmpty())
    <div class="conditions">
        <table class="condition-grid">
            <tr>
                @foreach($commercialSections as $section)
                    <td style="width: {{ $commercialSectionWidth }}%">
                        <strong>{{ $section['title'] }}</strong>{{ $section['content'] }}
                    </td>
                @endforeach
            </tr>
        </table>
    </div>
@endif

@if($quote->advisor_name)
<div class="signature">
    <div class="signature-line">
        <div class="signature-name">{{ $quote->advisor_name }}</div>
        <div>{{ $quote->advisor_title ?: 'Asesor comercial' }}</div>
        <div>{{ config('contact.email') }}</div>
    </div>
</div>
@endif

<div class="note">
    Esta propuesta se emite exclusivamente para el cliente indicado. Los valores están expresados en pesos colombianos y están sujetos a las condiciones comerciales descritas.
</div>
</body>
</html>
