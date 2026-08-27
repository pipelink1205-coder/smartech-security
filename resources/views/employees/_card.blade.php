@if(! empty($showFaceLabels))
    <p class="employee-card-face-label">Frente</p>
@endif
<div class="employee-card employee-card--front">
    <img class="employee-card__template" src="{{ $card['front_template'] }}" alt="">

    <div class="employee-card__copy">
        <h1>{{ $card['full_name'] }}</h1>
        <p class="employee-card__position">{{ $card['position'] }}</p>
        <p class="employee-card__code">Código {{ $card['employee_code'] }}</p>
    </div>

    <div class="employee-card__portrait-window">
        @if($card['portrait'])
            <img
                class="employee-card__portrait"
                src="{{ $card['portrait'] }}"
                alt="Fotografía de {{ $card['full_name'] }}"
                style="height: {{ $card['portrait_scale'] }}%; left: calc(50% + {{ $card['portrait_x'] }}%); top: calc(50% + {{ $card['portrait_y'] }}%);"
            >
        @else
            <div class="employee-card__portrait-empty">Foto</div>
        @endif
    </div>

    <img class="employee-card__foreground" src="{{ $card['front_foreground'] }}" alt="">
</div>

@if(! empty($showFaceLabels))
    <p class="employee-card-face-label">Reverso</p>
@endif
<div class="employee-card employee-card--back">
    <img class="employee-card__template" src="{{ $card['back_template'] }}" alt="">

    @if($card['signature'])
        <img class="employee-card__signature" src="{{ $card['signature'] }}" alt="Firma autorizada">
    @endif

    @if($card['qr'])
        <img class="employee-card__qr" src="{{ $card['qr'] }}" alt="Código QR de verificación">
    @else
        <span class="employee-card__qr-pending">QR</span>
    @endif
</div>
