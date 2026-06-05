<x-section
    tone="muted"
    tag="Área de Cobertura"
    title="Atendemos todo el"
    highlight="Valle de Aburrá y más allá"
    subtitle="Técnicos locales con respuesta rápida garantizada. Conocemos cada sector de Medellín y sus alrededores."
>
    <div class="coverage-grid">
        @foreach(config('site.coverage_zones') as $zone)
            <article class="coverage-card glass-card" data-aos="fade-up">
                <h3>{{ $zone['name'] }}</h3>
                <p>{{ $zone['areas'] }}</p>
            </article>
        @endforeach
    </div>
    <div class="coverage-cta">
        <p>¿Tu zona no aparece? Contáctanos de todos modos.</p>
        <a href="#contacto" class="btn btn-primary">Consultar disponibilidad</a>
    </div>
</x-section>
