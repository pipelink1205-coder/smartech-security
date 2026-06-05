<x-section
    tone="alt"
    tag="Nuestro Proceso"
    title="Así trabajamos en"
    highlight="Smart Tech Security"
    subtitle="Te acompañamos desde el primer contacto hasta el soporte postventa. Proceso transparente y profesional."
>
    <div class="process-grid">
        @foreach(config('site.process') as $step)
            <article class="process-card glass-card" data-aos="fade-up">
                <div class="process-step">{{ $step['step'] }}</div>
                <h3>{{ $step['title'] }}</h3>
                <p>{{ $step['text'] }}</p>
            </article>
        @endforeach
    </div>
</x-section>
