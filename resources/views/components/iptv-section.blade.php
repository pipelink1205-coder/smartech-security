@php
    $detailUrl = $iptvService
        ? route('servicios.show', $iptvService->slug)
        : route('servicios');

    $primarySrc = $iptvService?->image_url
        ?? (str_starts_with(config('images.iptv.primary'), 'http')
            ? config('images.iptv.primary')
            : asset(config('images.iptv.primary')));

    $secondary = config('images.iptv.secondary');
    $secondarySrc = str_starts_with($secondary, 'http') ? $secondary : asset($secondary);

    $featureIcons = ['📺', '🎬', '🏨', '📶'];
    $features = $iptvService?->features ?? [
        '+200 Canales HD',
        'Video On Demand',
        'Integración PMS',
        'WiFi por habitación',
    ];

    $subtitle = $iptvService?->description
        ?? 'Transforma la experiencia de tus huéspedes con televisión por internet HD. Especialistas en hoteles, hostales y apartahoteles en Medellín y el Valle de Aburrá.';
@endphp

<x-section
    tone="muted"
    tag="Producto estrella"
    title="Sistema IPTV para"
    highlight="Hoteles en Medellín"
    :subtitle="$subtitle"
>
    <div class="iptv-grid">
        <div class="iptv-img-wrap glass-card">
            <img src="{{ $primarySrc }}" alt="{{ $iptvService?->name ?? 'IPTV para hoteles' }} en Medellín" loading="lazy" />
        </div>
        <div>
            <div class="iptv-features">
                @foreach(array_slice($features, 0, 4) as $i => $feature)
                    <div class="iptv-feature glass-card glass-card--compact">
                        <div class="iptv-feature-icon">{{ $featureIcons[$i] ?? '📺' }}</div>
                        <div>
                            <div class="iptv-feature-title">{{ $feature }}</div>
                            @if($iptvService && $i === 0)
                                <div class="iptv-feature-desc">Nacionales e internacionales en alta definición</div>
                            @elseif($iptvService && $i === 1)
                                <div class="iptv-feature-desc">Películas y series para tus huéspedes</div>
                            @elseif($iptvService && $i === 2)
                                <div class="iptv-feature-desc">Integración con tu PMS y control de consumo</div>
                            @elseif($iptvService && $i === 3)
                                <div class="iptv-feature-desc">Red de alta velocidad para cada habitación</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="hero-btns iptv-hero-btns">
                <a href="{{ $detailUrl }}" class="btn btn-primary">Ver servicio completo</a>
                <a href="#contacto" class="btn btn-outline" data-page-link="contacto">Solicitar información IPTV</a>
            </div>
        </div>
    </div>
</x-section>

<x-section tone="alt" class="iptv-benefits-block">
    <div class="iptv-grid">
        <div>
            <h3 class="subsection-title">¿Por qué elegir nuestro IPTV para tu hotel?</h3>
            <p class="section-sub">
                {{ $iptvService?->highlight ?? 'Mayor calidad de imagen, interactividad y servicios personalizados frente al cable tradicional.' }}
            </p>
            <ul class="benefit-list glass-card glass-card--pad">
                <li><strong>Mayor rentabilidad:</strong> ingresos con Video On Demand y pay-per-view</li>
                <li><strong>Experiencia del huésped:</strong> interfaz con info del hotel y room service</li>
                <li><strong>Menor costo operativo:</strong> menos cableado coaxial y mantenimiento</li>
                <li><strong>Escalable:</strong> desde hostales hasta cadenas hoteleras</li>
            </ul>
            <div class="hero-btns">
                <a href="{{ $detailUrl }}" class="btn btn-outline">Ver ficha técnica y marcas</a>
                <a href="#contacto" class="btn btn-primary" data-page-link="contacto">Solicitar asesoría</a>
            </div>
        </div>
        <div class="iptv-img-wrap secondary glass-card">
            <img src="{{ $secondarySrc }}" alt="Hotel en Medellín con sistema de entretenimiento IPTV" loading="lazy" />
        </div>
    </div>
</x-section>
