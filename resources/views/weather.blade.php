@extends('layouts.app')
@section('title', 'Weather Monitor')
@section('breadcrumb', 'GLOBAL WEATHER MONITORING')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <div class="pw-section-title" style="margin-bottom:4px;">
            <i class="bi bi-cloud-lightning-rain me-2 text-cyan"></i>WEATHER INTELLIGENCE
        </div>
        <p style="color:var(--pw-text-dim);font-size:13px;margin:0;">
            Real-time weather monitoring via Open-Meteo API. Select a country to load conditions.
        </p>
    </div>
</div>

{{-- Search bar --}}
<div class="pw-card mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-md-4">
            <label style="font-size:11px;letter-spacing:1.5px;color:var(--pw-text-dim);font-family:'JetBrains Mono',monospace;">COUNTRY</label>
            <input type="text" id="countryInput" class="pw-input mt-1" placeholder="e.g. Germany, Indonesia, China..." autocomplete="off">
            <div id="countrySuggestions" style="display:none;position:absolute;z-index:999;background:var(--pw-bg2);border:1px solid var(--pw-border2);border-radius:10px;width:300px;margin-top:4px;box-shadow:0 8px 24px rgba(0,0,0,.5);"></div>
        </div>
        <div class="col-md-3">
            <label style="font-size:11px;letter-spacing:1.5px;color:var(--pw-text-dim);font-family:'JetBrains Mono',monospace;">LATITUDE</label>
            <input type="number" id="latInput" class="pw-input mt-1" placeholder="e.g. 52.5" step="0.01">
        </div>
        <div class="col-md-3">
            <label style="font-size:11px;letter-spacing:1.5px;color:var(--pw-text-dim);font-family:'JetBrains Mono',monospace;">LONGITUDE</label>
            <input type="number" id="lonInput" class="pw-input mt-1" placeholder="e.g. 13.4" step="0.01">
        </div>
        <div class="col-md-2">
            <button class="btn-pw-primary w-100" onclick="fetchWeather()">
                <i class="bi bi-cloud-download me-1"></i> Fetch
            </button>
        </div>
    </div>
</div>

{{-- Quick country buttons --}}
<div class="mb-4">
    <div style="font-size:11px;letter-spacing:2px;color:var(--pw-text-dim);font-family:'JetBrains Mono',monospace;margin-bottom:10px;">QUICK SELECT</div>
    <div style="display:flex;flex-wrap:wrap;gap:8px;">
        @foreach([
            ['Germany',   52.52,  13.41],
            ['China',     39.91, 116.39],
            ['Indonesia', -6.21, 106.85],
            ['Australia',-33.87, 151.21],
            ['Japan',     35.68, 139.69],
            ['USA',       40.71, -74.01],
            ['India',     28.61,  77.21],
            ['Brazil',   -15.78, -47.93],
        ] as [$name, $lat, $lon])
        <button class="pw-quick-btn" onclick="quickWeather('{{ $name }}', {{ $lat }}, {{ $lon }})">
            <i class="bi bi-geo-alt-fill me-1" style="color:var(--pw-cyan);"></i>{{ $name }}
        </button>
        @endforeach
    </div>
</div>

{{-- Results grid --}}
<div id="weatherResults" style="display:none;">

    {{-- Current Conditions --}}
    <div id="locationLabel" style="font-size:11px;letter-spacing:3px;color:var(--pw-cyan);font-family:'JetBrains Mono',monospace;margin-bottom:14px;"></div>

    <div class="pw-stat-row mb-4">
        <div class="pw-card glow-cyan text-center" style="padding:24px;">
            <div style="font-size:48px;margin-bottom:4px;">🌡</div>
            <div class="pw-card-label">TEMPERATURE</div>
            <div class="pw-card-value text-cyan" id="wTemp" style="font-size:40px;">—</div>
            <div class="pw-card-sub">°C · Current</div>
        </div>
        <div class="pw-card text-center" style="padding:24px;">
            <div style="font-size:48px;margin-bottom:4px;">💨</div>
            <div class="pw-card-label">WIND SPEED</div>
            <div class="pw-card-value text-green" id="wWind" style="font-size:40px;">—</div>
            <div class="pw-card-sub">km/h · 10m height</div>
        </div>
        <div class="pw-card text-center" style="padding:24px;">
            <div style="font-size:48px;margin-bottom:4px;">⚡</div>
            <div class="pw-card-label">STORM RISK</div>
            <div class="pw-card-value" id="wStorm" style="font-size:28px;">—</div>
            <div class="pw-card-sub" id="wStormSub">Based on wind speed</div>
        </div>
        <div class="pw-card text-center" style="padding:24px;">
            <div style="font-size:48px;margin-bottom:4px;">🌊</div>
            <div class="pw-card-label">WEATHER RISK</div>
            <div class="pw-card-value" id="wRisk" style="font-size:28px;">—</div>
            <div class="pw-card-sub">Supply chain impact</div>
        </div>
    </div>

    {{-- Map + hourly --}}
    <div style="display:grid;grid-template-columns:1fr 360px;gap:16px;">

        <div class="pw-card" style="padding:0;overflow:hidden;">
            <div style="padding:12px 18px;border-bottom:1px solid var(--pw-border);background:var(--pw-bg3);font-size:11px;letter-spacing:2px;color:var(--pw-cyan);font-family:'JetBrains Mono',monospace;">
                <i class="bi bi-map me-2"></i>WEATHER MAP
            </div>
            <div id="weatherMap" style="height:380px;"></div>
        </div>

        <div class="pw-card">
            <div class="pw-section-title"><i class="bi bi-clock me-2 text-cyan"></i>FORECAST · NEXT 6H</div>
            <div id="forecastList">
                <div style="color:var(--pw-text-dim);text-align:center;padding:20px;font-size:13px;">
                    Fetch weather to see forecast
                </div>
            </div>

            <div class="pw-section-title mt-4"><i class="bi bi-shield-shaded me-2 text-cyan"></i>RISK ASSESSMENT</div>
            <div id="riskAssessment" style="font-family:'JetBrains Mono',monospace;font-size:12px;line-height:2;color:var(--pw-text-dim);">
                Awaiting data...
            </div>
        </div>
    </div>

    {{-- Temp chart --}}
    <div class="pw-card mt-4">
        <div class="pw-section-title"><i class="bi bi-graph-up me-2 text-cyan"></i>TEMPERATURE TREND · 24H FORECAST</div>
        <div class="pw-chart-wrap-lg">
            <canvas id="tempChart"></canvas>
        </div>
    </div>

</div>

{{-- Loading --}}
<div id="weatherLoading" style="display:none;text-align:center;padding:60px;color:var(--pw-text-dim);">
    <div style="font-size:32px;margin-bottom:12px;" id="loadingSpinner">⟳</div>
    <div style="font-family:'JetBrains Mono',monospace;font-size:13px;">FETCHING WEATHER INTELLIGENCE...</div>
</div>

@endsection

@section('scripts')
<script>
let weatherMapObj = null;
let weatherMarker = null;
let tempChartObj  = null;
let currentLat = null, currentLon = null;

async function fetchWeather() {
    const lat = parseFloat(document.getElementById('latInput').value);
    const lon = parseFloat(document.getElementById('lonInput').value);
    if (isNaN(lat) || isNaN(lon)) { alert('Please enter valid coordinates or select a country.'); return; }
    await loadWeather(document.getElementById('countryInput').value || `${lat}, ${lon}`, lat, lon);
}

async function quickWeather(name, lat, lon) {
    document.getElementById('countryInput').value = name;
    document.getElementById('latInput').value     = lat;
    document.getElementById('lonInput').value     = lon;
    await loadWeather(name, lat, lon);
}

async function loadWeather(name, lat, lon) {
    document.getElementById('weatherLoading').style.display = 'block';
    document.getElementById('weatherResults').style.display = 'none';
    currentLat = lat; currentLon = lon;

    try {
        const r = await fetch(
            `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}` +
            `&current=temperature_2m,wind_speed_10m,weather_code` +
            `&hourly=temperature_2m,wind_speed_10m&forecast_days=2`
        );
        const data = await r.json();

        document.getElementById('weatherLoading').style.display = 'none';
        document.getElementById('weatherResults').style.display = 'block';
        document.getElementById('locationLabel').textContent =
            `📍 ${name.toUpperCase()} · LAT ${lat} · LON ${lon}`;

        const cur = data.current ?? {};
        const temp = cur.temperature_2m ?? '—';
        const wind = cur.wind_speed_10m ?? 0;

        document.getElementById('wTemp').textContent = temp + '°';
        document.getElementById('wWind').textContent = wind;

        // Storm risk
        let storm = 'LOW', stormColor = 'var(--pw-green)', stormSub = 'Wind < 20 km/h';
        let risk  = 'LOW', riskColor  = 'var(--pw-green)';
        if (wind > 30) {
            storm = 'HIGH'; stormColor = 'var(--pw-red)'; stormSub = 'Dangerous wind levels';
            risk  = 'HIGH'; riskColor  = 'var(--pw-red)';
        } else if (wind > 20) {
            storm = 'MODERATE'; stormColor = 'var(--pw-amber)'; stormSub = 'Elevated wind activity';
            risk  = 'MEDIUM';   riskColor  = 'var(--pw-amber)';
        }
        document.getElementById('wStorm').textContent   = storm;
        document.getElementById('wStorm').style.color   = stormColor;
        document.getElementById('wStormSub').textContent = stormSub;
        document.getElementById('wRisk').textContent    = risk;
        document.getElementById('wRisk').style.color    = riskColor;

        // Risk assessment text
        document.getElementById('riskAssessment').innerHTML = `
            <div style="color:${riskColor};margin-bottom:8px;font-size:13px;font-weight:700;">► ${risk} RISK LEVEL</div>
            <div>TEMP : <span style="color:#fff;">${temp}°C</span></div>
            <div>WIND : <span style="color:#fff;">${wind} km/h</span></div>
            <div>STORM: <span style="color:${stormColor};">${storm}</span></div>
            <div style="margin-top:12px;color:var(--pw-text);font-size:11px;line-height:1.6;">
                ${risk === 'HIGH'   ? '⚠ CRITICAL: Delay shipments. Port operations may be suspended.' :
                  risk === 'MEDIUM' ? '⚠ ADVISORY: Monitor conditions. Consider contingency routes.' :
                                      '✓ CLEAR: Conditions optimal for logistics operations.'}
            </div>
        `;

        // Forecast list
        const hours = (data.hourly?.time ?? []).slice(0, 6);
        const temps = (data.hourly?.temperature_2m ?? []).slice(0, 6);
        const winds = (data.hourly?.wind_speed_10m ?? []).slice(0, 6);
        document.getElementById('forecastList').innerHTML = hours.map((t, i) => `
            <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--pw-border);">
                <span style="font-size:12px;color:var(--pw-text-dim);font-family:'JetBrains Mono',monospace;">
                    ${t.split('T')[1]?.slice(0,5) ?? t.slice(-5)}
                </span>
                <span style="font-size:13px;color:var(--pw-cyan);">${temps[i] ?? '—'}°C</span>
                <span style="font-size:12px;color:var(--pw-green);">${winds[i] ?? '—'} km/h</span>
            </div>
        `).join('');

        // Map
        renderWeatherMap(lat, lon, name);

        // Chart — 24h hourly temps
        const chartHours = (data.hourly?.time ?? []).slice(0, 24).map(t => t.split('T')[1]?.slice(0,5) ?? t);
        const chartTemps = (data.hourly?.temperature_2m ?? []).slice(0, 24);
        renderTempChart(chartHours, chartTemps);

    } catch (e) {
        document.getElementById('weatherLoading').style.display = 'none';
        alert('Failed to fetch weather data. Please try again.');
    }
}

function renderWeatherMap(lat, lon, name) {
    if (!weatherMapObj) {
        weatherMapObj = L.map('weatherMap').setView([lat, lon], 6);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap', maxZoom: 18
        }).addTo(weatherMapObj);
    } else {
        weatherMapObj.flyTo([lat, lon], 6, { duration: 1 });
        if (weatherMarker) weatherMapObj.removeLayer(weatherMarker);
    }
    weatherMarker = L.circleMarker([lat, lon], {
        radius: 10, fillColor: '#29c5ff', color: '#29c5ff',
        fillOpacity: 0.6, weight: 2
    }).addTo(weatherMapObj)
      .bindPopup(`<div style="font-family:'JetBrains Mono',monospace;color:#29c5ff;font-weight:700;">${name}</div>`).openPopup();
}

function renderTempChart(labels, data) {
    if (tempChartObj) tempChartObj.destroy();
    tempChartObj = new Chart(document.getElementById('tempChart'), {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Temperature (°C)',
                data,
                borderColor: '#29c5ff',
                backgroundColor: 'rgba(41,197,255,.08)',
                borderWidth: 2,
                pointRadius: 3,
                pointBackgroundColor: '#29c5ff',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { labels: { color: '#7a9ab8', font: { family: 'JetBrains Mono' } } } },
            scales: {
                x: { ticks: { color: '#7a9ab8', font: { family: 'JetBrains Mono', size: 11 } }, grid: { color: 'rgba(255,255,255,.04)' } },
                y: { ticks: { color: '#7a9ab8', font: { family: 'JetBrains Mono', size: 11 } }, grid: { color: 'rgba(255,255,255,.04)' } }
            }
        }
    });
}
</script>

<style>
.pw-quick-btn {
    background: var(--pw-bg3);
    border: 1px solid var(--pw-border);
    color: var(--pw-text-dim);
    padding: 7px 14px;
    border-radius: 8px;
    font-size: 12px;
    cursor: pointer;
    transition: .2s;
}
.pw-quick-btn:hover { border-color: var(--pw-border2); color: var(--pw-cyan); }
</style>
@endsection
