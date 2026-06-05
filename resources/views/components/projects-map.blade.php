@push('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
@endpush

<div class="projects-map-block" wire:ignore>
    <p class="projects-map-hint">
        Haz clic en una <strong>comuna con trabajos</strong> (verde intenso) o en un <strong>pin</strong> para ver descripción y fotos de evidencia.
    </p>
    <div id="smartech-projects-map" class="projects-map-canvas" aria-label="Mapa de trabajos en Medellín"></div>
</div>

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script>
        window.SMARTECH_MAP = {
            geoUrl: @json(asset('data/comunas-medellin.geojson')),
            logoUrl: @json(asset('images/logo.png')),
            projects: @json($mapProjects ?? []),
        };
    </script>
    <script src="{{ asset('js/projects-map.js') }}?v=2" defer></script>
@endpush
