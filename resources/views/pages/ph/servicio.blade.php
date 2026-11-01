@php
    use App\Support\PropiedadHorizontal as PH;
    use App\Support\Seo;

    $path = PH::path($servicio['slug']);
    $title = $servicio['h1'].' en el Valle de Aburrá | Smart Tech Security';
    $description = \Illuminate\Support\Str::limit($servicio['tagline'], 155, '');
    $faqs = array_merge($servicio['faqs'], array_slice(config('propiedad-horizontal.faqs_generales'), 0, 2));
    $jsonLd = [
        PH::serviceGraph($servicio['h1'], $servicio['tagline'], $path),
        PH::breadcrumbGraph([['Inicio', '/'], ['Unidades residenciales', PH::BASE], [$servicio['name'], $path]]),
        Seo::faqGraph($faqs),
    ];
@endphp

<x-app-layout
    :title="$title"
    :description="$description"
    :canonical="Seo::url($path)"
    :og-image="PH::image($servicio)"
    :extra-json-ld="$jsonLd"
    :whatsapp-service="$servicio['short'].' para unidades residenciales'"
>
    @push('head')
        <link rel="stylesheet" href="{{ asset('css/ph.css') }}?v={{ @filemtime(public_path('css/ph.css')) ?: '1' }}" />
    @endpush

    <article class="sd ph" data-wa-service="{{ $servicio['name'] }} para unidades residenciales">

        <header class="sd-hero ph-hero">
            <div class="container">
                <nav class="sd-breadcrumb" aria-label="Ruta de navegación">
                    <a href="{{ route('home') }}">Inicio</a>
                    <span aria-hidden="true">/</span>
                    <a href="{{ url(PH::BASE) }}">Unidades residenciales</a>
                    <span aria-hidden="true">/</span>
                    <span aria-current="page">{{ $servicio['name'] }}</span>
                </nav>

                <div class="sd-hero-grid">
                    <div class="sd-hero-copy">
                        <span class="sd-hero-kicker"><x-ph-icon :name="$servicio['icon']" :size="16" /> Propiedad horizontal</span>
                        <h1 class="sd-hero-title">{{ $servicio['h1'] }}</h1>
                        <p class="sd-hero-tagline">{{ $servicio['tagline'] }}</p>
                        <ul class="sd-hero-badges">
                            <li>Diagnóstico gratuito</li>
                            <li>Instalación por etapas</li>
                            <li>Pago en cuotas</li>
                        </ul>
                        <div class="sd-hero-actions">
                            <a href="#diagnostico" class="btn btn-primary">Agendar diagnóstico gratuito</a>
                            <a href="#cobertura" class="btn sd-btn-outline">Ver mi municipio</a>
                        </div>
                    </div>
                    <div class="ph-hero-visual">
                        <figure class="sd-hero-media">
                            <img src="{{ PH::image($servicio) }}" alt="{{ $servicio['h1'] }}" width="880" height="550" fetchpriority="high" />
                        </figure>
                        @include('pages.ph.partials.report-card')
                    </div>
                </div>
            </div>
        </header>

        <div class="container ph-main">

            <section class="sd-block ph-intro" aria-labelledby="ph-int">
                <div class="ph-intro__grid">
                    <div>
                        <header class="sd-block-head">
                            <span class="sd-block-tag">Por qué ahora</span>
                            <h2 class="sd-block-title" id="ph-int">Lo que vemos en las unidades</h2>
                        </header>
                        <p class="ph-lead">{{ $servicio['intro'] }}</p>
                    </div>
                    <div>
                        <header class="sd-block-head">
                            <span class="sd-block-tag">Qué incluye</span>
                            <h2 class="sd-block-title">Alcance del servicio</h2>
                        </header>
                        <ul class="ph-checklist">
                            @foreach($servicio['includes'] as $item)
                                <li><x-ph-icon name="check" :size="16" /> {{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </section>

            @include('pages.ph.partials.paquetes', [
                'destacado' => $servicio['paquete'] ?? null,
                'recomendadoLabel' => ! empty($servicio['paquete']) ? 'Recomendado para '.$servicio['short'] : null,
            ])

            <section class="sd-block" id="cobertura" aria-labelledby="ph-cob">
                <header class="sd-block-head">
                    <span class="sd-block-tag">Cobertura</span>
                    <h2 class="sd-block-title" id="ph-cob">{{ $servicio['name'] }} para unidades residenciales en su municipio</h2>
                </header>
                <div class="ph-town-grid">
                    @foreach($municipios as $mslug => $mun)
                        <a href="{{ url(PH::path($servicio['slug'], $mslug)) }}" class="ph-town-card">
                            <span class="ph-town__pin"><x-ph-icon name="pin" :size="18" /></span>
                            <span class="ph-town-card__body">
                                <strong>{{ $mun['name'] }}</strong>
                                <small>{{ implode(' · ', array_slice($mun['sectores'], 0, 3)) }}</small>
                            </span>
                            <x-ph-icon name="arrow" :size="18" class="ph-town-card__arrow" />
                        </a>
                    @endforeach
                </div>
            </section>

            @include('pages.ph.partials.proceso')

            @include('pages.ph.partials.proyectos', ['titulo' => $servicio['name'].': instalaciones entregadas'])

            @include('pages.ph.partials.faqs', ['faqs' => $faqs, 'titulo' => 'Preguntas sobre '.$servicio['short']])

            <section class="sd-block" aria-labelledby="ph-otros">
                <header class="sd-block-head">
                    <span class="sd-block-tag">Complementa</span>
                    <h2 class="sd-block-title" id="ph-otros">Otros servicios para unidades residenciales</h2>
                </header>
                <div class="ph-mini-services">
                    @foreach($otros as $oslug => $otro)
                        <a href="{{ url(PH::path($oslug)) }}" class="ph-mini-service">
                            <x-ph-icon :name="$otro['icon']" :size="20" />
                            <span>{{ $otro['name'] }}</span>
                        </a>
                    @endforeach
                </div>
            </section>
        </div>

        @include('pages.ph.partials.cta', ['servicioSlug' => $servicio['slug']])

        <x-mobile-cta href="#diagnostico" label="Agendar diagnóstico gratis" :wa-message="'Hola, quiero información sobre '.$servicio['short'].' para mi unidad residencial.'" />
    </article>
</x-app-layout>
