{{-- Proyectos reales del portafolio (solo se muestra si hay coincidencias) --}}
@if($proyectos->isNotEmpty())
    <section class="sd-block" aria-labelledby="ph-proy">
        <header class="sd-block-head">
            <span class="sd-block-tag">Proyectos</span>
            <h2 class="sd-block-title" id="ph-proy">{{ $titulo ?? 'Instalaciones que hemos entregado' }}</h2>
        </header>
        <div class="ph-projects">
            @foreach($proyectos as $proyecto)
                <article class="ph-project">
                    <div class="ph-project__media">
                        <img src="{{ $proyecto->image_url }}" alt="{{ $proyecto->title }}" loading="lazy" width="480" height="300" onerror="this.closest('.ph-project__media').classList.add('is-empty');this.remove()" />
                    </div>
                    <div class="ph-project__body">
                        <span class="ph-project__cat">{{ $proyecto->service_name }}</span>
                        <h3>{{ $proyecto->title }}</h3>
                        @if($proyecto->location)
                            <p><x-ph-icon name="pin" :size="14" /> {{ $proyecto->location }}</p>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
        <p class="ph-more"><a href="{{ route('proyectos') }}" class="service-link">Ver todos los proyectos en el mapa →</a></p>
    </section>
@endif
