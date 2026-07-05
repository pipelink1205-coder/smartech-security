<x-section
    tone="alt"
    tag="Nuestros Servicios"
    title="Soluciones integrales de"
    highlight="seguridad y tecnología"
    subtitle="Diseñamos e instalamos sistemas a la medida de tus necesidades, con equipos de última generación y garantía real en Medellín y área metropolitana."
>
    <div class="services-grid">
        @foreach($services as $service)
            @php
                $isFeatured = $service->slug === 'iptv-hoteles';
                $iptvLandingUrl = request()->routeIs('home') ? '#iptv' : route('home') . '#iptv';
            @endphp
            <article @class(['service-card', 'glass-card', 'service-card--featured' => $isFeatured]) data-aos="fade-up">
                @if($isFeatured)
                    <span class="service-card-badge">Producto estrella</span>
                @endif
                <div class="service-card-media">
                    <img src="{{ $service->image_url }}" alt="{{ $service->name }}" loading="lazy" width="800" height="480" />
                    <div class="service-card-media-overlay" aria-hidden="true"></div>
                </div>
                <div class="service-card-body">
                    <div class="service-icon">
                        <x-service-icon :icon="$service->icon" :name="$service->name" size="lg" />
                    </div>
                    <h3>{{ $service->name }}</h3>
                    <p>{{ $service->description }}</p>
                    @if($service->features)
                        <ul class="service-features">
                            @foreach($service->features as $feature)
                                <li>{{ $feature }}</li>
                            @endforeach
                        </ul>
                    @endif
                    @if($service->highlight)
                        <span class="service-highlight">{{ $service->highlight }}</span>
                    @endif
                    <div class="service-card-actions">
                        <a href="{{ route('servicios.show', $service->slug) }}" class="service-link">Ver servicio →</a>
                        @if($isFeatured)
                            <a href="{{ $iptvLandingUrl }}" class="service-link service-link--muted" @if(request()->routeIs('home')) data-page-link="iptv" @endif>Presentación IPTV</a>
                        @endif
                        <a href="{{ route('contacto') }}" class="service-link service-link--muted">Cotizar</a>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
</x-section>
