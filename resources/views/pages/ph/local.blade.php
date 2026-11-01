@php
    use App\Support\PropiedadHorizontal as PH;
    use App\Support\Seo;
    use Illuminate\Support\Str;

    $path = PH::path($servicio['slug'], $municipio['slug']);
    $h1 = $servicio['h1'].' en '.$municipio['name'];
    $title = Str::limit($servicio['name'].' para unidades en '.$municipio['short'], 60, '').' | Smart Tech Security';
    $description = Str::limit($servicio['name'].' para unidades y conjuntos residenciales en '.$municipio['name'].' ('.implode(', ', array_slice($municipio['sectores'], 0, 3)).'). Diagnóstico gratuito, informe para el consejo y pago en cuotas.', 160, '…');

    $faqsLocales = [
        [
            'question' => '¿Atienden unidades residenciales en '.implode(', ', array_slice($municipio['sectores'], 0, 3)).'?',
            'answer' => 'Sí. Atendemos unidades y conjuntos residenciales en todo '.$municipio['name'].', incluidos sectores como '.implode(', ', $municipio['sectores']).'. El diagnóstico se agenda según la disponibilidad de la administración.',
        ],
        [
            'question' => '¿Cómo empieza un proyecto de '.$servicio['short'].' en '.$municipio['short'].'?',
            'answer' => 'Con un diagnóstico gratuito de 45 minutos en la unidad. Después le entregamos un informe en PDF con fotos y tres opciones, listo para presentar al consejo de administración.',
        ],
    ];
    $faqs = array_merge($faqsLocales, $servicio['faqs']);

    $jsonLd = [
        PH::serviceGraph($h1, $enfoque ?? $servicio['tagline'], $path, $municipio),
        PH::breadcrumbGraph([
            ['Inicio', '/'],
            ['Unidades residenciales', PH::BASE],
            [$servicio['name'], PH::path($servicio['slug'])],
            [$municipio['name'], $path],
        ]),
        Seo::faqGraph($faqs),
    ];
@endphp

<x-app-layout
    :title="$title"
    :description="$description"
    :canonical="Seo::url($path)"
    :og-image="PH::image($servicio)"
    :extra-json-ld="$jsonLd"
    :whatsapp-service="$servicio['short'].' para unidades en '.$municipio['short']"
>
    @push('head')
        <link rel="stylesheet" href="{{ asset('css/ph.css') }}?v={{ @filemtime(public_path('css/ph.css')) ?: '1' }}" />
    @endpush

    <article class="sd ph" data-wa-service="{{ $servicio['name'] }} para unidades en {{ $municipio['short'] }}">

        <header class="sd-hero ph-hero">
            <div class="container">
                <nav class="sd-breadcrumb" aria-label="Ruta de navegación">
                    <a href="{{ route('home') }}">Inicio</a>
                    <span aria-hidden="true">/</span>
                    <a href="{{ url(PH::BASE) }}">Unidades residenciales</a>
                    <span aria-hidden="true">/</span>
                    <a href="{{ url(PH::path($servicio['slug'])) }}">{{ $servicio['name'] }}</a>
                    <span aria-hidden="true">/</span>
                    <span aria-current="page">{{ $municipio['name'] }}</span>
                </nav>

                <div class="sd-hero-grid">
                    <div class="sd-hero-copy">
                        <span class="sd-hero-kicker"><x-ph-icon name="pin" :size="16" /> {{ $municipio['name'] }} · Propiedad horizontal</span>
                        <h1 class="sd-hero-title">{{ $servicio['h1'] }} en <span class="ph-grad">{{ $municipio['short'] }}</span></h1>
                        <p class="sd-hero-tagline">{{ $enfoque ?? $servicio['tagline'] }}</p>
                        <ul class="sd-hero-badges">
                            <li>Diagnóstico gratuito en {{ $municipio['short'] }}</li>
                            <li>Informe para el consejo</li>
                            <li>Pago en cuotas</li>
                        </ul>
                        <div class="sd-hero-actions">
                            <a href="#diagnostico" class="btn btn-primary">Agendar diagnóstico gratuito</a>
                            <x-whatsapp-link from="ph_local_hero" :message="'Hola, administro una unidad residencial en '.$municipio['name'].' y quiero información sobre '.$servicio['short'].'.'" class="btn sd-btn-outline" target="_blank">WhatsApp</x-whatsapp-link>
                        </div>
                    </div>
                    <div class="ph-hero-visual">
                        <figure class="sd-hero-media">
                            <img src="{{ PH::image($servicio) }}" alt="{{ $h1 }}" width="880" height="550" fetchpriority="high" />
                        </figure>
                        @include('pages.ph.partials.report-card')
                    </div>
                </div>
            </div>
        </header>

        <div class="container ph-main">

            {{-- ═══ CONTEXTO LOCAL ═══ --}}
            <section class="sd-block ph-local" aria-labelledby="ph-loc">
                <div class="ph-intro__grid">
                    <div>
                        <header class="sd-block-head">
                            <span class="sd-block-tag">{{ $municipio['name'] }}</span>
                            <h2 class="sd-block-title" id="ph-loc">Unidades residenciales en {{ $municipio['short'] }}: lo que encontramos</h2>
                        </header>
                        <p class="ph-lead">{{ $municipio['contexto'] }}</p>
                        <p class="ph-lead">{{ $servicio['intro'] }}</p>
                    </div>
                    <aside class="ph-local-card">
                        <span class="ph-local-card__icon"><x-ph-icon name="pin" :size="22" /></span>
                        <h3>Atendemos todo {{ $municipio['name'] }}, incluidos:</h3>
                        <ul class="ph-sector-chips">
                            @foreach($municipio['sectores'] as $sector)
                                <li>{{ $sector }}</li>
                            @endforeach
                        </ul>
                        <p class="ph-local-card__focus"><strong>Diagnóstico gratuito en {{ $municipio['short'] }}:</strong> recorrido de 45 minutos e informe en PDF para el consejo.</p>
                    </aside>
                </div>
            </section>

            <section class="sd-block" aria-labelledby="ph-inc">
                <header class="sd-block-head">
                    <span class="sd-block-tag">Qué incluye</span>
                    <h2 class="sd-block-title" id="ph-inc">{{ $servicio['name'] }} para su unidad en {{ $municipio['short'] }}</h2>
                </header>
                <ul class="ph-includes">
                    @foreach($servicio['includes'] as $item)
                        <li><span class="ph-includes__check"><x-ph-icon name="check" :size="16" /></span>{{ $item }}</li>
                    @endforeach
                </ul>
            </section>

            @include('pages.ph.partials.paquetes', [
                'destacado' => $servicio['paquete'] ?? null,
                'recomendadoLabel' => ! empty($servicio['paquete']) ? 'Recomendado para '.$servicio['short'] : null,
            ])

            @include('pages.ph.partials.proyectos', ['titulo' => 'Proyectos en '.$municipio['short']])

            @include('pages.ph.partials.faqs', ['faqs' => $faqs, 'titulo' => $servicio['name'].' en '.$municipio['short'].': preguntas frecuentes'])

            {{-- ═══ ENLAZADO INTERNO ═══ --}}
            <section class="sd-block" aria-labelledby="ph-rel">
                <header class="sd-block-head">
                    <span class="sd-block-tag">Siga explorando</span>
                    <h2 class="sd-block-title" id="ph-rel">Más soluciones para unidades residenciales</h2>
                </header>
                <div class="ph-related">
                    <div class="ph-related__col">
                        <h3>Otros servicios en {{ $municipio['short'] }}</h3>
                        <ul>
                            @foreach($otros as $oslug => $otro)
                                <li><a href="{{ url(PH::path($oslug, $municipio['slug'])) }}"><x-ph-icon :name="$otro['icon']" :size="18" /> {{ $otro['name'] }} en {{ $municipio['short'] }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="ph-related__col">
                        <h3>{{ $servicio['name'] }} en municipios vecinos</h3>
                        <ul>
                            @foreach($vecinos as $vecino)
                                <li><a href="{{ url(PH::path($servicio['slug'], $vecino['slug'])) }}"><x-ph-icon name="pin" :size="18" /> {{ $servicio['name'] }} en {{ $vecino['short'] }}</a></li>
                            @endforeach
                            <li><a href="{{ url(PH::BASE) }}#cobertura"><x-ph-icon name="building" :size="18" /> Todos los municipios</a></li>
                        </ul>
                    </div>
                </div>
            </section>
        </div>

        @include('pages.ph.partials.cta', [
            'servicioSlug' => $servicio['slug'],
            'municipioSlug' => $municipio['slug'],
            'titulo' => 'Diagnóstico gratuito para su unidad en '.$municipio['short'],
        ])

        <x-mobile-cta href="#diagnostico" :label="'Diagnóstico gratis en '.$municipio['short']" :wa-message="'Hola, administro una unidad residencial en '.$municipio['name'].' y quiero información sobre '.$servicio['short'].'.'" />
    </article>
</x-app-layout>
