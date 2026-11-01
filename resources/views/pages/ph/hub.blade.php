@php
    use App\Support\PropiedadHorizontal as PH;
    use App\Support\Seo;

    $seo = config('propiedad-horizontal.hub');
    $faqs = config('propiedad-horizontal.faqs_generales');
    $jsonLd = [
        PH::serviceGraph('Seguridad electrónica para unidades y conjuntos residenciales', $seo['description'], PH::BASE),
        PH::breadcrumbGraph([['Inicio', '/'], ['Unidades residenciales', PH::BASE]]),
        Seo::faqGraph($faqs),
    ];
@endphp

<x-app-layout
    :title="$seo['title']"
    :description="$seo['description']"
    :canonical="Seo::url(PH::BASE)"
    :extra-json-ld="$jsonLd"
    whatsapp-service="seguridad para unidades residenciales"
>
    @push('head')
        <link rel="stylesheet" href="{{ asset('css/ph.css') }}?v={{ @filemtime(public_path('css/ph.css')) ?: '1' }}" />
    @endpush

    <article class="sd ph" data-wa-service="Seguridad para unidades residenciales">

        {{-- ═══ HERO ═══ --}}
        <header class="sd-hero ph-hero">
            <div class="container">
                <nav class="sd-breadcrumb" aria-label="Ruta de navegación">
                    <a href="{{ route('home') }}">Inicio</a>
                    <span aria-hidden="true">/</span>
                    <span aria-current="page">Unidades residenciales</span>
                </nav>

                <div class="sd-hero-grid">
                    <div class="sd-hero-copy">
                        <span class="sd-hero-kicker"><x-ph-icon name="building" :size="16" /> Propiedad horizontal · Valle de Aburrá</span>
                        <h1 class="sd-hero-title">Seguridad electrónica para <span class="ph-grad">unidades residenciales</span></h1>
                        <p class="sd-hero-tagline">Cámaras IP, control de acceso, citofonía IP y mantenimiento para unidades y conjuntos residenciales, con resultados que el administrador puede mostrar en la asamblea.</p>
                        <ul class="sd-hero-badges">
                            <li>Diagnóstico gratuito</li>
                            <li>Informe para el consejo</li>
                            <li>Pago en cuotas</li>
                        </ul>
                        <div class="sd-hero-actions">
                            <a href="#diagnostico" class="btn btn-primary">Agendar diagnóstico gratuito</a>
                            <a href="#paquetes" class="btn sd-btn-outline">Ver paquetes</a>
                        </div>
                    </div>

                    <div class="ph-hero-visual">
                        <figure class="sd-hero-media">
                            <img src="{{ PH::image(['image_service' => 'camaras-4k']) }}" alt="Cámaras de seguridad en portería de unidad residencial" width="880" height="550" fetchpriority="high" />
                        </figure>
                        @include('pages.ph.partials.report-card')
                    </div>
                </div>
            </div>
        </header>

        <div class="container ph-main">

            {{-- ═══ DOLORES ═══ --}}
            <section class="sd-block" aria-labelledby="ph-dol">
                <header class="sd-block-head">
                    <span class="sd-block-tag">¿Le suena?</span>
                    <h2 class="sd-block-title" id="ph-dol">Los cuatro problemas que más llegan a la administración</h2>
                </header>
                <div class="ph-pains">
                    @foreach(config('propiedad-horizontal.dolores') as $pain)
                        <div class="ph-pain">
                            <span class="ph-pain__icon"><x-ph-icon :name="$pain['icon']" :size="22" /></span>
                            <h3>{{ $pain['title'] }}</h3>
                            <p>{{ $pain['text'] }}</p>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- ═══ SERVICIOS ═══ --}}
            <section class="sd-block" aria-labelledby="ph-srv">
                <header class="sd-block-head">
                    <span class="sd-block-tag">Servicios</span>
                    <h2 class="sd-block-title" id="ph-srv">Lo que instalamos y mantenemos en unidades residenciales</h2>
                </header>
                <div class="ph-services">
                    @foreach($servicios as $slug => $srv)
                        <a href="{{ url(PH::path($slug)) }}" class="ph-service">
                            <span class="ph-service__icon"><x-ph-icon :name="$srv['icon']" :size="26" /></span>
                            <h3>{{ $srv['name'] }}</h3>
                            <p>{{ $srv['tagline'] }}</p>
                            <span class="ph-service__more">Ver detalle <x-ph-icon name="arrow" :size="16" /></span>
                        </a>
                    @endforeach
                </div>
            </section>

            @include('pages.ph.partials.paquetes')

            @include('pages.ph.partials.proceso')

            {{-- ═══ MUNICIPIOS ═══ --}}
            <section class="sd-block" id="cobertura" aria-labelledby="ph-mun">
                <header class="sd-block-head">
                    <span class="sd-block-tag">Cobertura</span>
                    <h2 class="sd-block-title" id="ph-mun">Unidades y conjuntos que atendemos en el Valle de Aburrá</h2>
                </header>
                <p class="ph-lead ph-coverage-note">Atendemos todo Medellín y el Valle de Aburrá. También visitamos {{ implode(', ', array_slice(config('propiedad-horizontal.cobertura_extra'), 0, -1)) }} y {{ \Illuminate\Support\Arr::last(config('propiedad-horizontal.cobertura_extra')) }}, y otros municipios con costo de desplazamiento según la distancia.</p>
                @php($firstTown = array_key_first($municipios))
                <div class="ph-picker" x-data="{ active: '{{ $firstTown }}' }">
                    <div class="ph-picker__tabs" role="tablist" aria-label="Elija su municipio">
                        @foreach($municipios as $mslug => $mun)
                            <button type="button" role="tab" id="tab-{{ $mslug }}" aria-controls="panel-{{ $mslug }}"
                                    @class(['ph-picker__tab', 'is-active' => $mslug === $firstTown])
                                    :class="{ 'is-active': active === '{{ $mslug }}' }"
                                    :aria-selected="active === '{{ $mslug }}' ? 'true' : 'false'"
                                    @click="active = '{{ $mslug }}'">
                                <x-ph-icon name="pin" :size="16" /> {{ $mun['name'] }}
                            </button>
                        @endforeach
                    </div>

                    @foreach($municipios as $mslug => $mun)
                        <div role="tabpanel" id="panel-{{ $mslug }}" aria-labelledby="tab-{{ $mslug }}"
                             @class(['ph-picker__panel', 'is-hidden' => $mslug !== $firstTown])
                             :class="{ 'is-hidden': active !== '{{ $mslug }}' }">
                            <div class="ph-picker__info">
                                <h3>Unidades residenciales en {{ $mun['name'] }}</h3>
                                <p>{{ $mun['contexto'] }}</p>
                                <p class="ph-picker__sectors"><strong>Todo {{ $mun['name'] }}</strong>, incluidos {{ implode(', ', $mun['sectores']) }}.</p>
                                <a href="#diagnostico" class="btn btn-primary ph-picker__cta">Diagnóstico gratuito en {{ $mun['name'] }}</a>
                            </div>
                            <div class="ph-picker__services">
                                @foreach($servicios as $sslug => $srv)
                                    <a href="{{ url(PH::path($sslug, $mslug)) }}" class="ph-picker__service">
                                        <span class="ph-picker__icon"><x-ph-icon :name="$srv['icon']" :size="22" /></span>
                                        <span class="ph-picker__text">
                                            <strong>{{ $srv['name'] }}</strong>
                                            <small>en {{ $mun['name'] }}</small>
                                        </span>
                                        <x-ph-icon name="arrow" :size="18" class="ph-picker__arrow" />
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            @include('pages.ph.partials.proyectos', ['titulo' => 'Instalaciones que hemos entregado'])

            @include('pages.ph.partials.faqs', ['faqs' => $faqs])
        </div>

        @include('pages.ph.partials.cta')
    </article>
</x-app-layout>
