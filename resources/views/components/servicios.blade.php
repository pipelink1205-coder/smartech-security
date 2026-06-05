<x-section
    tone="alt"
    tag="Nuestros Servicios"
    title="Soluciones integrales de"
    highlight="seguridad y tecnología"
    subtitle="Diseñamos e instalamos sistemas a la medida de tus necesidades, con equipos de última generación y garantía real en Medellín y área metropolitana."
>
    <div class="services-grid">
        @foreach($services as $service)
            <article class="service-card glass-card" data-aos="fade-up">
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
</x-section>
