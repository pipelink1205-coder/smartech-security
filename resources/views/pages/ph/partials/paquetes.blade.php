{{-- Paquetes para unidades residenciales. $destacado (opcional) = clave del paquete recomendado --}}
@php
    $paquetes = config('propiedad-horizontal.paquetes');
    $destacado = $destacado ?? collect($paquetes)->search(fn ($p) => ! empty($p['featured']));
@endphp
<section class="sd-block" id="paquetes" aria-labelledby="ph-paq">
    <header class="sd-block-head">
        <span class="sd-block-tag">Paquetes</span>
        <h2 class="sd-block-title" id="ph-paq">Tres paquetes cerrados, listos para presentar al consejo</h2>
    </header>

    <div x-data="{ plan: '{{ $destacado }}' }">
    {{-- Móvil: selector tipo app, se ve un paquete a la vez --}}
    <div class="ph-seg" role="tablist" aria-label="Elegir paquete">
        @foreach($paquetes as $key => $plan)
            <button type="button" role="tab" @class(['ph-seg__btn', 'is-active' => $key === $destacado])
                    :class="{ 'is-active': plan === '{{ $key }}' }" @click="plan = '{{ $key }}'">{{ $plan['name'] }}</button>
        @endforeach
    </div>

    <div class="ph-plans">
        @foreach($paquetes as $key => $plan)
            <article @class(['ph-plan', 'ph-plan--featured' => $key === $destacado, 'm-hidden' => $key !== $destacado])
                     :class="{ 'm-hidden': plan !== '{{ $key }}' }">
                @if($key === $destacado)
                    <span class="ph-plan__badge">{{ isset($recomendadoLabel) ? $recomendadoLabel : 'Más elegido' }}</span>
                @endif
                <span class="ph-plan__step">Paquete {{ $loop->iteration }}</span>
                <h3 class="ph-plan__name">{{ $plan['name'] }}</h3>
                <p class="ph-plan__tagline">{{ $plan['tagline'] }}</p>
                <ul class="ph-plan__features">
                    @foreach($plan['features'] as $feature)
                        <li><x-ph-icon name="check" :size="16" /> {{ $feature }}</li>
                    @endforeach
                </ul>
                <a href="#diagnostico" class="btn {{ $key === $destacado ? 'btn-primary' : 'btn-ghost' }} ph-plan__cta">Cotizar con diagnóstico gratis</a>
            </article>
        @endforeach
    </div>
    </div>

    <div class="ph-plus">
        <div class="ph-plus__copy">
            <span class="ph-plus__tag"><x-ph-icon name="wrench" :size="16" /> Mantenimiento Plus · mensual</span>
            <h3>Toda instalación incluye 3 meses de mantenimiento</h3>
            <p>Después, la unidad decide si lo renueva. Pago en cuotas disponible para todos los paquetes.</p>
        </div>
        <ul class="ph-plus__list">
            @foreach(config('propiedad-horizontal.mantenimiento_plus') as $item)
                <li><x-ph-icon name="check" :size="16" /> {{ $item }}</li>
            @endforeach
        </ul>
    </div>
</section>
