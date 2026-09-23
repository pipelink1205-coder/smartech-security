@if(! empty($showFaceLabels))
    <p class="employee-card-face-label">Frente</p>
@endif
<div class="employee-card employee-card--front">
    <img class="employee-card__watermark" src="{{ $card['brand_mark'] }}" alt="">

    <header class="employee-card__brand" aria-label="Smart Tech Security S.A.S.">
        <img class="employee-card__brand-mark" src="{{ $card['brand_mark'] }}" alt="">
        <span class="employee-card__brand-name">SMART TECH</span>
        <span class="employee-card__brand-company">SECURITY S.A.S.</span>
    </header>

    <div class="employee-card__portrait-window">
        @if($card['portrait'])
            <img
                class="employee-card__portrait"
                src="{{ $card['portrait'] }}"
                alt="Fotografía de {{ $card['full_name'] }}"
                style="height: {{ $card['portrait_scale'] }}%; left: calc(50% + {{ $card['portrait_x'] }}%); top: calc(50% + {{ $card['portrait_y'] }}%);"
            >
        @else
            <div class="employee-card__portrait-empty">FOTO</div>
        @endif
    </div>

    <section class="employee-card__copy">
        <h1>{{ $card['full_name'] }}</h1>
        <p class="employee-card__position">{{ $card['position'] }}</p>
        <p class="employee-card__code">Código <strong>{{ $card['employee_code'] }}</strong></p>
        <p class="employee-card__authorization">PERSONAL AUTORIZADO</p>
    </section>

    <div class="employee-card__footer employee-card__footer--front"><span></span></div>
</div>

@if(! empty($showFaceLabels))
    <p class="employee-card-face-label">Reverso</p>
@endif
<div class="employee-card employee-card--back">
    <img class="employee-card__watermark employee-card__watermark--back" src="{{ $card['brand_mark'] }}" alt="">

    <section class="employee-card__back-copy">
        <h2>SMART TECH SECURITY S.A.S.</h2>
        <div class="employee-card__back-rule"></div>
        <p class="employee-card__nit">NIT 901.124.137-1</p>
        <p class="employee-card__notice">Este carnet es personal e intransferible.<br>Si lo encuentra, comuníquese con:</p>
        <div class="employee-card__contacts">
            <p><span>☎</span>+57 301 458 9464</p>
            <p><span>✉</span>comercial@smarttechsecurity.com.co</p>
            <p><span>●</span>Envigado, Antioquia</p>
        </div>
    </section>

    <div class="employee-card__signature-block">
        @if($card['signature'])
            <img class="employee-card__signature" src="{{ $card['signature'] }}" alt="Firma autorizada">
        @endif
        <span>Firma autorizada</span>
    </div>

    <div class="employee-card__qr-panel">
        <div class="employee-card__qr-frame">
            @if($card['qr'])
                <img class="employee-card__qr" src="{{ $card['qr'] }}" alt="Código QR de verificación">
            @else
                <span class="employee-card__qr-pending">QR</span>
            @endif
        </div>
        <p>Escanee para verificar<br>esta credencial</p>
    </div>

    <div class="employee-card__footer employee-card__footer--back">
        <span></span>
        <p>smarttechsecurity.com.co</p>
    </div>
</div>
