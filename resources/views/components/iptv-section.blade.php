@php
    $iptvPrimary = config('images.iptv.primary');
    $iptvSecondary = config('images.iptv.secondary');
    $iptvPrimarySrc = str_starts_with($iptvPrimary, 'http') ? $iptvPrimary : asset($iptvPrimary);
    $iptvSecondarySrc = str_starts_with($iptvSecondary, 'http') ? $iptvSecondary : asset($iptvSecondary);
@endphp
<section class="iptv-section" id="iptv">
    <div class="container iptv-grid">
        <div class="iptv-img-wrap">
            <img src="{{ $iptvPrimarySrc }}" alt="Sistema IPTV instalado en habitación de hotel en Medellín" loading="lazy" />
        </div>
        <div>
            <span class="section-tag">Servicio Especializado</span>
            <h2 class="section-title">Sistema IPTV para <span>Hoteles</span> en Medellín</h2>
            <p class="section-sub">Transforma la experiencia de tus huéspedes con televisión por internet HD. Especialistas en hoteles, hostales y apartahoteles en El Poblado, Laureles, Envigado y el área metropolitana.</p>
            <div class="iptv-features">
                <div class="iptv-feature">
                    <div class="iptv-feature-icon">📺</div>
                    <div>
                        <div class="iptv-feature-title">+200 Canales HD</div>
                        <div class="iptv-feature-desc">Nacionales e internacionales en alta definición</div>
                    </div>
                </div>
                <div class="iptv-feature">
                    <div class="iptv-feature-icon">🎬</div>
                    <div>
                        <div class="iptv-feature-title">Video On Demand</div>
                        <div class="iptv-feature-desc">Películas y series para tus huéspedes</div>
                    </div>
                </div>
                <div class="iptv-feature">
                    <div class="iptv-feature-icon">🏨</div>
                    <div>
                        <div class="iptv-feature-title">Facturación Hotelera</div>
                        <div class="iptv-feature-desc">Integración con tu PMS y control de consumo</div>
                    </div>
                </div>
                <div class="iptv-feature">
                    <div class="iptv-feature-icon">📶</div>
                    <div>
                        <div class="iptv-feature-title">WiFi Integrado</div>
                        <div class="iptv-feature-desc">Red de alta velocidad para cada habitación</div>
                    </div>
                </div>
            </div>
            <div class="hero-btns">
                <a href="#contacto" class="btn btn-primary">Cotizar IPTV para mi Hotel</a>
            </div>
        </div>
    </div>
</section>

<section class="iptv-benefits">
    <div class="container iptv-grid">
        <div>
            <h3 class="subsection-title">¿Por qué elegir nuestro IPTV para tu hotel?</h3>
            <p class="section-sub">Mayor calidad de imagen, interactividad y servicios personalizados frente al cable tradicional.</p>
            <ul class="benefit-list">
                <li><strong>Mayor rentabilidad:</strong> ingresos con Video On Demand y pay-per-view</li>
                <li><strong>Experiencia del huésped:</strong> interfaz con info del hotel y room service</li>
                <li><strong>Menor costo operativo:</strong> menos cableado coaxial y mantenimiento</li>
                <li><strong>Escalable:</strong> desde hostales hasta cadenas hoteleras</li>
            </ul>
            <a href="#contacto" class="btn btn-outline">Solicitar asesoría especializada</a>
        </div>
        <div class="iptv-img-wrap secondary">
            <img src="{{ $iptvSecondarySrc }}" alt="Hotel en Medellín con sistema de entretenimiento IPTV" loading="lazy" />
        </div>
    </div>
</section>
