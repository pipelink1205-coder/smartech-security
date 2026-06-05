@php
    $mapId = 'admin-project-map-' . uniqid();
@endphp

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script src="{{ asset('js/admin-project-location.js') }}?v=1"></script>

<div class="space-y-3" wire:ignore>
    <p class="text-sm text-gray-600 dark:text-gray-400">
        <strong>Clic en el mapa</strong> o <strong>arrastra el pin</strong> para marcar dónde quedó el trabajo.
        Los números de latitud y longitud se actualizan solos.
    </p>

    <div class="flex flex-wrap gap-2">
        <button
            type="button"
            id="admin-location-search"
            class="fi-btn fi-btn-size-sm fi-color-gray justify-center gap-x-1.5 rounded-lg px-3 py-2 text-sm font-semibold shadow-sm ring-1 ring-gray-950/10 dark:ring-white/20 inline-flex items-center"
            onclick="window.AdminProjectLocation.searchAddress()"
        >
            Buscar dirección en el mapa
        </button>
        <button
            type="button"
            class="fi-btn fi-btn-size-sm justify-center gap-x-1.5 rounded-lg px-3 py-2 text-sm font-semibold ring-1 ring-gray-950/10 dark:ring-white/20 inline-flex items-center bg-transparent"
            onclick="window.AdminProjectLocation.syncFromInputs()"
        >
            Centrar según coordenadas
        </button>
    </div>

    <div
        id="{{ $mapId }}"
        class="rounded-xl border border-gray-200 dark:border-gray-700"
        style="height: 320px; width: 100%; z-index: 0;"
    ></div>
</div>

<script>
    (function () {
        const mapId = @js($mapId);

        function boot() {
            if (typeof L === 'undefined' || typeof window.AdminProjectLocation === 'undefined') {
                setTimeout(boot, 150);
                return;
            }
            const wire = @this;
            window.AdminProjectLocation.init(mapId, wire);
        }

        boot();

        document.addEventListener('livewire:navigated', boot);
    })();
</script>
