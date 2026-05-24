<section class="cobertura" id="cobertura">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Área de Cobertura</span>
            <h2 class="section-title">Atendemos todo el <span>Valle de Aburrá</span> y más allá</h2>
            <p class="section-sub">Técnicos locales con respuesta rápida garantizada. Conocemos cada sector de Medellín y sus alrededores.</p>
        </div>
        <div class="coverage-grid">
            @foreach(config('site.coverage_zones') as $zone)
                <article class="coverage-card">
                    <h3>{{ $zone['name'] }}</h3>
                    <p>{{ $zone['areas'] }}</p>
                </article>
            @endforeach
        </div>
        <div class="coverage-cta">
            <p>¿Tu zona no aparece? Contáctanos de todos modos.</p>
            <a href="#contacto" class="btn btn-primary">Consultar disponibilidad</a>
        </div>
    </div>
</section>
