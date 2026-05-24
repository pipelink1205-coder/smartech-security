@php
    $hero = config('images.hero');
    $heroSrc = str_starts_with($hero, 'http') ? $hero : asset($hero);
@endphp
<section class="hero">
    <div class="hero-pattern" aria-hidden="true"></div>
    <div class="container hero-grid">
        <div class="hero-content">
            <div class="hero-badge">{{ config('site.hero.badge') }}</div>
            <h1 class="hero-h1">{{ config('site.hero.title') }}</h1>
            <p class="hero-p">{{ config('site.hero.subtitle') }}</p>
            <div class="hero-btns">
                <a href="#servicios" class="btn btn-primary">Ver Servicios</a>
                <a href="#contacto" class="btn btn-outline">Diagnóstico Gratis</a>
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
            <div class="hero-img-wrap">
                <img src="{{ $heroSrc }}" alt="Instalación de cámaras de seguridad y sistemas tecnológicos en Medellín" loading="eager" />
            </div>
        </div>
    </div>
</section>
