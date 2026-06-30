{{-- CSS inline: @push('head') no llega al layout cuando el mapa vive dentro de Livewire --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />

<div class="projects-map-block" wire:ignore>
    <p class="projects-map-hint">
        Pasa el cursor sobre un <strong>pin</strong> para ver el trabajo y pulsa <strong>Ver</strong> para abrir las fotos. También puedes hacer clic en una <strong>comuna con trabajos</strong> (verde intenso).
    </p>
    <div id="smartech-projects-map" class="projects-map-canvas" aria-label="Mapa de trabajos en Medellín"></div>
</div>

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script>
        window.SMARTECH_MAP = {
            {{-- Rutas relativas: mismo protocolo/host que la página (evita mixed-content en HTTPS) --}}
            geoUrl: @json('/mapa/comunas.geojson'),
            logoUrl: @json('/images/logo.png'),
            projects: @json($mapProjects ?? []),
        };
    </script>
    <script src="{{ asset('js/projects-map.js') }}?v=4" defer></script>
@endpush
