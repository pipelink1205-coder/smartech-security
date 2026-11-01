@php
    use App\Support\PropiedadHorizontal as PH;
    use App\Support\Seo;

    $seo = config('propiedad-horizontal.diagnostico');
    $faqs = config('propiedad-horizontal.faqs_generales');
    $jsonLd = [
        PH::serviceGraph('Diagnóstico de seguridad gratuito para unidades residenciales', $seo['description'], PH::DIAGNOSTICO),
        PH::breadcrumbGraph([['Inicio', '/'], ['Unidades residenciales', PH::BASE], ['Diagnóstico gratuito', PH::DIAGNOSTICO]]),
        Seo::faqGraph($faqs),
    ];
    $zonas = [
        ['icon' => 'bell', 'title' => 'Portería', 'text' => 'Citofonía, visualización de cámaras, registro de visitantes y flujo de domicilios.'],
        ['icon' => 'parking', 'title' => 'Parqueaderos y sótanos', 'text' => 'Cobertura de cámaras, iluminación, rampas y control vehicular.'],
        ['icon' => 'shield', 'title' => 'Perímetro y zonas comunes', 'text' => 'Cerramiento, shut de basuras, piscina, gimnasio y accesos peatonales.'],
        ['icon' => 'zap', 'title' => 'Cuarto de equipos', 'text' => 'Grabador, discos, red, respaldo de energía y estado del cableado.'],
    ];
@endphp

<x-app-layout
    :title="$seo['title']"
    :description="$seo['description']"
    :canonical="Seo::url(PH::DIAGNOSTICO)"
    :extra-json-ld="$jsonLd"
    whatsapp-service="el diagnóstico de seguridad gratuito para unidades residenciales"
>
    @push('head')
        <link rel="stylesheet" href="{{ asset('css/ph.css') }}?v={{ @filemtime(public_path('css/ph.css')) ?: '1' }}" />
    @endpush

    <article class="sd ph ph-diag" data-wa-service="Diagnóstico de seguridad para unidades residenciales">

        <header class="sd-hero ph-hero ph-hero--form">
            <div class="container">
                <nav class="sd-breadcrumb" aria-label="Ruta de navegación">
                    <a href="{{ route('home') }}">Inicio</a>
                    <span aria-hidden="true">/</span>
                    <a href="{{ url(PH::BASE) }}">Unidades residenciales</a>
                    <span aria-hidden="true">/</span>
                    <span aria-current="page">Diagnóstico gratuito</span>
                </nav>

                <div class="ph-diag-grid" id="diagnostico" data-contextual-contact>
                    <div class="sd-hero-copy">
                        <span class="sd-hero-kicker"><x-ph-icon name="file" :size="16" /> Para administradores y consejos</span>
                        <h1 class="sd-hero-title">Diagnóstico de seguridad <span class="ph-grad">gratuito</span> para su unidad residencial</h1>
                        <p class="sd-hero-tagline">Un técnico recorre la unidad o conjunto durante 45 minutos y le entregamos un informe en PDF con fotos, puntos ciegos y tres opciones de mejora con su valor. Es lo que necesita para llevar el tema al consejo o a la asamblea.</p>

                        <ul class="ph-diag-facts">
                            <li><x-ph-icon name="clock" :size="20" /><span><strong>45 minutos</strong> de recorrido en sitio</span></li>
                            <li><x-ph-icon name="file" :size="20" /><span><strong>Informe PDF</strong> con fotos y prioridades</span></li>
                            <li><x-ph-icon name="users" :size="20" /><span><strong>Presentación</strong> al consejo si la necesita</span></li>
                            <li><x-ph-icon name="shield" :size="20" /><span><strong>Sin costo</strong> y sin compromiso</span></li>
                        </ul>

                        <p class="ph-diag-areas">Atendemos unidades y conjuntos en todo Medellín y el Valle de Aburrá, además de Guarne y Rionegro.</p>
                    </div>

                    <livewire:ph-diagnostic-form origin="unidades-residenciales/diagnostico-gratuito" />
                </div>
            </div>
        </header>

        <div class="container ph-main">

            <section class="sd-block" aria-labelledby="ph-zonas">
                <header class="sd-block-head">
                    <span class="sd-block-tag">Qué revisamos</span>
                    <h2 class="sd-block-title" id="ph-zonas">Cuatro zonas, una lista de chequeo</h2>
                </header>
                <div class="ph-pains">
                    @foreach($zonas as $zona)
                        <div class="ph-pain">
                            <span class="ph-pain__icon"><x-ph-icon :name="$zona['icon']" :size="22" /></span>
                            <h3>{{ $zona['title'] }}</h3>
                            <p>{{ $zona['text'] }}</p>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="sd-block" aria-labelledby="ph-recibe">
                <div class="ph-intro__grid ph-intro__grid--center">
                    <div>
                        <header class="sd-block-head">
                            <span class="sd-block-tag">Qué recibe</span>
                            <h2 class="sd-block-title" id="ph-recibe">Un informe que el consejo entiende en 5 minutos</h2>
                        </header>
                        <ul class="ph-checklist">
                            <li><x-ph-icon name="check" :size="16" /> Fotos de cada punto revisado y del estado de los equipos</li>
                            <li><x-ph-icon name="check" :size="16" /> Puntos ciegos marcados por prioridad: crítico, importante, mejora</li>
                            <li><x-ph-icon name="check" :size="16" /> Tres opciones de solución, de la más básica a la más completa</li>
                            <li><x-ph-icon name="check" :size="16" /> Valor de cada opción y alternativa de pago en cuotas</li>
                            <li><x-ph-icon name="check" :size="16" /> Recomendaciones de tratamiento de datos de las cámaras (Ley 1581)</li>
                        </ul>
                    </div>
                    <div class="ph-report-stage">
                        @include('pages.ph.partials.report-card')
                    </div>
                </div>
            </section>

            @include('pages.ph.partials.proceso')

            <section class="sd-block" aria-labelledby="ph-srvs">
                <header class="sd-block-head">
                    <span class="sd-block-tag">Después del diagnóstico</span>
                    <h2 class="sd-block-title" id="ph-srvs">Lo que podemos implementar</h2>
                </header>
                <div class="ph-mini-services">
                    @foreach(PH::servicios() as $slug => $srv)
                        <a href="{{ url(PH::path($slug)) }}" class="ph-mini-service">
                            <x-ph-icon :name="$srv['icon']" :size="20" />
                            <span>{{ $srv['name'] }}</span>
                        </a>
                    @endforeach
                </div>
            </section>

            @include('pages.ph.partials.faqs', ['faqs' => $faqs])

            <div class="ph-backtop">
                <a href="#diagnostico" class="btn btn-primary">Agendar mi diagnóstico gratuito</a>
            </div>
        </div>
    </article>
</x-app-layout>
