<div class="project-portfolio">
    <div class="container">
        <x-section-header
            :tag="$featuredOnly ? 'Proyectos Recientes' : 'Portafolio'"
            :subtitle="'Selecciona un servicio para ver la descripción y las fotos de evidencia del trabajo realizado.'"
        >
            <x-slot:heading>
                <h2 class="section-title">
                    @if($featuredOnly)
                        Trabajos realizados en <span>Medellín</span> y el Valle de Aburrá
                    @else
                        Trabajos en <span>Medellín</span> y el Valle de Aburrá
                    @endif
                </h2>
            </x-slot:heading>
        </x-section-header>

        @include('components.projects-map', ['mapProjects' => $mapProjects])

        <div class="project-portfolio-layout">
            <div class="project-portfolio-grid" role="list">
                @foreach($projects as $project)
                    <button
                        type="button"
                        role="listitem"
                        wire:click="selectProject({{ $project->id }})"
                        wire:key="project-card-{{ $project->id }}"
                        class="project-card project-card-selectable has-image {{ $selected && $selected->id === $project->id ? 'is-active' : '' }}"
                        aria-pressed="{{ $selected && $selected->id === $project->id ? 'true' : 'false' }}"
                        aria-label="Ver evidencias de {{ $project->category }}"
                    >
                        <img src="{{ $project->image_url }}" alt="{{ $project->category }}" loading="lazy" />
                        <div class="project-overlay">
                            <h3>{{ $project->category }}</h3>
                        </div>
                    </button>
                @endforeach
            </div>

            @if($selected)
                <aside class="project-portfolio-detail" aria-live="polite">
                    <h3 class="project-detail-title">{{ $selected->category }}</h3>
                    @if($selected->address)
                        <p class="project-detail-address">{{ $selected->address }}</p>
                    @endif
                    <p class="project-detail-desc">{{ $selected->description }}</p>

                    @if(count($gallery) > 0)
                        <div class="project-detail-gallery">
                            <div class="project-detail-main">
                                <img
                                    src="{{ $gallery[$activeImage]['url'] }}"
                                    alt="{{ $gallery[$activeImage]['caption'] ?? $selected->category }}"
                                    loading="lazy"
                                />
                            </div>
                            @if(count($gallery) > 1)
                                <div class="project-detail-thumbs" role="tablist">
                                    @foreach($gallery as $index => $item)
                                        <button
                                            type="button"
                                            role="tab"
                                            wire:click="selectImage({{ $index }})"
                                            class="project-detail-thumb {{ $activeImage === $index ? 'is-active' : '' }}"
                                            aria-selected="{{ $activeImage === $index ? 'true' : 'false' }}"
                                            aria-label="Foto {{ $index + 1 }}"
                                        >
                                            <img src="{{ $item['url'] }}" alt="" loading="lazy" />
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                            @if(!empty($gallery[$activeImage]['caption']))
                                <p class="project-detail-caption">{{ $gallery[$activeImage]['caption'] }}</p>
                            @endif
                        </div>
                    @endif
                </aside>
            @endif
        </div>

        @if($featuredOnly)
            <div class="projects-cta-row">
                <a href="{{ route('proyectos') }}" class="btn btn-outline-white">Ver todos los proyectos</a>
            </div>
        @endif
    </div>
</div>
