<div class="project-portfolio">
    <div class="container">
        <x-section-header
            :tag="$featuredOnly ? 'Proyectos Recientes' : 'Portafolio'"
            :subtitle="$showHub
                ? 'Elige un servicio para ver las fotos de los trabajos realizados.'
                : 'Abre cada trabajo para ver las fotos de evidencia.'"
        >
            <x-slot:heading>
                <h2 class="section-title">
                    @if($featuredOnly)
                        Trabajos realizados en <span>Medellín</span> y el Valle de Aburrá
                    @elseif($activeService)
                        {{ $activeService->name }}
                    @else
                        Trabajos en <span>Medellín</span> y el Valle de Aburrá
                    @endif
                </h2>
            </x-slot:heading>
        </x-section-header>

        @include('components.projects-map', ['mapProjects' => $mapProjects])

        @if($showHub)
            <div class="project-service-hub" role="list">
                @foreach($hubCards as $card)
                    @if($card['href'])
                        @if($featuredOnly)
                            <a
                                href="{{ $card['href'] }}"
                                role="listitem"
                                class="project-service-hub-card"
                                wire:key="hub-{{ $card['key'] }}"
                                aria-label="Ver {{ $card['count'] }} {{ $card['count'] === 1 ? 'proyecto' : 'proyectos' }} de {{ $card['label'] }}"
                            >
                                <img src="{{ $card['cover'] }}" alt="" class="project-service-hub-cover" loading="lazy" />
                                <span class="project-service-hub-shade" aria-hidden="true"></span>
                                <span class="project-service-hub-icon">
                                    <x-service-icon :icon="$card['icon']" :name="$card['label']" size="md" />
                                </span>
                                <span class="project-service-hub-copy">
                                    <span class="project-service-hub-title">{{ $card['label'] }}</span>
                                    <span class="project-service-hub-meta">
                                        {{ $card['count'] }} {{ $card['count'] === 1 ? 'proyecto' : 'proyectos' }}
                                    </span>
                                </span>
                            </a>
                        @else
                            <button
                                type="button"
                                role="listitem"
                                class="project-service-hub-card"
                                wire:key="hub-{{ $card['key'] }}"
                                wire:click="selectService('{{ $card['key'] }}')"
                                aria-label="Ver {{ $card['count'] }} {{ $card['count'] === 1 ? 'proyecto' : 'proyectos' }} de {{ $card['label'] }}"
                            >
                                <img src="{{ $card['cover'] }}" alt="" class="project-service-hub-cover" loading="lazy" />
                                <span class="project-service-hub-shade" aria-hidden="true"></span>
                                <span class="project-service-hub-icon">
                                    <x-service-icon :icon="$card['icon']" :name="$card['label']" size="md" />
                                </span>
                                <span class="project-service-hub-copy">
                                    <span class="project-service-hub-title">{{ $card['label'] }}</span>
                                    <span class="project-service-hub-meta">
                                        {{ $card['count'] }} {{ $card['count'] === 1 ? 'proyecto' : 'proyectos' }}
                                    </span>
                                </span>
                            </button>
                        @endif
                    @else
                        <div
                            role="listitem"
                            class="project-service-hub-card is-soon"
                            wire:key="hub-{{ $card['key'] }}"
                            aria-label="{{ $card['label'] }}: próximamente"
                        >
                            <img src="{{ $card['cover'] }}" alt="" class="project-service-hub-cover" loading="lazy" />
                            <span class="project-service-hub-shade" aria-hidden="true"></span>
                            <span class="project-service-hub-icon">
                                <x-service-icon :icon="$card['icon']" :name="$card['label']" size="md" />
                            </span>
                            <span class="project-service-hub-copy">
                                <span class="project-service-hub-title">{{ $card['label'] }}</span>
                                <span class="project-service-hub-meta">Próximamente</span>
                            </span>
                        </div>
                    @endif
                @endforeach
            </div>
        @else
            <div class="project-service-detail">
                <button type="button" class="project-service-back" wire:click="clearService">
                    ← Volver a servicios
                </button>

                <header class="project-service-group-head">
                    <span class="project-service-group-icon">
                        <x-service-icon :icon="$activeService->icon" :name="$activeService->name" size="md" />
                    </span>
                    <div class="project-service-group-copy">
                        <h3 class="project-service-group-title">{{ $activeService->name }}</h3>
                        <span class="project-service-hub-meta">
                            {{ $projects->count() }} {{ $projects->count() === 1 ? 'proyecto' : 'proyectos' }}
                        </span>
                    </div>
                </header>

                @if($projects->isEmpty())
                    <div class="project-coming-soon" role="status">
                        <span class="project-coming-soon-badge">Próximamente</span>
                        <p>Pronto publicaremos trabajos de {{ $activeService->name }}.</p>
                    </div>
                @else
                    <div class="project-portfolio-grid" role="list">
                        @foreach($projects as $project)
                            <button
                                type="button"
                                role="listitem"
                                wire:click="openProject({{ $project->id }})"
                                wire:key="project-card-{{ $project->id }}"
                                class="project-card project-card-selectable has-image"
                                aria-label="Ver fotos de {{ $project->title }}"
                            >
                                <img src="{{ $project->image_url }}" alt="{{ $project->title }}" loading="lazy" />
                                <div class="project-overlay">
                                    <h3>{{ $project->title }}</h3>
                                    @if($project->location)
                                        <p class="project-card-location">{{ $project->location }}</p>
                                    @endif
                                </div>
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        @if($featuredOnly)
            <div class="projects-cta-row">
                <a href="{{ route('proyectos') }}" class="btn btn-ghost">Ver portafolio completo</a>
                <button type="button" class="btn btn-ghost" data-scroll-to="smartech-projects-map">
                    Volver al mapa
                </button>
            </div>
        @endif
    </div>

    @if($openProject)
        <div
            class="project-lightbox"
            wire:click="closeProject"
            wire:keydown.escape.window="closeProject"
            role="dialog"
            aria-modal="true"
            aria-label="Galería de {{ $openProject->title }}"
        >
            <div class="project-lightbox-panel" wire:click.stop>
                    <button type="button" class="project-lightbox-close" wire:click="closeProject" aria-label="Cerrar">
                        &times;
                    </button>

                    <div class="project-lightbox-header">
                        <span class="project-tag">{{ $openProject->service_name }}</span>
                        <h3>{{ $openProject->title }}</h3>
                        @if($openProject->address)
                            <p class="project-detail-address">{{ $openProject->address }}</p>
                        @elseif($openProject->location)
                            <p class="project-detail-address">{{ $openProject->location }}</p>
                        @endif
                    </div>

                    <div class="project-lightbox-body {{ $openProject->description ? 'has-aside' : '' }}">
                        @if(count($gallery) > 0)
                            <div class="project-lightbox-gallery">
                                @php
                                    $galleryCount = count($gallery);
                                    $hasSwipe = $galleryCount > 1;
                                    $prevImageIndex = $hasSwipe ? ($activeImage - 1 + $galleryCount) % $galleryCount : $activeImage;
                                    $nextImageIndex = $hasSwipe ? ($activeImage + 1) % $galleryCount : $activeImage;
                                @endphp
                                <div
                                    class="project-lightbox-stage {{ $hasSwipe ? 'has-swipe' : '' }}"
                                    @if($hasSwipe)
                                        x-data="projectLightboxSwipe()"
                                        @touchstart.passive="onStart($event)"
                                        @touchmove="onMove($event)"
                                        @touchend.passive="onEnd()"
                                        @touchcancel.passive="onEnd()"
                                    @endif
                                >
                                    @if($hasSwipe)
                                        <button type="button" class="project-lightbox-nav prev" wire:click="prevImage" aria-label="Foto anterior">‹</button>
                                    @endif

                                    @if($hasSwipe)
                                        <div
                                            class="project-lightbox-track"
                                            :class="{ 'is-dragging': dragging, 'is-settling': settling }"
                                            :style="trackStyle"
                                        >
                                            <div class="project-lightbox-slide" aria-hidden="true">
                                                <img
                                                    src="{{ $gallery[$prevImageIndex]['url'] }}"
                                                    alt=""
                                                    class="project-lightbox-main-image"
                                                    draggable="false"
                                                />
                                            </div>
                                            <div class="project-lightbox-slide">
                                                <img
                                                    src="{{ $gallery[$activeImage]['url'] }}"
                                                    alt="{{ $gallery[$activeImage]['caption'] ?? $openProject->title }}"
                                                    class="project-lightbox-main-image"
                                                    draggable="false"
                                                />
                                            </div>
                                            <div class="project-lightbox-slide" aria-hidden="true">
                                                <img
                                                    src="{{ $gallery[$nextImageIndex]['url'] }}"
                                                    alt=""
                                                    class="project-lightbox-main-image"
                                                    draggable="false"
                                                />
                                            </div>
                                        </div>
                                    @else
                                        <img
                                            src="{{ $gallery[$activeImage]['url'] }}"
                                            alt="{{ $gallery[$activeImage]['caption'] ?? $openProject->title }}"
                                            class="project-lightbox-main-image"
                                            draggable="false"
                                        />
                                    @endif

                                    @if($hasSwipe)
                                        <button type="button" class="project-lightbox-nav next" wire:click="nextImage" aria-label="Foto siguiente">›</button>
                                    @endif
                                </div>

                                @if(count($gallery) > 1)
                                    <div class="project-detail-thumbs project-lightbox-thumbs" role="tablist">
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
                        @else
                            <p class="project-detail-caption">Este proyecto aún no tiene fotos en la galería.</p>
                        @endif

                        @if($openProject->description)
                            <aside class="project-lightbox-aside">
                                <div class="project-desc-bubble">
                                    <span class="project-desc-bubble-label">Descripción</span>
                                    <p>{{ $openProject->description }}</p>
                                </div>
                                <a href="{{ url('/#contacto') }}" class="btn btn-primary project-lightbox-cta" wire:click="closeProject">
                                    Solicitar información
                                </a>
                            </aside>
                        @endif
                    </div>
                </div>
            </div>
    @endif
</div>
