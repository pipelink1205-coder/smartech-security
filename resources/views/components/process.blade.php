<section class="process">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Nuestro Proceso</span>
            <h2 class="section-title">Así trabajamos en <span>Smart Tech Security</span></h2>
            <p class="section-sub">Te acompañamos desde el primer contacto hasta el soporte postventa. Proceso transparente y profesional.</p>
        </div>
        <div class="process-grid">
            @foreach(config('site.process') as $step)
                <article class="process-card">
                    <div class="process-step">{{ $step['step'] }}</div>
                    <h3>{{ $step['title'] }}</h3>
                    <p>{{ $step['text'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>
