<x-section
    tone="default"
    tag="¿Por qué elegirnos?"
    title="La mejor opción en"
    highlight="Medellín y el Valle de Aburrá"
>
    <div class="why-grid">
        @foreach(config('site.why_us') as $item)
            <article class="why-card glass-card" data-aos="fade-up">
                <div class="why-icon">{{ $item['icon'] }}</div>
                <h3>{{ $item['title'] }}</h3>
                <p>{{ $item['text'] }}</p>
            </article>
        @endforeach
    </div>
</x-section>
