/**
 * Selector de ubicación en el panel admin (Filament).
 * La dirección escrita por el usuario no se modifica al geocodificar.
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
        const hasCoords = this.readNumber('latitude') != null && this.readNumber('longitude') != null;

        this.map = L.map(el, { scrollWheelZoom: true }).setView([lat, lng], this.defaultZoom);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap',
        }).addTo(this.map);

        this.marker = L.marker([lat, lng], { draggable: true }).addTo(this.map);

        this.marker.on('dragend', () => {
            const pos = this.marker.getLatLng();
            this.writeCoords(pos.lat, pos.lng);
            this.setStatus('Pin movido. Revisa las coordenadas abajo y guarda el proyecto.', 'success');
        });

        this.map.on('click', (e) => {
            this.marker.setLatLng(e.latlng);
            this.writeCoords(e.latlng.lat, e.latlng.lng);
            this.setStatus('Pin colocado en el mapa. Guarda el proyecto para publicarlo en el sitio.', 'success');
        });

        setTimeout(() => {
            this.map.invalidateSize();
            this.refreshStatus(hasCoords ? 'success' : 'muted');
        }, 200);
    },

    wirePath(field) {
        return `data.${field}`;
    },

    findInput(field) {
        const path = this.wirePath(field);
        const selectors = [
            `input[wire\\:model="${path}"]`,
            `input[wire\\:model\\.live="${path}"]`,
            `input[wire\\:model\\.live\\.debounce\\.500ms="${path}"]`,
            `input[wire\\:model*="${path}"]`,
            `textarea[wire\\:model="${path}"]`,
            `textarea[wire\\:model*="${path}"]`,
        ];

        for (const selector of selectors) {
            const el = document.querySelector(selector);
            if (el) {
                return el;
            }
        }

        return null;
    },

    readText(field) {
        try {
            const value = this.wire?.get?.(this.wirePath(field));
            if (value !== null && value !== undefined && String(value).trim() !== '') {
                return String(value).trim();
            }
        } catch (_) {
            // Filament 5 puede no exponer wire.get() en vistas embebidas.
        }

        const input = this.findInput(field);
        return (input?.value || '').trim();
    },

    readNumber(field) {
        try {
            const value = this.wire?.get?.(this.wirePath(field));
            const n = parseFloat(value);
            if (Number.isFinite(n)) {
                return n;
            }
        } catch (_) {
            // fallback al DOM
        }

        const input = this.findInput(field);
        const n = parseFloat(input?.value);
        return Number.isFinite(n) ? n : null;
    },

    writeField(field, value) {
        try {
            this.wire?.set?.(this.wirePath(field), value, false);
        } catch (_) {
            // fallback al DOM
        }

        const input = this.findInput(field);
        if (!input || String(input.value) === String(value)) {
            return;
        }

        input.value = value;
        input.dispatchEvent(new Event('input', { bubbles: true }));
        input.dispatchEvent(new Event('change', { bubbles: true }));
    },

    writeCoords(lat, lng) {
        const roundedLat = Math.round(lat * 1e6) / 1e6;
        const roundedLng = Math.round(lng * 1e6) / 1e6;
        this.writeField('latitude', roundedLat);
        this.writeField('longitude', roundedLng);
        this.updateCoordPreview(roundedLat, roundedLng);
    },

    updateCoordPreview(lat, lng) {
        const el = document.getElementById('admin-location-coords-preview');
        if (!el) {
            return;
        }

        if (lat == null || lng == null) {
            el.textContent = 'Sin ubicación';
            return;
        }

        el.textContent = `${lat}, ${lng}`;
    },

    setStatus(message, type = 'info') {
        const el = document.getElementById('admin-location-status');
        if (!el) {
            return;
        }

        el.hidden = !message;
        el.textContent = message;
        el.dataset.status = type;
    },

    refreshStatus(type = 'muted') {
        const lat = this.readNumber('latitude');
        const lng = this.readNumber('longitude');
        this.updateCoordPreview(lat, lng);

        if (lat != null && lng != null) {
            this.setStatus(
                `Pin listo (${lat}, ${lng}). Aparecerá en el mapa público al guardar.`,
                type === 'muted' ? 'success' : type
            );
            return;
        }

        this.setStatus(
            'Sin pin en el mapa. Ubica por dirección o marca el punto manualmente.',
            'muted'
        );
    },

    syncFromInputs() {
        const lat = this.readNumber('latitude');
        const lng = this.readNumber('longitude');
        if (lat == null || lng == null || !this.map || !this.marker) {
            this.setStatus('Completa latitud y longitud abajo, o ubica el pin en el mapa.', 'warning');
            return;
        }

        const pos = [lat, lng];
        this.marker.setLatLng(pos);
        this.map.panTo(pos);
        this.updateCoordPreview(lat, lng);
        this.setStatus('Mapa centrado en las coordenadas actuales.', 'info');
    },

    clearLocation() {
        this.writeField('latitude', '');
        this.writeField('longitude', '');
        this.updateCoordPreview(null, null);

        if (this.map && this.marker) {
            this.marker.setLatLng(this.defaultCenter);
            this.map.setView(this.defaultCenter, this.defaultZoom);
        }

        this.setStatus('Ubicación quitada del mapa. La dirección escrita arriba no se modificó.', 'warning');
    },

    async searchAddress() {
        const query = this.readText('address');
        if (!query) {
            this.setStatus('Escribe primero la dirección en el campo de arriba.', 'warning');
            return;
        }

        const btn = document.getElementById('admin-location-search');
        if (btn) {
            btn.disabled = true;
        }

        this.setStatus('Buscando en OpenStreetMap (Valle de Aburrá)…', 'info');

        try {
            const base = window.SMARTECH_ADMIN_GEOCODE || '/admin/geocode';
            const url = `${base}?q=${encodeURIComponent(query)}`;
            const res = await fetch(url, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            const payload = await res.json().catch(() => ({}));

            if (!res.ok) {
                this.setStatus(
                    payload.message ||
                        'No encontramos esa dirección. Coloca el pin manualmente en el mapa.',
                    'warning'
                );
                return;
            }

            const lat = parseFloat(payload.lat);
            const lng = parseFloat(payload.lng);
            if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
                this.setStatus('Respuesta inválida del geocodificador. Marca el pin a mano.', 'warning');
                return;
            }

            this.writeCoords(lat, lng);
            this.marker?.setLatLng([lat, lng]);
            this.map?.panTo([lat, lng]);

            const ref = payload.label ? ` Referencia: ${payload.label}.` : '';
            this.setStatus(
                `Ubicación aproximada encontrada (${payload.source || 'mapa'}).${ref} Ajusta el pin si no coincide con el sitio real.`,
                'success'
            );
        } catch {
            this.setStatus('Error al buscar. Marca el punto manualmente en el mapa.', 'warning');
        } finally {
            if (btn) {
                btn.disabled = false;
            }
        }
    },
};
