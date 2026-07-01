/**
 * Selector de ubicación en el panel admin (Filament).
 *
 * Dos gestores que se validan entre sí:
 *   1) Geocodificador (dirección → coordenadas): Alcaldía Medellín → ArcGIS → Nominatim.
 *   2) Espacial (coordenadas → comuna): punto-en-polígono en el servidor.
 * La comuna, el barrio y la zona se autocompletan; el usuario solo ajusta el pin si hace falta.
 */
window.AdminProjectLocation = {
    map: null,
    marker: null,
    wire: null,
    candidates: [],

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
            this.reverseLookup(pos.lat, pos.lng);
        });

        this.map.on('click', (e) => {
            this.marker.setLatLng(e.latlng);
            this.writeCoords(e.latlng.lat, e.latlng.lng);
            this.reverseLookup(e.latlng.lat, e.latlng.lng);
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
            // Filament puede no exponer wire.get() en vistas embebidas.
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
        if (!input || String(input.value) === String(value ?? '')) {
            return;
        }

        input.value = value ?? '';
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

    /** Escribe comuna, barrio y la zona detectada a partir del payload del servidor. */
    writeZone(payload) {
        this.writeField('comuna_numero', payload.comuna_numero ?? '');
        this.writeField('barrio', payload.barrio ?? '');
        this.writeField('location', this.composeLocation(payload));
    },

    composeLocation(payload) {
        if (payload.in_medellin) {
            if (payload.barrio) {
                return `${this.titleCase(payload.barrio)}, Medellín`;
            }
            if (payload.comuna_nombre) {
                return `${this.titleCase(payload.comuna_nombre)}, Medellín`;
            }
            return 'Medellín';
        }

        return payload.municipio || '';
    },

    titleCase(text) {
        return String(text)
            .toLocaleLowerCase('es-CO')
            .replace(/\b\p{L}/gu, (c) => c.toLocaleUpperCase('es-CO'));
    },

    updateCoordPreview(lat, lng) {
        const el = document.getElementById('admin-location-coords-preview');
        if (!el) {
            return;
        }

        el.textContent = (lat == null || lng == null) ? 'Sin ubicación' : `${lat}, ${lng}`;
    },

    setStatus(message, type = 'info') {
        const el = document.getElementById('admin-location-status');
        if (!el) {
            return;
        }

        el.hidden = !message;
        el.innerHTML = message;
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
            'Sin pin en el mapa. Busca por dirección o marca el punto manualmente.',
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
        this.writeField('comuna_numero', '');
        this.writeField('barrio', '');
        this.writeField('location', '');
        this.updateCoordPreview(null, null);

        if (this.map && this.marker) {
            this.marker.setLatLng(this.defaultCenter);
            this.map.setView(this.defaultCenter, this.defaultZoom);
        }

        this.setStatus('Ubicación quitada. Comuna, barrio y zona se vaciaron.', 'warning');
    },

    /** Frase de confianza + validación cruzada de comuna. */
    validationSummary(payload) {
        const fuente = {
            alcaldia: 'API Alcaldía de Medellín',
            arcgis: 'ArcGIS',
            nominatim: 'OpenStreetMap',
            photon: 'Photon',
            polygon: 'mapa de comunas',
        }[payload.source] || payload.source || 'mapa';

        const conf = {
            alta: '🟢 Confianza alta',
            media: '🟡 Confianza media',
            baja: '🔴 Confianza baja',
        }[payload.confidence] || '';

        const cc = payload.cross_check || {};
        let cruce = '';
        if (cc.match === true) {
            cruce = ' · ✅ La comuna del geocodificador coincide con el polígono.';
        } else if (cc.match === false) {
            cruce = ' · ⚠️ La comuna del geocodificador NO coincide con el polígono; se usó la del mapa. Revisa el pin.';
        }

        const zona = [];
        if (payload.comuna_numero) zona.push(`Comuna ${payload.comuna_numero}`);
        if (payload.barrio) zona.push(this.titleCase(payload.barrio));
        const zonaStr = zona.length ? ` Zona: ${zona.join(' · ')}.` : '';

        return `Ubicación por <strong>${fuente}</strong> (${conf}).${zonaStr}${cruce}`;
    },

    /** Aviso cuando la dirección aparece en varios municipios del Valle de Aburrá. */
    ambiguityMessage(payload) {
        const munis = (payload.municipios || []).map((m) => this.titleCase(m)).join(' y ');
        const botones = (this.candidates || [])
            .map((c, i) => {
                const nombre = c.municipio ? this.titleCase(c.municipio) : `Opción ${i + 1}`;
                return `<button type="button" class="admin-loc-cand" onclick="window.AdminProjectLocation.useCandidate(${i})">${nombre}</button>`;
            })
            .join(' ');

        return (
            `⚠️ Esta dirección aparece en varios municipios: <strong>${munis}</strong>. ` +
            `Elige el correcto o arrastra el pin manualmente.` +
            (botones ? `<div class="admin-loc-cands">${botones}</div>` : '')
        );
    },

    /** Coloca el pin en el candidato elegido y recalcula la zona. */
    useCandidate(index) {
        const candidate = (this.candidates || [])[index];
        if (!candidate) {
            return;
        }

        const lat = parseFloat(candidate.lat);
        const lng = parseFloat(candidate.lng);
        if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
            return;
        }

        this.writeCoords(lat, lng);
        this.marker?.setLatLng([lat, lng]);
        this.map?.setView([lat, lng], 16);
        this.reverseLookup(lat, lng);
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

        this.setStatus('Buscando dirección…', 'info');

        try {
            const base = window.SMARTECH_ADMIN_GEOCODE || '/admin/geocode';
            const url = `${base}?q=${encodeURIComponent(query)}`;
            const payload = await this.fetchJson(url);

            if (!payload || !Number.isFinite(parseFloat(payload.lat)) || !Number.isFinite(parseFloat(payload.lng))) {
                this.setStatus(
                    (payload && payload.message) ||
                        'No encontramos esa dirección. Coloca el pin manualmente en el mapa.',
                    'warning'
                );
                return;
            }

            const lat = parseFloat(payload.lat);
            const lng = parseFloat(payload.lng);

            this.writeCoords(lat, lng);
            this.writeZone(payload);
            this.marker?.setLatLng([lat, lng]);
            this.map?.setView([lat, lng], payload.in_medellin ? 16 : 14);

            this.candidates = Array.isArray(payload.candidates) ? payload.candidates : [];

            if (payload.ambiguous) {
                this.setStatus(this.ambiguityMessage(payload), 'warning');
            } else {
                const status = payload.confidence === 'baja' || payload.cross_check?.match === false ? 'warning' : 'success';
                this.setStatus(
                    `${this.validationSummary(payload)} Ajusta el pin si no cae en el sitio real.`,
                    status
                );
            }
        } catch {
            this.setStatus('Error al buscar. Marca el punto manualmente en el mapa.', 'warning');
        } finally {
            if (btn) {
                btn.disabled = false;
            }
        }
    },

    /** Recalcula comuna/barrio/zona cuando el pin se mueve a mano. */
    async reverseLookup(lat, lng) {
        this.setStatus('Verificando comuna del punto…', 'info');

        try {
            const base = window.SMARTECH_ADMIN_GEOCODE || '/admin/geocode';
            const url = `${base}?lat=${encodeURIComponent(lat)}&lng=${encodeURIComponent(lng)}`;
            const payload = await this.fetchJson(url);

            if (!payload) {
                this.setStatus('Pin movido. No se pudo determinar la comuna automáticamente.', 'warning');
                return;
            }

            this.writeZone(payload);

            if (payload.in_medellin) {
                const zona = [];
                if (payload.comuna_numero) zona.push(`Comuna ${payload.comuna_numero}`);
                if (payload.barrio) zona.push(this.titleCase(payload.barrio));
                this.setStatus(
                    `Pin colocado en Medellín. ${zona.length ? zona.join(' · ') + '.' : ''} Guarda para publicarlo.`,
                    'success'
                );
            } else {
                this.setStatus(
                    'Pin fuera de Medellín: comuna vacía. Escribe la zona manualmente si lo necesitas.',
                    'warning'
                );
            }
        } catch {
            this.setStatus('Pin movido. Error al verificar la comuna.', 'warning');
        }
    },

    async fetchJson(url) {
        const res = await fetch(url, {
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        });

        return res.json().catch(() => null);
    },
};
