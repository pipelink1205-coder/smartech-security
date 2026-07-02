<x-app-layout :title="$service->name . ' – Smart Tech Security'">
    <article class="service-detail">
        <header class="service-detail-hero">
            <div class="container">
                <nav class="service-detail-breadcrumb" aria-label="Ruta de navegación">
                    <a href="{{ route('home') }}">Inicio</a>
                    <span aria-hidden="true">/</span>
                    <a href="{{ route('servicios') }}">Servicios</a>
                    <span aria-hidden="true">/</span>
                    <span>{{ $service->name }}</span>
                </nav>

                <div class="service-detail-hero-grid">
                    <div class="service-detail-hero-media glass-card">
                        <img src="{{ $service->image_url }}" alt="{{ $service->name }}" width="960" height="540" />
                    </div>
                    <div class="service-detail-hero-copy">
                        <span class="service-detail-icon" aria-hidden="true">{{ $service->icon }}</span>
                        <h1 class="service-detail-title">{{ $service->name }}</h1>
                        @if($service->highlight)
                            <p class="service-detail-tagline">{{ $service->highlight }}</p>
                        @endif
                        @if($service->price_from)
                            <p class="service-detail-price">Desde <strong>${{ number_format($service->price_from, 0, ',', '.') }}</strong> COP</p>
                        @endif
                        <div class="service-detail-hero-actions">
                            <a href="{{ route('contacto') }}" class="btn btn-primary">Solicitar cotización</a>
                            <a href="{{ route('servicios') }}" class="btn btn-ghost">Ver todos los servicios</a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        @if($service->long_description)
            <x-section tone="default" tag="Descripción" title="Qué es y para" highlight="quién es">
                <div class="service-detail-prose glass-card glass-card--pad">
                    @foreach(preg_split('/\n\s*\n/', trim($service->long_description)) as $paragraph)
                        @if(trim($paragraph) !== '')
                            <p>{{ trim($paragraph) }}</p>
                        @endif
                    @endforeach
                </div>
            </x-section>
        @endif

        @if($service->includes)
            <x-section tone="alt" tag="Entregables" title="Qué" highlight="incluye">
                <ul class="service-detail-list service-detail-list--check glass-card glass-card--pad">
                    @foreach($service->includes as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </x-section>
        @endif

        @if($service->process_steps)
            <x-section tone="default" tag="Proceso" title="Cómo lo" highlight="hacemos">
                <ol class="service-detail-steps">
                    @foreach($service->process_steps as $index => $step)
                        <li class="service-detail-step glass-card glass-card--pad">
                            <span class="service-detail-step-num">{{ $index + 1 }}</span>
                            <span class="service-detail-step-text">{{ $step }}</span>
                        </li>
                    @endforeach
                </ol>
            </x-section>
        @endif

        @if($service->brands || $service->standards || $service->tools)
            <x-section tone="alt" tag="Expertise" title="Marcas, normas y" highlight="herramientas">
                <div class="service-detail-meta-grid">
                    @if($service->brands)
                        <div class="service-detail-meta-block glass-card glass-card--pad">
                            <h3>Marcas con las que trabajamos</h3>
                            <ul class="service-detail-chips">
                                @foreach($service->brands as $brand)
                                    <li>{{ $brand }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if($service->standards)
                        <div class="service-detail-meta-block glass-card glass-card--pad">
                            <h3>Normas y estándares</h3>
                            <ul class="service-detail-list">
                                @foreach($service->standards as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if($service->tools)
                        <div class="service-detail-meta-block glass-card glass-card--pad">
                            <h3>Herramientas y equipos propios</h3>
                            <ul class="service-detail-list">
                                @foreach($service->tools as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </x-section>
        @endif

        <x-section tone="default" tag="Portafolio" title="Proyectos" highlight="realizados">
            @if($projects->isNotEmpty())
                <div class="service-detail-projects">
                    @foreach($projects as $project)
                        <a href="{{ route('proyectos') }}" class="service-detail-project glass-card">
                            <div class="service-detail-project-media">
                                <img src="{{ $project->image_url }}" alt="{{ $project->title }}" loading="lazy" width="400" height="260" />
                            </div>
                            <div class="service-detail-project-body">
                                <h3>{{ $project->title }}</h3>
                                @if($project->location)
                                    <p>{{ $project->location }}</p>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
                <p class="service-detail-projects-more">
                    <a href="{{ route('proyectos') }}" class="service-link">Ver portafolio completo →</a>
                </p>
            @else
                <p class="service-detail-empty glass-card glass-card--pad">
                    Pronto publicaremos casos de éxito de este servicio.
                </p>
            @endif
        </x-section>

        @if($service->faqs)
            <x-section tone="alt" tag="FAQ" title="Preguntas" highlight="frecuentes">
                <div class="service-detail-faqs">
                    @foreach($service->faqs as $faq)
                        <details class="service-detail-faq glass-card glass-card--pad">
                            <summary>{{ $faq['question'] ?? '' }}</summary>
                            <p>{{ $faq['answer'] ?? '' }}</p>
                        </details>
                    @endforeach
                </div>
            </x-section>
        @endif

        <section class="service-detail-cta">
            <div class="container">
                <div class="service-detail-cta-inner glass-card glass-card--pad">
                    <h2>¿Listo para empezar?</h2>
                    <p>Agenda tu diagnóstico gratuito y recibe una propuesta a la medida en Medellín y el Valle de Aburrá.</p>
                    <div class="service-detail-hero-actions">
                        <a href="{{ route('contacto') }}" class="btn btn-primary">Solicitar cotización</a>
                        <a href="https://wa.me/{{ config('contact.whatsapp') }}" class="btn btn-ghost" target="_blank" rel="noopener">WhatsApp</a>
                    </div>
                </div>
            </div>
        </section>
    </article>
</x-app-layout>
