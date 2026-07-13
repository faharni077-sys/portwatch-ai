@extends('layouts.app')
@section('title', 'Port Monitor')
@section('breadcrumb', 'PORT LOCATION DASHBOARD')

@section('content')

{{-- Header + Controls --}}
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-3">
    <div>
        <div class="pw-section-title" style="margin-bottom:4px;">
            <i class="bi bi-anchor me-2 text-cyan"></i>WORLD PORT MONITOR
        </div>
        <p style="color:var(--pw-text-dim);font-size:13px;margin:0;">
            Interactive global port map — {{ count($ports) }} ports indexed worldwide.
        </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <div style="position:relative;">
            <i class="bi bi-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--pw-text-dim);font-size:13px;"></i>
            <input type="text" id="searchPort" class="pw-input" placeholder="Search port or city..." style="padding-left:36px;width:220px;">
        </div>
        <select id="countryFilter" class="pw-select" style="width:200px;">
            <option value="">All Countries</option>
            @foreach($countries as $country)
                <option value="{{ $country->code }}">{{ $country->name }}</option>
            @endforeach
        </select>
        <button onclick="loadPorts()" class="btn-pw-primary">
            <i class="bi bi-funnel me-1"></i> Filter
        </button>
    </div>
</div>

{{-- Stats --}}
<div class="pw-stat-row mb-3">
    <div class="pw-card" style="padding:14px 18px;">
        <div class="pw-card-label">TOTAL PORTS</div>
        <div class="pw-card-value text-cyan" id="portTotalCount">{{ count($ports) }}</div>
    </div>
    <div class="pw-card" style="padding:14px 18px;">
        <div class="pw-card-label">SHOWN ON MAP</div>
        <div class="pw-card-value" id="portShownCount">—</div>
    </div>
    <div class="pw-card" style="padding:14px 18px;">
        <div class="pw-card-label">COUNTRIES</div>
        <div class="pw-card-value text-green">{{ count($countries) }}</div>
    </div>
    <div class="pw-card" style="padding:14px 18px;">
        <div class="pw-card-label">MAP STATUS</div>
        <div class="pw-card-value text-green" style="font-size:13px;display:flex;align-items:center;gap:6px;">
            <span class="dot-pulse" style="width:7px;height:7px;"></span> LIVE
        </div>
    </div>
</div>

{{-- Map + Sidebar grid --}}
<div style="display:grid;grid-template-columns:1fr 300px;gap:16px;min-height:580px;">

    {{-- Map --}}
    <div class="pw-card" style="padding:0;overflow:hidden;display:flex;flex-direction:column;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 18px;border-bottom:1px solid var(--pw-border);background:var(--pw-bg3);">
            <span style="font-size:11px;letter-spacing:2px;color:var(--pw-cyan);font-family:'JetBrains Mono',monospace;font-weight:700;">
                <i class="bi bi-map me-2"></i>GLOBAL PORT NETWORK
            </span>
            <span id="mapLoadingLabel" style="font-size:11px;color:var(--pw-text-dim);font-family:'JetBrains Mono',monospace;">READY</span>
        </div>
        <div id="portMap" style="flex:1;min-height:520px;"></div>
        <div style="padding:10px 18px;border-top:1px solid var(--pw-border);background:var(--pw-bg3);display:flex;justify-content:space-between;align-items:center;">
            <span id="mapCoords" style="font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--pw-text-dim);">LAT: — · LON: —</span>
            <span style="font-size:11px;color:var(--pw-text-dim);">© OpenStreetMap</span>
        </div>
    </div>

    {{-- Port list panel --}}
    <div class="pw-card" style="padding:0;display:flex;flex-direction:column;overflow:hidden;">
        <div style="padding:14px 16px;border-bottom:1px solid var(--pw-border);background:var(--pw-bg3);">
            <div style="font-size:11px;letter-spacing:2px;color:var(--pw-cyan);font-family:'JetBrains Mono',monospace;font-weight:700;">
                <i class="bi bi-list-ul me-2"></i>PORT LIST
            </div>
        </div>
        <div id="portList" style="overflow-y:auto;flex:1;padding:8px;">
            <div style="color:var(--pw-text-dim);font-size:13px;text-align:center;padding:40px 16px;">
                <i class="bi bi-anchor" style="font-size:32px;display:block;margin-bottom:10px;opacity:.4;"></i>
                Loading ports...
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
const portMap = L.map('portMap').setView([20, 0], 2);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap', maxZoom: 18
}).addTo(portMap);

portMap.on('mousemove', e => {
    document.getElementById('mapCoords').textContent =
        `LAT: ${e.latlng.lat.toFixed(4)} · LON: ${e.latlng.lng.toFixed(4)}`;
});

const portIcon = L.divIcon({
    className: '',
    html: `<div style="
        width:10px;height:10px;
        background:#29c5ff;
        border-radius:50%;
        border:2px solid rgba(41,197,255,.35);
        box-shadow:0 0 8px rgba(41,197,255,.5);
        cursor:pointer;
    "></div>`,
    iconSize: [10, 10], iconAnchor: [5, 5]
});

let markers = [];
let currentPorts = [];

function loadPorts() {
    const country = document.getElementById('countryFilter').value;
    const search  = document.getElementById('searchPort').value;
    const label   = document.getElementById('mapLoadingLabel');

    label.textContent = 'LOADING...';
    label.style.color = 'var(--pw-cyan)';

    markers.forEach(m => portMap.removeLayer(m));
    markers = [];

    fetch(`/api/ports?country=${encodeURIComponent(country)}&search=${encodeURIComponent(search)}`)
        .then(r => r.json())
        .then(data => {
            currentPorts = data;
            document.getElementById('portShownCount').textContent = data.length;
            renderPortList(data);

            data.forEach(p => {
                if (!p.latitude || !p.longitude) return;
                const m = L.marker([p.latitude, p.longitude], { icon: portIcon }).addTo(portMap);
                m.bindPopup(`
                    <div style="font-family:'JetBrains Mono',monospace;min-width:180px;">
                        <div style="color:#29c5ff;font-weight:700;font-size:13px;margin-bottom:6px;">
                            <i class="bi bi-anchor"></i> ${p.port_name}
                        </div>
                        <div style="color:#7a9ab8;font-size:12px;line-height:1.8;">
                            📍 ${p.city ?? '—'}<br>
                            🌍 ${p.country?.name ?? '—'}<br>
                            🗺 ${parseFloat(p.latitude).toFixed(4)}, ${parseFloat(p.longitude).toFixed(4)}
                        </div>
                    </div>
                `);
                markers.push(m);
            });

            if (data.length > 0 && country) {
                const first = data.find(p => p.latitude && p.longitude);
                if (first) portMap.flyTo([first.latitude, first.longitude], 6, { duration: 1 });
            }

            label.textContent = data.length + ' PORTS';
            label.style.color = 'var(--pw-green)';
        })
        .catch(() => { label.textContent = 'ERROR'; label.style.color = 'var(--pw-red)'; });
}

function renderPortList(ports) {
    const list = document.getElementById('portList');
    if (!ports.length) {
        list.innerHTML = `<div style="color:var(--pw-text-dim);font-size:13px;text-align:center;padding:40px 16px;">
            <i class="bi bi-exclamation-circle d-block mb-2" style="font-size:24px;"></i>No ports found</div>`;
        return;
    }
    list.innerHTML = ports.slice(0, 100).map((p, i) => `
        <div class="pw-port-list-item" onclick="flyToPort(${i})">
            <div class="pw-port-name">${p.port_name}</div>
            <div class="pw-port-meta">
                <i class="bi bi-geo-alt" style="color:var(--pw-cyan);"></i> ${p.city ?? '—'} ·
                <i class="bi bi-globe" style="color:var(--pw-green);"></i> ${p.country?.name ?? '—'}
            </div>
        </div>
    `).join('') + (ports.length > 100 ? `
        <div style="text-align:center;padding:10px;font-size:12px;color:var(--pw-text-dim);">
            + ${ports.length - 100} more ports on map
        </div>` : '');
}

function flyToPort(idx) {
    const p = currentPorts[idx];
    if (p?.latitude && p?.longitude) {
        portMap.flyTo([p.latitude, p.longitude], 8, { duration: 1 });
        markers[idx]?.openPopup();
    }
}

// Debounced search
let searchTimer;
document.getElementById('searchPort').addEventListener('input', () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(loadPorts, 400);
});
document.getElementById('countryFilter').addEventListener('change', loadPorts);

// Initial load
loadPorts();
</script>

<style>
.pw-port-list-item {
    padding: 10px 12px;
    border-radius: 8px;
    cursor: pointer;
    transition: .2s;
    border-bottom: 1px solid var(--pw-border);
}
.pw-port-list-item:hover { background: var(--pw-bg3); border-color: var(--pw-border2); }
.pw-port-name { font-size: 13px; font-weight: 600; color: #fff; margin-bottom: 3px; }
.pw-port-meta { font-size: 11px; color: var(--pw-text-dim); }
</style>
@endsection
