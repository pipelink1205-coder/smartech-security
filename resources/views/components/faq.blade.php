<x-section
    tone="default"
    tag="Preguntas Frecuentes"
    title="Resolvemos tus"
    highlight="dudas"
    subtitle="Todo lo que necesitas saber sobre nuestros servicios en Medellín y el área metropolitana."
>
    <div class="faq-list">
        @foreach(config('site.faq') as $item)
            <details class="faq-item glass-card">
                <summary>{{ $item['q'] }}</summary>
                <p>{{ $item['a'] }}</p>
            </details>
        @endforeach
    </div>
</x-section>
