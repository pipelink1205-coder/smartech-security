<div
    class="hero-tech-intro"
    hidden
    data-hero-tech-intro
    data-force="{{ app()->environment('local') && request()->query('intro') === 'force' ? 'true' : 'false' }}"
>
    <div class="hero-tech-intro__glow" aria-hidden="true"></div>
    <div class="hero-tech-intro__shield" aria-hidden="true">
        <img class="hero-tech-intro__shield-depth" src="{{ asset('images/logo-transparent.png') }}" alt="" width="688" height="785">
        <img class="hero-tech-intro__shield-face" src="{{ asset('images/logo-transparent.png') }}" alt="" width="688" height="785">
    </div>
    <div class="hero-tech-intro__watermark" aria-hidden="true">
        <img src="{{ asset('images/logo-transparent.png') }}" alt="" width="688" height="785">
    </div>

    <div class="hero-tech-intro__cards" aria-hidden="true">
        @foreach([
            ['camera', 'Seguridad electrónica'],
            ['network', 'Redes e infraestructura'],
            ['shield', 'Ciberseguridad'],
            ['code', 'Desarrollo de Software'],
            ['code', 'Automatización e IA'],
            ['network', 'Analítica de Datos'],
            ['support', 'Outsourcing TI'],
        ] as [$icon, $label])
            <div class="hero-tech-intro__card">
                <x-service-icon :icon="$icon" size="md" />
                <span>{{ $label }}</span>
            </div>
        @endforeach
    </div>

    <button class="hero-tech-intro__skip" type="button" data-hero-tech-intro-skip>
        Omitir intro
    </button>
</div>

@push('scripts')
    <script src="{{ asset('js/hero-tech-intro.js') }}?v=2" defer></script>
@endpush
