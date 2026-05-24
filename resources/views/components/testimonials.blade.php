<section class="testimonials" id="testimonios">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Testimonios</span>
            <h2 class="section-title">Lo que dicen nuestros <span>clientes</span> en Medellín</h2>
            <p class="section-sub">Más de 500 familias, empresas y hoteles confían en nosotros.</p>
        </div>
        <div class="testimonials-grid">
            @foreach(config('site.testimonials') as $index => $t)
                <blockquote class="testimonial-card">
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
    </div>
</section>
