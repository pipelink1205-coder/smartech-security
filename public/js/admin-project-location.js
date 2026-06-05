/**
 * Selector de ubicación en el panel admin (Filament).
 * Sincroniza latitud, longitud y dirección con el formulario Livewire.
 */
window.AdminProjectLocation = {
    map: null,
    marker: null,
    wire: null,

    defaultCenter: [6.2442, -75.5812],
    defaultZoom: 12,

    init(containerId, wire) {
        this.wire = wire;
        const el = document.getElementById(containerId);
        if (!el || typeof L === 'undefined') {
            return;
        }

        if (this.map) {
            this.map.remove();
            this.map = null;
            this.marker = null;
        }

        const lat = this.readNumber('latitude') ?? this.defaultCenter[0];
        const lng = this.readNumber('longitude') ?? this.defaultCenter[1];

        this.map = L.map(el, { scrollWheelZoom: true }).setView([lat, lng], this.defaultZoom);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap',
        }).addTo(this.map);

        this.marker = L.marker([lat, lng], { draggable: true }).addTo(this.map);

        this.marker.on('dragend', () => {
            const pos = this.marker.getLatLng();
            this.writeCoords(pos.lat, pos.lng);
        });

        this.map.on('click', (e) => {
            this.marker.setLatLng(e.latlng);
            this.writeCoords(e.latlng.lat, e.latlng.lng);
        });

        setTimeout(() => this.map.invalidateSize(), 200);
    },

    readNumber(field) {
        const v = this.wire?.get?.('data.' + field);
        const n = parseFloat(v);
        return Number.isFinite(n) ? n : null;
    },

    writeCoords(lat, lng) {
        if (!this.wire) {
            return;
        }
        this.wire.set('data.latitude', Math.round(lat * 1e6) / 1e6, false);
        this.wire.set('data.longitude', Math.round(lng * 1e6) / 1e6, false);
    },

    syncFromInputs() {
        const lat = this.readNumber('latitude');
        const lng = this.readNumber('longitude');
        if (lat == null || lng == null || !this.map || !this.marker) {
            return;
        }
        const pos = [lat, lng];
        this.marker.setLatLng(pos);
        this.map.panTo(pos);
    },

    async searchAddress() {
        const query = (this.wire?.get?.('data.address') || '').trim();
        if (!query) {
            alert('Escribe una dirección primero.');
            return;
        }

        const btn = document.getElementById('admin-location-search');
        if (btn) {
            btn.disabled = true;
        }

        try {
            const url =
                'https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' +
                encodeURIComponent(query + ', Medellín, Colombia');
            const res = await fetch(url, {
                headers: { Accept: 'application/json' },
            });
            const data = await res.json();
            if (!data?.length) {
                alert('No se encontró esa dirección. Haz clic en el mapa para ubicar el pin.');
                return;
            }
            const hit = data[0];
            const lat = parseFloat(hit.lat);
            const lng = parseFloat(hit.lon);
            this.writeCoords(lat, lng);
            if (hit.display_name) {
                this.wire.set('data.address', hit.display_name, false);
            }
            this.marker?.setLatLng([lat, lng]);
            this.map?.panTo([lat, lng]);
        } catch {
            alert('Error al buscar. Intenta de nuevo o marca el punto en el mapa.');
        } finally {
            if (btn) {
                btn.disabled = false;
            }
        }
    },
};
