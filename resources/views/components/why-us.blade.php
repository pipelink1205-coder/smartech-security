<section class="why-us">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">¿Por qué elegirnos?</span>
            <h2 class="section-title">La mejor opción en <span>Medellín</span> y el Valle de Aburrá</h2>
        </div>
        <div class="why-grid">
            @foreach(config('site.why_us') as $item)
                <article class="why-card">
                    <div class="why-icon">{{ $item['icon'] }}</div>
                    <h3>{{ $item['title'] }}</h3>
                    <p>{{ $item['text'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>
