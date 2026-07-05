<x-app-layout :title="$service->name . ' – Smart Tech Security'">
    <article class="sd">

        {{-- ═══ HERO OSCURO ═══ --}}
        <header class="sd-hero">
            <div class="container">
                <nav class="sd-breadcrumb" aria-label="Ruta de navegación">
                    <a href="{{ route('home') }}">Inicio</a>
                    <span aria-hidden="true">/</span>
                    <a href="{{ route('servicios') }}">Servicios</a>
                    <span aria-hidden="true">/</span>
                    <span aria-current="page">{{ $service->name }}</span>
                </nav>

                <div class="sd-hero-grid">
                    <div class="sd-hero-copy">
                        <span class="sd-hero-kicker">
                            <x-service-icon :icon="$service->icon" :name="$service->name" size="sm" class="sd-hero-kicker-icon" />
                            {{ $service->slug === 'iptv-hoteles' ? 'Producto estrella' : 'Servicio especializado' }}
                        </span>
                        <h1 class="sd-hero-title">{{ $service->name }}</h1>
                        @if($service->highlight)
                            <p class="sd-hero-tagline">{{ $service->highlight }}</p>
                        @endif
                        <ul class="sd-hero-badges">
                            <li>Visita técnica gratuita</li>
                            <li>Garantía 1 año</li>
                            <li>Medellín y Valle de Aburrá</li>
                        </ul>
                        <div class="sd-hero-actions">
                            <a href="{{ route('contacto') }}" class="btn btn-primary">Solicitar cotización</a>
                            @if($service->slug === 'iptv-hoteles')
                                <a href="{{ route('home') }}#iptv" class="btn sd-btn-outline">Ver presentación IPTV</a>
                            @endif
                            <a href="https://wa.me/{{ config('contact.whatsapp') }}" class="btn sd-btn-outline" target="_blank" rel="noopener">WhatsApp</a>
                        </div>
                    </div>

                    <figure class="sd-hero-media">
                        <img src="{{ $service->image_url }}" alt="{{ $service->name }}" width="880" height="550" fetchpriority="high" />
                        @if($service->price_from)
                            <figcaption class="sd-hero-price">
                                Desde <strong>${{ number_format($service->price_from, 0, ',', '.') }}</strong> COP
                            </figcaption>
                        @endif
                    </figure>
                </div>
            </div>
        </header>

        {{-- ═══ CONTENIDO + SIDEBAR ═══ --}}
        <div class="container sd-layout">
            <div class="sd-main">

                @if($service->long_description)
                    <section class="sd-block" aria-labelledby="sd-desc">
                        <header class="sd-block-head">
                            <span class="sd-block-tag">Descripción</span>
                            <h2 class="sd-block-title" id="sd-desc">Qué es y para quién</h2>
                        </header>
                        <div class="sd-prose">
                            @foreach(preg_split('/\n\s*\n/', trim($service->long_description)) as $paragraph)
                                @if(trim($paragraph) !== '')
                                    <p>{{ trim($paragraph) }}</p>
                                @endif
                            @endforeach
                        </div>
                    </section>
                @endif

                @if($service->includes)
                    <section class="sd-block" aria-labelledby="sd-inc">
                        <header class="sd-block-head">
                            <span class="sd-block-tag">Entregables</span>
                            <h2 class="sd-block-title" id="sd-inc">Qué incluye</h2>
                        </header>
                        <ul class="sd-includes">
                            @foreach($service->includes as $item)
                                <li class="sd-include">
                                    <span class="sd-check" aria-hidden="true">✓</span>
                                    <span>{{ $item }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif

                @if($service->process_steps)
                    <section class="sd-block" aria-labelledby="sd-proc">
                        <header class="sd-block-head">
                            <span class="sd-block-tag">Proceso</span>
                            <h2 class="sd-block-title" id="sd-proc">Cómo lo hacemos</h2>
                        </header>
                        <ol class="sd-steps">
                            @foreach($service->process_steps as $index => $step)
                                <li class="sd-step">
                                    <span class="sd-step-num" aria-hidden="true">{{ $index + 1 }}</span>
                                    <p class="sd-step-text">{{ $step }}</p>
                                </li>
                            @endforeach
                        </ol>
                    </section>
                @endif

                @if($service->brand_groups)
                    <section class="sd-block" aria-labelledby="sd-brands">
                        <header class="sd-block-head">
                            <span class="sd-block-tag">Marcas</span>
                            <h2 class="sd-block-title" id="sd-brands">Trabajamos con líderes del sector</h2>
                        </header>
                        @foreach($service->brand_groups as $group)
                            @if($group['label'])
                                <h3 class="sd-brand-group">{{ $group['label'] }}</h3>
                            @endif
                            <ul class="sd-brand-wall">
                                @foreach($group['brands'] as $brand)
                                    <x-brand-logo :brand="$brand" />
                                @endforeach
                            </ul>
                        @endforeach
                    </section>
                @endif

                @if($service->standards || $service->tools)
                    <section class="sd-block" aria-labelledby="sd-exp">
                        <header class="sd-block-head">
                            <span class="sd-block-tag">Expertise</span>
                            <h2 class="sd-block-title" id="sd-exp">Normas y herramientas</h2>
                        </header>
                        <div class="sd-duo">
                            @if($service->standards)
                                <div class="sd-duo-card glass-card glass-card--pad">
                                    <h3>Normas y estándares</h3>
                                    <ul class="sd-list">
                                        @foreach($service->standards as $item)
                                            <li>{{ $item }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if($service->tools)
                                <div class="sd-duo-card glass-card glass-card--pad">
                                    <h3>Herramientas y equipos propios</h3>
                                    <ul class="sd-list">
                                        @foreach($service->tools as $item)
                                            <li>{{ $item }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </section>
                @endif

                @if($projects->isNotEmpty())
                    <section class="sd-block" aria-labelledby="sd-proj">
                        <header class="sd-block-head">
                            <span class="sd-block-tag">Proyectos</span>
                            <h2 class="sd-block-title" id="sd-proj">Trabajos realizados</h2>
                        </header>
                        <div class="sd-projects">
                            @foreach($projects as $project)
                                <a href="{{ route('proyectos') }}" class="sd-project glass-card">
                                    <div class="sd-project-media">
                                        <img src="{{ $project->image_url }}" alt="{{ $project->title }}" loading="lazy" width="400" height="260" />
                                    </div>
                                    <div class="sd-project-body">
                                        <h3>{{ $project->title }}</h3>
                                        @if($project->location)
                                            <p>{{ $project->location }}</p>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        <p class="sd-projects-more">
                            <a href="{{ route('proyectos') }}" class="service-link">Ver todos los proyectos →</a>
                        </p>
                    </section>
                @endif

                @if($service->faqs)
                    <section class="sd-block" aria-labelledby="sd-faq">
                        <header class="sd-block-head">
                            <span class="sd-block-tag">FAQ</span>
                            <h2 class="sd-block-title" id="sd-faq">Preguntas frecuentes</h2>
                        </header>
                        <div class="sd-faqs">
                            @foreach($service->faqs as $faq)
                                <details class="sd-faq glass-card glass-card--pad">
                                    <summary>{{ $faq['question'] ?? '' }}</summary>
                                    <p>{{ $faq['answer'] ?? '' }}</p>
                                </details>
                            @endforeach
                        </div>
                    </section>
                @endif

            </div>

            {{-- ═══ SIDEBAR STICKY ═══ --}}
            <aside class="sd-aside">
                <div class="sd-quote glass-card">
                    @if($service->price_from)
                        <p class="sd-quote-label">Inversión</p>
                        <p class="sd-quote-price">Desde <strong>${{ number_format($service->price_from, 0, ',', '.') }}</strong> <span>COP</span></p>
                    @else
                        <p class="sd-quote-label">Cotización</p>
                        <p class="sd-quote-price"><strong>A la medida</strong></p>
                    @endif

                    <div class="sd-quote-actions">
                        <a href="{{ route('contacto') }}" class="btn btn-primary sd-btn-block">Solicitar cotización</a>
                        <a href="https://wa.me/{{ config('contact.whatsapp') }}" class="btn btn-ghost sd-btn-block" target="_blank" rel="noopener">Escríbenos por WhatsApp</a>
                    </div>

                    <ul class="sd-quote-trust">
                        <li>Visita técnica y diagnóstico gratuitos</li>
                        <li>Garantía de 1 año en equipos y mano de obra</li>
                        <li>{{ config('contact.support_note') }}</li>
                    </ul>

                    <p class="sd-quote-hours">{{ config('contact.hours') }}</p>
                </div>

                @if($otherServices->isNotEmpty())
                    <nav class="sd-others glass-card" aria-label="Otros servicios">
                        <h3>Otros servicios</h3>
                        <ul>
                            @foreach($otherServices as $other)
                                <li>
                                    <a href="{{ route('servicios.show', $other->slug) }}">
                                        <x-service-icon :icon="$other->icon" :name="$other->name" size="sm" class="sd-others__icon" />
                                        {{ $other->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </nav>
                @endif
            </aside>
        </div>

        {{-- ═══ CTA FINAL ═══ --}}
        <section class="sd-cta">
            <div class="container">
                <div class="sd-cta-inner">
                    <h2>¿Listo para empezar?</h2>
                    <p>Agenda tu diagnóstico gratuito y recibe una propuesta a la medida en Medellín y el Valle de Aburrá.</p>
                    <div class="sd-hero-actions">
                        <a href="{{ route('contacto') }}" class="btn btn-primary">Solicitar cotización</a>
                        <a href="https://wa.me/{{ config('contact.whatsapp') }}" class="btn sd-btn-outline" target="_blank" rel="noopener">WhatsApp</a>
                    </div>
                </div>
            </div>
        </section>

    </article>
</x-app-layout>
