<section class="faq" id="faq">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Preguntas Frecuentes</span>
            <h2 class="section-title">Resolvemos tus <span>dudas</span></h2>
            <p class="section-sub">Todo lo que necesitas saber sobre nuestros servicios en Medellín y el área metropolitana.</p>
        </div>
        <div class="faq-list">
            @foreach(config('site.faq') as $item)
                <details class="faq-item">
                    <summary>{{ $item['q'] }}</summary>
                    <p>{{ $item['a'] }}</p>
                </details>
            @endforeach
        </div>
    </div>
</section>
