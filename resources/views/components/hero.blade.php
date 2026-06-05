@php
    $hero = config('images.hero');
    $heroSrc = str_starts_with($hero, 'http') ? $hero : asset($hero);
@endphp

<x-section tone="hero" class="hero-block">
    <div class="hero-pattern" aria-hidden="true"></div>
    <div class="hero-grid">
        <div class="hero-content">
            <div class="hero-badge">{{ config('site.hero.badge') }}</div>
            <h1 class="hero-h1">{{ config('site.hero.title') }}</h1>
            <p class="hero-p">{{ config('site.hero.subtitle') }}</p>
            <div class="hero-btns">
                <a href="#servicios" class="btn btn-primary" data-page-link="servicios">Ver Servicios</a>
                <a href="#contacto" class="btn btn-outline" data-page-link="contacto">Diagnóstico Gratis</a>
            </div>
            <div class="hero-stats">
                @foreach(config('site.stats') as $stat)
                    <div>
                        <div class="hero-stat-num">{{ $stat['num'] }}</div>
                        <div class="hero-stat-label">{{ $stat['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="hero-image">
            <div class="hero-img-wrap glass-card">
                <img src="{{ $heroSrc }}" alt="Instalación de cámaras de seguridad y sistemas tecnológicos en Medellín" loading="eager" />
            </div>
        </div>
    </div>
</x-section>
