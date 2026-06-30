@php
    $mapId = 'admin-project-map-' . uniqid();
@endphp

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

<style>
    .admin-location-picker { font-size: 0.8125rem; }
    .admin-location-toolbar { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 0.625rem; }
    .admin-location-status {
        margin-bottom: 0.625rem; padding: 0.5rem 0.75rem; border-radius: 0.5rem;
        font-size: 0.8125rem; line-height: 1.4; border: 1px solid transparent;
    }
    .admin-location-status[data-status="success"] { background: rgb(236 253 245); color: rgb(6 95 70); border-color: rgb(167 243 208); }
    .admin-location-status[data-status="warning"] { background: rgb(255 251 235); color: rgb(146 64 14); border-color: rgb(253 230 138); }
    .admin-location-status[data-status="info"],
    .admin-location-status[data-status="muted"] { background: rgb(239 246 255); color: rgb(30 64 175); border-color: rgb(191 219 254); }
    .dark .admin-location-status[data-status="success"] { background: rgb(6 78 59 / 0.35); color: rgb(167 243 208); }
    .dark .admin-location-status[data-status="warning"] { background: rgb(120 53 15 / 0.35); color: rgb(253 230 138); }
    .dark .admin-location-status[data-status="info"],
    .dark .admin-location-status[data-status="muted"] { background: rgb(30 58 138 / 0.35); color: rgb(191 219 254); }
    .admin-location-meta {
        display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center;
        margin-bottom: 0.625rem; color: rgb(107 114 128); font-size: 0.8125rem;
    }
    .admin-location-meta strong { color: rgb(17 24 39); font-weight: 600; }
    .dark .admin-location-meta strong { color: rgb(229 231 235); }
    .admin-location-map {
        height: 280px; width: 100%; border-radius: 0.65rem;
        border: 1px solid rgb(229 231 235); z-index: 0;
    }
    .dark .admin-location-map { border-color: rgb(55 65 81); }
</style>

<div class="admin-location-picker" wire:ignore>
    <div class="admin-location-toolbar">
        <button
            type="button"
            id="admin-location-search"
            class="fi-btn fi-btn-size-sm fi-color-primary justify-center rounded-lg px-3 py-2 text-sm font-semibold shadow-sm inline-flex items-center"
            onclick="window.AdminProjectLocation.searchAddress()"
        >
            Ubicar pin
        </button>
        <button
            type="button"
            class="fi-btn fi-btn-size-sm justify-center rounded-lg px-3 py-2 text-sm font-semibold ring-1 ring-gray-950/10 dark:ring-white/20 inline-flex items-center bg-transparent"
            onclick="window.AdminProjectLocation.syncFromInputs()"
        >
            Centrar mapa
        </button>
        <button
            type="button"
            class="fi-btn fi-btn-size-sm justify-center rounded-lg px-3 py-2 text-sm font-semibold ring-1 ring-gray-950/10 dark:ring-white/20 inline-flex items-center bg-transparent"
            onclick="window.AdminProjectLocation.clearLocation()"
        >
            Quitar pin
        </button>
    </div>

    <div id="admin-location-status" class="admin-location-status" data-status="muted" hidden></div>

    <p class="admin-location-meta">
        <span>Pin: <strong id="admin-location-coords-preview">Sin ubicación</strong></span>
        <span>· Clic o arrastre en el mapa · OpenStreetMap (referencia aproximada)</span>
    </p>

    <div id="{{ $mapId }}" class="admin-location-map" aria-label="Mapa para ubicar el proyecto"></div>
</div>

<script>
    window.SMARTECH_ADMIN_GEOCODE = @json(route('admin.geocode'));
</script>

<script>
    (function () {
        const mapId = @js($mapId);

        function boot() {
            if (typeof L === 'undefined' || typeof window.AdminProjectLocation === 'undefined') {
                setTimeout(boot, 150);
                return;
            }
            window.AdminProjectLocation.init(mapId, @this);
        }

        boot();
        document.addEventListener('livewire:navigated', boot);
    })();
</script>
