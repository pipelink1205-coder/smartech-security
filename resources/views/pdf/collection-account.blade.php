<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    @page { margin: 96px 40px 58px; }
    * { box-sizing: border-box; }
    html, body, table, td, th, div, span, p, strong {
        font-family: Arial, Helvetica, sans-serif !important;
    }
    body { color: #172033; font-size: 10px; line-height: 1.4; }
    .page-header { position: fixed; top: -80px; left: 0; right: 0; height: 66px; border-bottom: 1.5px solid #168c80; }
    .page-footer { position: fixed; bottom: -42px; left: 0; right: 0; height: 32px; border-top: 1px solid #d9e2e8; padding-top: 6px; color: #64748b; font-size: 7.5px; }
    .logo { height: 50px; width: auto; }
    .header-table { width: 100%; border-collapse: collapse; }
    .company { text-align: right; vertical-align: middle; font-size: 7.5px; color: #526174; }
    .company strong { display: block; font-size: 11px; color: #0f766e; margin-bottom: 2px; }
    .title-row { width: 100%; border-collapse: collapse; margin: 4px 0 16px; }
    .title-row h1 { margin: 0; color: #0f766e; font-size: 20px; font-weight: 700; }
    .title-row .place { margin-top: 4px; color: #526174; font-size: 10px; }
    .date-box { display: inline-block; padding: 8px 12px; background: #f3faf9; border-left: 3px solid #168c80; text-align: right; }
    .date-box strong { display: block; font-size: 12px; color: #0f766e; }
    .box { background: #f7fafb; border: 1px solid #d7e6e3; border-left: 3px solid #168c80; padding: 12px 14px; margin: 0 0 10px; }
    .box.center { text-align: center; }
    .kicker { display: block; color: #0f766e; font-size: 8px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; margin-bottom: 5px; }
    .lead { margin: 0; font-size: 12px; font-weight: 700; color: #172033; }
    .sub { margin: 3px 0 0; color: #526174; font-size: 10px; }
    .amount { margin: 0; font-size: 11px; font-weight: 700; color: #0f766e; line-height: 1.45; }
    .items { width: 100%; border-collapse: collapse; margin-top: 6px; }
    .items th { background: #0f766e; color: #fff; padding: 6px 5px; font-size: 7.5px; text-transform: uppercase; text-align: left; }
    .items td { padding: 6px 5px; border-bottom: 1px solid #dfe7eb; vertical-align: top; }
    .items tbody tr:nth-child(even) td { background: #fff; }
    .right { text-align: right; }
    .center-cell { text-align: center; }
    .bottom { width: 100%; border-collapse: separate; border-spacing: 8px 0; margin: 6px 0 0 -8px; }
    .bottom td { width: 50%; vertical-align: top; }
    .total-box { background: #0f766e; color: #fff; padding: 14px 16px; }
    .total-box .kicker { color: #99f6e4; }
    .total-box .lead { color: #fff; font-size: 18px; }
    .pay-box { background: #f7fafb; border: 1px solid #d7e6e3; padding: 12px 14px; }
    .pay-box .line { margin: 0 0 3px; font-weight: 700; color: #172033; }
    .pay-box .muted { margin: 0; color: #526174; font-size: 9px; }
    .note { margin-top: 10px; color: #64748b; font-size: 8px; }
    .disclaimer { margin-top: 10px; padding: 8px 10px; background: #fff7ed; border-left: 3px solid #f59e0b; color: #9a3412; font-size: 7.5px; }
</style>
</head>
<body>
@php
    $account->loadMissing(['items', 'quote']);
    $company = config('quotes.company');
    $logo = public_path('images/logo-transparent.png');
    $quoteRef = $account->quote?->quote_number;
    $money = fn (float|int $value): string => '$'.number_format((float) $value, 0, ',', '.');
    $holder = $account->bank_account_holder ?: ($company['legal_name'] ?? 'Smart Tech Security');
    $bankType = strtoupper(trim((string) ($account->bank_account_type ?: 'Ahorros')));
    $bankName = strtoupper(trim((string) ($account->bank_name ?: '')));
    $conceptText = trim((string) ($account->concept ?: ''));
    $singleItem = $account->items->count() === 1 ? $account->items->first() : null;
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
                {{ config('contact.email') }} · +57 {{ config('contact.whatsapp_formatted') }}
            </td>
        </tr>
    </table>
</div>
@endif

<div class="page-footer">
    Cuenta de cobro · Smart Tech Security · No es factura electrónica DIAN
    <span style="float:right">{{ $account->number }}</span>
</div>

<table class="title-row">
    <tr>
        <td style="vertical-align:middle;">
            <h1>Cuenta de cobro {{ $account->number }}</h1>
            <div class="place">{{ $account->place_and_date }}</div>
        </td>
        <td style="width:38%;text-align:right;vertical-align:middle;">
            <div class="date-box">
                <span style="color:#64748b;font-size:7px;text-transform:uppercase;">Documento</span>
                <strong>{{ $account->number }}</strong>
            </div>
        </td>
    </tr>
</table>

<div class="box center">
    <span class="kicker">Cliente</span>
    <p class="lead">{{ $account->client_name }}</p>
    <p class="sub">
        @if($account->client_document)
            NIT {{ $account->client_document }}
        @else
            Documento pendiente
        @endif
        @if($account->client_address)
            · {{ $account->client_address }}
        @endif
    </p>
</div>

<div class="box center">
    <span class="kicker">Debe a</span>
    <p class="lead">{{ $company['legal_name'] ?? 'Smart Tech Security' }}</p>
    <p class="sub">
        @if(!empty($company['tax_id'])) NIT {{ $company['tax_id'] }}@endif
        @if(!empty($company['address'])) · {{ $company['address'] }}@endif
    </p>
</div>

<div class="box center">
    <span class="kicker">La suma de</span>
    <p class="amount">{{ $account->amount_in_words }} ({{ $money($account->total) }})</p>
</div>

<div class="box">
    <span class="kicker" style="text-align:center;display:block;">Por concepto de</span>
    @if($conceptText !== '')
        <p class="lead" style="text-align:center;margin-bottom:8px;">{{ $conceptText }}</p>
    @elseif($singleItem)
        <p class="lead" style="text-align:center;margin-bottom:8px;">{{ $singleItem->description }}</p>
    @endif

    @if($account->items->count() > 1 || ($account->items->count() === 1 && $conceptText !== ''))
        <table class="items">
            <thead>
                <tr>
                    <th style="width:8%">#</th>
                    <th>Descripción</th>
                    <th style="width:12%" class="center-cell">Cant.</th>
                    <th style="width:18%" class="right">Valor</th>
                    <th style="width:18%" class="right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($account->items as $i => $line)
                    <tr>
                        <td class="center-cell">{{ $i + 1 }}</td>
                        <td>{{ $line->description }}</td>
                        <td class="center-cell">{{ rtrim(rtrim(number_format((float) $line->quantity, 2, ',', '.'), '0'), ',') }}</td>
                        <td class="right">{{ $money($line->unit_price) }}</td>
                        <td class="right">{{ $money($line->line_total) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($quoteRef)
        <p class="sub" style="text-align:center;margin-top:8px;">Cotización {{ $quoteRef }}</p>
    @endif
</div>

<table class="bottom">
    <tr>
        <td>
            <div class="total-box">
                <span class="kicker">Costo total</span>
                <p class="lead">{{ $money($account->total) }} COP</p>
            </div>
        </td>
        <td>
            <div class="pay-box">
                <p class="line">{{ $holder }}</p>
                @if($account->bank_nit)
                    <p class="muted">NIT {{ $account->bank_nit }}</p>
                @endif
                @if($bankName !== '')
                    <p class="line" style="margin-top:6px;">CTA {{ $bankType }} {{ $bankName }}</p>
                @endif
                @if($account->bank_account_number)
                    <p class="lead" style="font-size:13px;margin-top:2px;">{{ $account->bank_account_number }}</p>
                @endif
                @if($account->payment_instructions)
                    <p class="muted" style="margin-top:6px;white-space:pre-line;">{{ $account->payment_instructions }}</p>
                @endif
            </div>
        </td>
    </tr>
</table>

@if($account->notes)
<p class="note"><strong>Observaciones:</strong> {{ $account->notes }}</p>
@endif

<div class="disclaimer">
    Este documento es una cuenta de cobro comercial. No constituye factura electrónica de venta ni documento equivalente DIAN.
</div>
</body>
</html>
