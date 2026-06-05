<x-section
    tone="muted"
    tag="Testimonios"
    title="Lo que dicen nuestros"
    highlight="clientes en Medellín"
    subtitle="Más de 500 familias, empresas y hoteles confían en nosotros."
>
    <div class="testimonials-grid">
        @foreach(config('site.testimonials') as $index => $t)
            <blockquote class="testimonial-card glass-card" data-aos="fade-up">
                <p class="testimonial-quote">"{{ $t['quote'] }}"</p>
                <footer class="testimonial-author">
                    <img
                        src="{{ config('images.testimonials.' . $index) }}"
                        alt="{{ $t['name'] }}"
                        class="testimonial-avatar"
                        loading="lazy"
                        width="48"
                        height="48"
                    />
                    <div>
                        <strong>{{ $t['name'] }}</strong>
                        <span>{{ $t['location'] }} · {{ $t['service'] }}</span>
                    </div>
                </footer>
            </blockquote>
        @endforeach
    </div>
</x-section>
