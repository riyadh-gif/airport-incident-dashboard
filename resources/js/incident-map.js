/**
 * Interactive Leaflet incident map.
 *
 * The MapController embeds its payload as JSON in a #incident-map-data script
 * tag and renders a #incident-map container. Gates with incidents become
 * circle markers whose size/color scale with the incident count; clicking a
 * marker opens a popup with the gate code, total, and a per-condition
 * breakdown (using IncidentCondition labels/colors supplied by the server).
 *
 * Tiles use CartoDB Positron (light) / dark_matter (dark) and swap live on the
 * `theme-changed` event and on initial load based on the current <html> class
 * (which dark-mode.js derives from localStorage / system preference).
 *
 * This module is a no-op on pages without a map container.
 */

import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import 'leaflet.markercluster';
import 'leaflet.markercluster/dist/MarkerCluster.css';
import 'leaflet.markercluster/dist/MarkerCluster.Default.css';

const TILES = {
    light: {
        // Satellite imagery (Esri World Imagery).
        url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
        attribution:
            'Tiles &copy; Esri &mdash; Source: Esri, Maxar, Earthstar Geographics, and the GIS User Community',
    },
    dark: {
        url: 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png',
        attribution:
            '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
    },
};

function isDark() {
    return document.documentElement.classList.contains('dark');
}

/**
 * Scale a marker radius from the incident count. Clamped so a single incident
 * is still visible and very busy gates don't dominate the map.
 */
function radiusForCount(count, maxCount) {
    const min = 8;
    const max = 26;
    if (maxCount <= 1) {
        return min + 6;
    }
    return min + Math.round(((max - min) * (count - 1)) / (maxCount - 1));
}

/** Pick a marker color from the dominant (highest-count) condition. */
function colorForGate(gate, conditionMeta) {
    let bestValue = null;
    let bestCount = -1;
    for (const meta of conditionMeta) {
        const c = gate.breakdown[meta.value] || 0;
        if (c > bestCount) {
            bestCount = c;
            bestValue = meta.value;
        }
    }
    const match = conditionMeta.find((m) => m.value === bestValue);
    return match ? match.color : '#dc2626';
}

function escapeHtml(value) {
    return String(value).replace(/[&<>"']/g, (ch) => {
        return {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;',
        }[ch];
    });
}

function popupHtml(gate, conditionMeta) {
    const rows = conditionMeta
        .filter((m) => (gate.breakdown[m.value] || 0) > 0)
        .map((m) => {
            const c = gate.breakdown[m.value] || 0;
            return `
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-top:4px;">
                    <span style="display:inline-flex;align-items:center;gap:6px;">
                        <span style="width:9px;height:9px;border-radius:9999px;background:${m.color};display:inline-block;"></span>
                        ${escapeHtml(m.label)}
                    </span>
                    <span style="font-weight:600;">${c}</span>
                </div>`;
        })
        .join('');

    return `
        <div style="min-width:170px;font-family:Figtree,system-ui,sans-serif;">
            <div style="font-weight:700;font-size:14px;">Gate ${escapeHtml(gate.gate)}</div>
            <div style="font-size:12px;opacity:0.75;margin-top:2px;">${gate.count} incident(s)</div>
            <div style="margin-top:8px;font-size:12px;">${rows}</div>
        </div>`;
}

function initIncidentMap() {
    const container = document.getElementById('incident-map');
    const dataEl = document.getElementById('incident-map-data');

    if (!container || !dataEl) {
        return;
    }

    let payload;
    try {
        payload = JSON.parse(dataEl.textContent);
    } catch (e) {
        return;
    }

    const { center, zoom } = payload.config;
    const gates = payload.gates || [];
    const conditionMeta = payload.conditionMeta || [];

    const map = L.map(container, {
        center,
        zoom,
        scrollWheelZoom: true,
    });

    // Active tile layer, swapped on theme change.
    let tileLayer = L.tileLayer(TILES[isDark() ? 'dark' : 'light'].url, {
        attribution: TILES.light.attribution,
        maxZoom: 19,
        subdomains: 'abcd',
    }).addTo(map);

    function applyTheme() {
        const theme = isDark() ? 'dark' : 'light';
        map.removeLayer(tileLayer);
        tileLayer = L.tileLayer(TILES[theme].url, {
            attribution: TILES[theme].attribution,
            maxZoom: 19,
            subdomains: 'abcd',
        }).addTo(map);
    }

    window.addEventListener('theme-changed', applyTheme);

    // Subtle center label marking the (fictional) airport.
    L.marker(center, {
        icon: L.divIcon({
            className: 'incident-map-airport-label',
            html: '<span>✈ Terminal</span>',
            iconSize: [90, 20],
            iconAnchor: [45, 10],
        }),
        interactive: false,
        keyboard: false,
    }).addTo(map);

    if (gates.length === 0) {
        return;
    }

    const maxCount = gates.reduce((m, g) => Math.max(m, g.count), 0);

    const cluster = L.markerClusterGroup({
        showCoverageOnHover: false,
        maxClusterRadius: 45,
    });

    const bounds = [];

    for (const gate of gates) {
        const marker = L.circleMarker([gate.lat, gate.lng], {
            radius: radiusForCount(gate.count, maxCount),
            color: '#ffffff',
            weight: 1.5,
            fillColor: colorForGate(gate, conditionMeta),
            fillOpacity: 0.85,
        });

        marker.bindPopup(popupHtml(gate, conditionMeta));
        marker.bindTooltip(`${gate.gate} · ${gate.count}`, {
            direction: 'top',
            offset: [0, -4],
        });

        cluster.addLayer(marker);
        bounds.push([gate.lat, gate.lng]);
    }

    map.addLayer(cluster);

    if (bounds.length > 1) {
        map.fitBounds(bounds, { padding: [40, 40], maxZoom: zoom + 1 });
    }

    // Leaflet needs a size recalculation once the container is laid out.
    setTimeout(() => map.invalidateSize(), 50);
}

document.addEventListener('DOMContentLoaded', initIncidentMap);
