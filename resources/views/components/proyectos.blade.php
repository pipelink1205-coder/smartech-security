<section class="proyectos" id="proyectos">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Proyectos Recientes</span>
            <h2 class="section-title">Trabajos realizados en <span>Medellín</span> y el Valle de Aburrá</h2>
            <p class="section-sub">Algunas de nuestras instalaciones más recientes. Cada proyecto es una referencia de calidad.</p>
        </div>
        <div class="projects-grid">
            @foreach($projects as $project)
                <article class="project-card has-image">
                    <img src="{{ $project->image_url }}" alt="{{ $project->title }}" loading="lazy" />
                    <div class="project-overlay">
                        <span class="project-tag">{{ $project->category }}</span>
                        <h3>{{ $project->title }}</h3>
                        <p>{{ $project->description }}</p>
                        <p class="project-meta">{{ $project->location }} · {{ $project->year }}</p>
                    </div>
                </article>
            @endforeach
        </div>
        <div class="projects-cta-row">
            <a href="{{ route('proyectos') }}" class="btn btn-outline-white">Ver todos los proyectos</a>
        </div>
    </div>
</section>
