/**
 * Mapa de trabajos — comunas + pines (logo) + enlace con Livewire
 */
(function () {
    const cfg = window.SMARTECH_MAP;
    const el = document.getElementById('smartech-projects-map');
    if (!cfg || !el || typeof L === 'undefined') return;

    const projects = Array.isArray(cfg.projects) ? cfg.projects : [];
    let map = null;
    let layerGeo = null;
    let layerLabels = null;
    let layerPins = null;
    let labelZoomRaf = null;
    let activeComunaLayer = null;

    const brand = {
        fill: '#d1fae5',
        fillActive: '#6ee7b7',
        fillCorreg: '#ecfdf5',
        stroke: '#178f82',
        strokeHover: '#0f766e',
        label: '#0f4c45',
    };

    function comunaProjectCount(numero) {
        return projects.filter((p) => p.comuna_numero === numero).length;
    }

    function estiloZona(feature, selected) {
        const correg = feature.properties.tipo === 'corregimiento';
        const cnt = comunaProjectCount(feature.properties.numero);
        const hasWork = cnt > 0;
        return {
            fillColor: selected
                ? brand.fillActive
                : hasWork
                  ? '#a7f3d0'
                  : correg
                    ? brand.fillCorreg
                    : brand.fill,
            fillOpacity: selected ? 0.5 : hasWork ? 0.62 : 0.45,
            color: selected || hasWork ? brand.strokeHover : brand.stroke,
            weight: selected ? 2.8 : hasWork ? 1.8 : 1.1,
            opacity: 0.95,
        };
    }

    function selectProject(id) {
        if (typeof Livewire !== 'undefined') {
            Livewire.dispatch('map-select-project', { id });
        }
        const panel = document.querySelector('.project-portfolio-detail');
        if (panel) {
            panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    function buildPinIcon() {
        return L.divIcon({
            className: 'smartech-project-pin',
            html: `<img src="${cfg.logoUrl}" alt="" width="28" height="28" />`,
            iconSize: [32, 32],
            iconAnchor: [16, 32],
            popupAnchor: [0, -28],
        });
    }

    function pintarPins() {
        if (layerPins) {
            map.removeLayer(layerPins);
            layerPins = null;
        }
        layerPins = L.layerGroup();
        projects.forEach((p) => {
            if (p.latitude == null || p.longitude == null) return;
            const marker = L.marker([p.latitude, p.longitude], {
                icon: buildPinIcon(),
                title: p.category,
            });
            const addr = p.address ? `<br><small>${p.address}</small>` : '';
            marker.bindPopup(
                `<strong>${p.category}</strong>${addr}<br>` +
                    `<button type="button" class="map-popup-btn" data-project-id="${p.id}">Ver evidencias →</button>`
            );
            marker.on('click', () => selectProject(p.id));
            marker.addTo(layerPins);
        });
        layerPins.addTo(map);
    }

    function highlightComuna(layer) {
        if (activeComunaLayer && activeComunaLayer !== layer) {
            layerGeo.resetStyle(activeComunaLayer);
        }
        activeComunaLayer = layer;
        layer.setStyle(estiloZona(layer.feature, true));
    }

    function onComunaClick(feature, layer) {
        const num = feature.properties.numero;
        const list = projects.filter((p) => p.comuna_numero === num);
        highlightComuna(layer);

        if (list.length === 0) {
            L.popup()
                .setLatLng(layer.getBounds().getCenter())
                .setContent('<em>Sin trabajos registrados en esta comuna aún.</em>')
                .openOn(map);
            return;
        }

        if (list.length === 1) {
            selectProject(list[0].id);
            return;
        }

        const items = list
            .map(
                (p) =>
                    `<button type="button" class="map-popup-btn" data-project-id="${p.id}">${p.category}</button>`
            )
            .join('');
        const popup = L.popup()
            .setLatLng(layer.getBounds().getCenter())
            .setContent(
                `<div class="map-popup-list"><p class="map-popup-title">${list.length} trabajos en esta comuna</p>${items}</div>`
            );
        popup.openOn(map);
    }

    function opacidadEtiquetaPorZoom(z) {
        if (z <= 12.15) return 1;
        if (z >= 15.05) return 0;
        const t = (z - 12.15) / (15.05 - 12.15);
        const s = t * t * (3 - 2 * t);
        return 1 - s;
    }

    function tamFuenteEtiqueta(z) {
        const raw = 7.2 + Math.max(0, z - 10) * 2.45;
        return Math.min(30, Math.max(7.5, raw));
    }

    function pintarLabels(geo) {
        if (!map) return;
        if (layerLabels) {
            map.removeLayer(layerLabels);
            layerLabels = null;
        }
        const z = map.getZoom();
        const fade = opacidadEtiquetaPorZoom(z);
        if (fade <= 0.02) return;

        const sizePx = tamFuenteEtiqueta(z);
        layerLabels = L.layerGroup();

        geo.features.forEach((f) => {
            const p = f.properties;
            if (p.clat == null || p.clon == null || p.numero == null) return;
            const num = String(p.numero);
            const icon = L.divIcon({
                className: 'smartech-map-label',
                html: `<span style="font-size:${sizePx}px;font-weight:700;color:${brand.label};opacity:${fade};text-shadow:0 1px 2px #fff">${num}</span>`,
                iconSize: [Math.ceil(num.length * sizePx * 0.55 + 10), Math.ceil(sizePx * 1.4)],
                iconAnchor: [0, 0],
            });
            L.marker([p.clat, p.clon], { icon, interactive: false }).addTo(layerLabels);
        });

        layerLabels.addTo(map);
    }

    function bindGeoLayer(geo) {
        layerGeo = L.geoJSON(geo, {
            style: (f) => estiloZona(f, false),
            onEachFeature: (f, lyr) => {
                const p = f.properties;
                const cnt = comunaProjectCount(p.numero);
                const titulo = p.nombre_corto || p.nombre || 'Zona';
                const tipo = p.tipo === 'corregimiento' ? 'Corregimiento' : 'Comuna';
                lyr.bindTooltip(
                    `<strong>${titulo}</strong><br>${tipo} ${p.numero ?? ''}` +
                        (cnt ? `<br><span style="color:#059669;font-weight:600">${cnt} trabajo${cnt > 1 ? 's' : ''}</span>` : ''),
                    { sticky: true, direction: 'top' }
                );
                lyr.on('click', () => onComunaClick(f, lyr));
                lyr.on('mouseover', () => {
                    if (activeComunaLayer !== lyr) {
                        lyr.setStyle({ fillOpacity: 0.72, weight: 2.2, color: brand.strokeHover });
                    }
                });
                lyr.on('mouseout', () => {
                    if (activeComunaLayer !== lyr) layerGeo.resetStyle(lyr);
                });
            },
        }).addTo(map);

        map.on('zoom', () => {
            if (labelZoomRaf != null) return;
            labelZoomRaf = requestAnimationFrame(() => {
                labelZoomRaf = null;
                pintarLabels(geo);
            });
        });
        map.on('zoomend', () => pintarLabels(geo));
        pintarLabels(geo);
    }

    el.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-project-id]');
        if (!btn) return;
        e.preventDefault();
        selectProject(parseInt(btn.dataset.projectId, 10));
        map.closePopup();
    });

    async function init() {
        map = L.map(el, { zoomControl: true, preferCanvas: true }).setView([6.2518, -75.5636], 11);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap',
            maxZoom: 19,
        }).addTo(map);

        try {
            const res = await fetch(cfg.geoUrl);
            if (!res.ok) throw new Error('GeoJSON no disponible');
            const geo = await res.json();
            bindGeoLayer(geo);
            pintarPins();
        } catch (e) {
            console.warn('[Smartech map]', e);
            el.insertAdjacentHTML(
                'beforeend',
                '<p class="projects-map-error">No se pudo cargar el mapa de comunas.</p>'
            );
        }

        setTimeout(() => map.invalidateSize(), 300);

        window.addEventListener('smartech-show-page', (event) => {
            if (event.detail?.pageId === 'proyectos' && map) {
                setTimeout(() => map.invalidateSize(), 250);
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
