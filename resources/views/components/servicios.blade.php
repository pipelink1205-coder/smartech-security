<section class="servicios" id="servicios">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Nuestros Servicios</span>
            <h2 class="section-title">Soluciones integrales de <span>seguridad y tecnología</span></h2>
            <p class="section-sub">Diseñamos e instalamos sistemas a la medida de tus necesidades, con equipos de última generación y garantía real en Medellín y área metropolitana.</p>
        </div>
        <div class="services-grid">
            @foreach($services as $service)
                <article class="service-card">
                    <div class="service-card-media">
                        <img src="{{ $service->image_url }}" alt="{{ $service->name }}" loading="lazy" width="800" height="480" />
                        <div class="service-card-media-overlay" aria-hidden="true"></div>
                    </div>
                    <div class="service-card-body">
                        <div class="service-icon">{{ $service->icon }}</div>
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
                        <a href="#contacto" class="service-link">Solicitar cotización →</a>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
