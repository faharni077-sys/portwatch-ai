@extends('layouts.app')
@section('title', 'Dashboard')
@section('breadcrumb', 'TACTICAL HUD')

@section('content')
{{-- ============================================================
     MISSION TOPBAR — country selector + analyze
     ============================================================ --}}
<div class="pw-mission-bar mb-4">
    <div class="pw-mission-item">
        <label>COUNTRY</label>
        <select id="countrySelect" class="pw-select" style="width:200px;">
            <option value="">— Select Country —</option>
            @foreach($countries as $c)
                <option value="{{ $c->code }}" data-name="{{ $c->name }}">
                    {{ $c->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="pw-mission-item">
        <label>ANALYSIS MODE</label>
        <select class="pw-select" style="width:160px;">
            <option>FULL SPECTRUM</option>
            <option>WEATHER ONLY</option>
            <option>ECONOMIC ONLY</option>
        </select>
    </div>
    <div class="pw-mission-item">
        <label>DATE</label>
        <div class="pw-mission-date" id="missionDate"></div>
    </div>
    <div class="pw-mission-item ms-auto">
        <label>&nbsp;</label>
        <button id="analyzeBtn" class="btn-pw-primary" onclick="runAnalysis()">
            <i class="bi bi-cpu me-2"></i> Analyze
        </button>
    </div>
    <div class="pw-mission-status">
        <span class="pw-mission-status-label">AI ANALYSIS</span>
        <span class="pw-mission-status-val" id="analysisStatus">STANDBY</span>
        <span class="dot-pulse" id="statusDot" style="opacity:.35;"></span>
    </div>
</div>

{{-- ============================================================
     MAIN GRID: MAP left · INTEL PANEL right
     ============================================================ --}}
<div class="pw-hud-grid">

    {{-- ---- MAP PANEL ---- --}}
    <div class="pw-map-col">
        <div class="pw-card" style="padding:0;overflow:hidden;height:100%;">
            <div class="pw-map-header">
                <span class="pw-map-label"><i class="bi bi-map me-2"></i>GLOBAL TACTICAL MAP</span>
                <span id="mapCountryLabel" class="pw-map-country">WORLDWIDE VIEW</span>
                <span class="pw-map-live"><span class="dot-pulse" style="width:6px;height:6px;"></span> LIVE</span>
            </div>
            <div id="dashMap"></div>
            <div class="pw-map-footer">
                <span id="mapCoords" style="font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--pw-text-dim);">LAT: — · LON: —</span>
                <span id="mapPortCount" style="font-size:11px;color:var(--pw-cyan);">0 PORTS LOADED</span>
            </div>
        </div>
    </div>

    {{-- ---- INTEL PANEL right ---- --}}
    <div class="pw-intel-col">

        {{-- Economic Trends --}}
        <div class="pw-card mb-3">
            <div class="pw-section-title"><i class="bi bi-graph-up me-2 text-cyan"></i>ECONOMIC TRENDS</div>
            <div class="row g-2">
                <div class="col-6">
                    <div class="pw-mini-card">
                        <div class="pw-card-label">GDP GROWTH</div>
                        <div class="pw-card-value text-cyan" id="gdpVal">—</div>
                        <div class="pw-card-sub" id="gdpYear">World Bank</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="pw-mini-card">
                        <div class="pw-card-label">INFLATION</div>
                        <div class="pw-card-value" id="inflationVal" style="color:var(--pw-amber);">—</div>
                        <div class="pw-card-sub" id="inflationYear">CPI %</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="pw-mini-card">
                        <div class="pw-card-label">CURRENCY</div>
                        <div class="pw-card-value text-green" id="currencyVal">—</div>
                        <div class="pw-card-sub" id="currencyBase">vs USD</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="pw-mini-card">
                        <div class="pw-card-label">RISK INDEX</div>
                        <div class="pw-card-value" id="riskVal">—</div>
                        <div class="pw-card-sub" id="riskLevel">Composite Score</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Weather --}}
        <div class="pw-card mb-3">
            <div class="pw-section-title"><i class="bi bi-cloud-lightning me-2 text-cyan"></i>WEATHER CONDITIONS</div>
            <div class="row g-2">
                <div class="col-6">
                    <div class="pw-mini-card">
                        <div class="pw-card-label">TEMPERATURE</div>
                        <div class="pw-card-value text-cyan" id="tempVal">—</div>
                        <div class="pw-card-sub">°C · Current</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="pw-mini-card">
                        <div class="pw-card-label">WIND SPEED</div>
                        <div class="pw-card-value" id="windVal" style="color:var(--pw-green);">—</div>
                        <div class="pw-card-sub">km/h</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Live Intelligence (news) --}}
        <div class="pw-card" style="flex:1;overflow:hidden;display:flex;flex-direction:column;">
            <div class="pw-section-title">
                <i class="bi bi-broadcast me-2 text-cyan"></i>LIVE INTELLIGENCE
                <span id="newsCount" class="ms-auto" style="font-size:10px;color:var(--pw-cyan);background:var(--pw-cyan-dim);padding:2px 8px;border-radius:10px;letter-spacing:1px;">0 NEW</span>
            </div>
            <div id="newsFeed" style="overflow-y:auto;flex:1;max-height:220px;">
                <div style="color:var(--pw-text-dim);font-size:13px;padding:20px;text-align:center;">
                    <i class="bi bi-broadcast" style="font-size:24px;display:block;margin-bottom:8px;"></i>
                    Select a country to load intelligence feed
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ============================================================
     MISSION TIMELINE
     ============================================================ --}}
<div class="pw-card mt-4">
    <div class="pw-section-title">
        <i class="bi bi-clock-history me-2 text-cyan"></i>MISSION TIMELINE / EVENT LOG
        <span id="nodeStatus" style="margin-left:auto;font-size:11px;font-family:'JetBrains Mono',monospace;color:var(--pw-green);">NODE_STABLE</span>
    </div>
    <div id="missionTimeline">
        <div class="pw-timeline-item">
            <span class="pw-timeline-time" id="tl-time-1">--:--</span>
            <span class="pw-timeline-dot info"></span>
            <span class="pw-timeline-text">System initialized. Select a country to begin intelligence gathering.</span>
        </div>
    </div>
</div>

{{-- AI Recommendation box --}}
<div class="pw-card mt-3" id="aiRecommendBox" style="display:none;border-color:rgba(34,197,94,.3);">
    <div class="pw-section-title" style="color:var(--pw-green);">
        <i class="bi bi-robot me-2"></i>AI RECOMMENDATION
    </div>
    <div id="aiRecommendText" style="font-family:'JetBrains Mono',monospace;font-size:13px;color:var(--pw-text);line-height:1.8;">
    </div>
</div>
@endsection

@section('scripts')
<script>
// ================================================================
// PORTWATCH AI — Dashboard Intelligence Engine
// ================================================================

const map = L.map('dashMap', { zoomControl: false }).setView([20, 0], 2);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap',
    maxZoom: 18
}).addTo(map);

L.control.zoom({ position: 'bottomright' }).addTo(map);

// Dark tile overlay
map.on('mousemove', e => {
    document.getElementById('mapCoords').textContent =
        `LAT: ${e.latlng.lat.toFixed(4)} · LON: ${e.latlng.lng.toFixed(4)}`;
});

// Mission date
const now = new Date();
document.getElementById('missionDate').textContent =
    now.toISOString().split('T')[0].replace(/-/g, '/');
document.getElementById('tl-time-1').textContent =
    now.toLocaleTimeString('en-GB', { hour:'2-digit', minute:'2-digit' });

// ---- Port markers ----
let portMarkers = [];

const portIcon = L.divIcon({
    className: '',
    html: `<div style="
        width:10px;height:10px;
        background:#29c5ff;
        border-radius:50%;
        border:2px solid rgba(41,197,255,.4);
        box-shadow:0 0 8px rgba(41,197,255,.6);
    "></div>`,
    iconSize: [10, 10]
});

function loadPorts(country = '') {
    portMarkers.forEach(m => map.removeLayer(m));
    portMarkers = [];
    const url = country ? `/api/ports?country=${country}` : '/api/ports';
    fetch(url)
        .then(r => r.json())
        .then(data => {
            data.forEach(p => {
                if (!p.latitude || !p.longitude) return;
                const m = L.marker([p.latitude, p.longitude], { icon: portIcon }).addTo(map);
                m.bindPopup(`
                    <div style="font-family:'JetBrains Mono',monospace;">
                        <div style="color:#29c5ff;font-weight:700;margin-bottom:4px;">${p.port_name}</div>
                        <div style="color:#7a9ab8;font-size:12px;">${p.city ?? '-'} · ${p.country?.name ?? '-'}</div>
                    </div>
                `);
                portMarkers.push(m);
            });
            document.getElementById('mapPortCount').textContent = data.length + ' PORTS LOADED';
        })
        .catch(() => {});
}
loadPorts();

// ---- Country lookup (mledoze GitHub — no API key, always works) ----
let countryCache = {};
let mledozeAllCountries = null;

async function getAllCountriesData() {
    if (mledozeAllCountries) return mledozeAllCountries;
    try {
        const r = await fetch('https://raw.githubusercontent.com/mledoze/countries/master/countries.json');
        if (!r.ok) return null;
        mledozeAllCountries = await r.json();
        return mledozeAllCountries;
    } catch(e) { return null; }
}

async function fetchCountryInfo(code) {
    if (countryCache[code]) return countryCache[code];
    const all = await getAllCountriesData();
    if (!all) return null;
    const country = all.find(c =>
        (c.cca2 ?? '').toUpperCase() === code.toUpperCase() ||
        (c.cca3 ?? '').toUpperCase() === code.toUpperCase()
    );
    if (country) countryCache[code] = country;
    return country ?? null;
}

// ---- Weather from Open-Meteo ----
async function fetchWeather(lat, lon) {
    const r = await fetch(
        `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current=temperature_2m,wind_speed_10m`
    );
    if (!r.ok) return null;
    return r.json();
}

// ---- Exchange Rate ----
async function fetchRate(base) {
    const r = await fetch(`https://open.er-api.com/v6/latest/${base}`);
    if (!r.ok) return null;
    return r.json();
}

// ---- Risk calculation ----
function calcRisk(weather, inflation, rateData, newsSentiment) {
    let weatherRisk = 20;
    if (weather) {
        const w = weather.current?.wind_speed_10m ?? 0;
        if (w > 30) weatherRisk = 80;
        else if (w > 20) weatherRisk = 50;
    }

    let inflationRisk = 30;
    const inf = parseFloat(inflation);
    if (!isNaN(inf)) {
        if (inf > 10) inflationRisk = 80;
        else if (inf > 5) inflationRisk = 50;
    }

    let currencyRisk = 20;
    if (rateData?.rates?.USD) {
        const usd = rateData.rates.USD;
        if (usd < 0.5 || usd > 2) currencyRisk = 70;
    }

    let newsRisk = 20;
    if (newsSentiment === 'Negative') newsRisk = 70;
    else if (newsSentiment === 'Neutral') newsRisk = 40;

    const total = Math.round(
        weatherRisk * 0.30 +
        inflationRisk * 0.20 +
        newsRisk * 0.40 +
        currencyRisk * 0.10
    );

    let level = 'LOW';
    if (total >= 70) level = 'HIGH';
    else if (total >= 40) level = 'MEDIUM';

    return { total, level };
}

function riskColor(level) {
    if (level === 'HIGH')   return 'var(--pw-red)';
    if (level === 'MEDIUM') return 'var(--pw-amber)';
    return 'var(--pw-green)';
}

// ---- Timeline append ----
function addEvent(text, type = 'ok') {
    const tl = document.getElementById('missionTimeline');
    const t = new Date().toLocaleTimeString('en-GB', { hour:'2-digit', minute:'2-digit' });
    const dotClass = type === 'warn' ? 'warn' : type === 'info' ? 'info' : '';
    const el = document.createElement('div');
    el.className = 'pw-timeline-item';
    el.innerHTML = `
        <span class="pw-timeline-time">${t}</span>
        <span class="pw-timeline-dot ${dotClass}"></span>
        <span class="pw-timeline-text">${text}</span>
    `;
    tl.insertBefore(el, tl.firstChild);
    // Keep max 8 items
    while (tl.children.length > 8) tl.removeChild(tl.lastChild);
}

// ---- Main Analyze function ----
async function runAnalysis() {
    const sel     = document.getElementById('countrySelect');
    const code    = sel.value;
    const name    = sel.options[sel.selectedIndex]?.dataset.name;

    if (!code) {
        alert('Please select a country first.');
        return;
    }

    // Status update
    document.getElementById('analysisStatus').textContent = 'SCANNING...';
    document.getElementById('statusDot').style.opacity = '1';
    document.getElementById('mapCountryLabel').textContent = name?.toUpperCase();
    addEvent(`Analysis started for <strong style="color:var(--pw-cyan)">${name}</strong>`, 'info');

    // Flyto country using REST Countries latlng
    const ci = await fetchCountryInfo(code);
    if (ci) {
        const lat = ci.latlng?.[0] ?? 0;
        const lon = ci.latlng?.[1] ?? 0;
        map.flyTo([lat, lon], 5, { duration: 1.5 });

        // Weather
        const weather = await fetchWeather(lat, lon);
        if (weather) {
            const temp = weather.current?.temperature_2m ?? '—';
            const wind = weather.current?.wind_speed_10m ?? '—';
            document.getElementById('tempVal').textContent = temp + '°';
            document.getElementById('windVal').textContent = wind;
            addEvent(`Weather data synced for ${name} — ${temp}°C, wind ${wind} km/h`);
        }

        // Currency
        const currencies  = ci.currencies ? Object.keys(ci.currencies) : [];
        const baseCurrency = currencies[0] ?? 'USD';
        const rateData = await fetchRate(baseCurrency);
        if (rateData) {
            const usd = rateData.rates?.USD ?? '—';
            document.getElementById('currencyVal').textContent = typeof usd === 'number' ? usd.toFixed(4) : usd;
            document.getElementById('currencyBase').textContent = `${baseCurrency} vs USD`;
            addEvent(`Currency synced — 1 ${baseCurrency} = ${typeof usd === 'number' ? usd.toFixed(4) : usd} USD`);
        }

        // GDP / Inflation from World Bank API (async, non-blocking)
        const iso3 = ci.cca3 ?? code;

        // Initial risk with weather + currency (inflasi menyusul)
        const risk = calcRisk(weather, 0, rateData, 'Neutral');
        document.getElementById('riskVal').textContent = risk.total;
        document.getElementById('riskVal').style.color = riskColor(risk.level);
        document.getElementById('riskLevel').textContent = risk.level + ' RISK';
        document.getElementById('riskLevel').style.color = riskColor(risk.level);
        addEvent(`Risk score dihitung — ${risk.total} (${risk.level})`, risk.level === 'HIGH' ? 'warn' : 'ok');

        document.getElementById('gdpVal').textContent = '...';
        document.getElementById('inflationVal').textContent = '...';
        document.getElementById('gdpYear').textContent = 'Memuat World Bank...';

        fetch(`https://api.worldbank.org/v2/country/${iso3}/indicator/NY.GDP.MKTP.CD?format=json&per_page=3`)
            .then(r => r.ok ? r.json() : null)
            .then(d => {
                const rows = d?.[1]?.filter(x => x.value !== null) ?? [];
                if (rows[0]) {
                    const gdp = (rows[0].value / 1e12).toFixed(2);
                    document.getElementById('gdpVal').textContent = '$' + gdp + 'T';
                    document.getElementById('gdpYear').textContent = 'World Bank · ' + rows[0].date;
                } else {
                    document.getElementById('gdpVal').textContent = '—';
                    document.getElementById('gdpYear').textContent = 'World Bank';
                }
            })
            .catch(() => {
                document.getElementById('gdpVal').textContent = '—';
                document.getElementById('gdpYear').textContent = 'World Bank';
            });

        fetch(`https://api.worldbank.org/v2/country/${iso3}/indicator/FP.CPI.TOTL.ZG?format=json&per_page=3`)
            .then(r => r.ok ? r.json() : null)
            .then(d => {
                const rows = d?.[1]?.filter(x => x.value !== null) ?? [];
                if (rows[0]) {
                    const inf = rows[0].value.toFixed(2);
                    document.getElementById('inflationVal').textContent = inf + '%';
                    document.getElementById('inflationYear').textContent = 'CPI · ' + rows[0].date;
                    // Perbarui risk score dengan data inflasi nyata
                    const risk2 = calcRisk(weather, rows[0].value, rateData, 'Neutral');
                    document.getElementById('riskVal').textContent = risk2.total;
                    document.getElementById('riskVal').style.color = riskColor(risk2.level);
                    document.getElementById('riskLevel').textContent = risk2.level + ' RISK';
                    document.getElementById('riskLevel').style.color = riskColor(risk2.level);
                    addEvent(`Risk diperbarui dengan data inflasi — ${risk2.total} (${risk2.level})`, risk2.level === 'HIGH' ? 'warn' : 'ok');
                } else {
                    document.getElementById('inflationVal').textContent = '—';
                    document.getElementById('inflationYear').textContent = 'CPI %';
                }
            })
            .catch(() => {
                document.getElementById('inflationVal').textContent = '—';
                document.getElementById('inflationYear').textContent = 'CPI %';
            });

        // AI Recommendation
        const recs = {
            LOW:    `> SYSTEM RECOMMENDATION: ${name.toUpperCase()}\n> CONDITIONS OPTIMAL FOR TRADE OPERATIONS.\n> WEATHER STABLE · CURRENCY WITHIN NORMAL RANGE.\n> PROCEED WITH STANDARD LOGISTICS PROTOCOLS.`,
            MEDIUM: `> ADVISORY: ${name.toUpperCase()}\n> MODERATE RISK DETECTED. MONITOR CURRENCY FLUCTUATIONS.\n> RECOMMEND HEDGING STRATEGY FOR IMPORT COSTS.\n> CONTINUE OPERATIONS WITH ENHANCED MONITORING.`,
            HIGH:   `> CRITICAL ALERT: ${name.toUpperCase()}\n> HIGH RISK ENVIRONMENT DETECTED.\n> RECOMMEND DELAYING NON-ESSENTIAL SHIPMENTS.\n> ACTIVATE CONTINGENCY SUPPLY CHAIN PROTOCOLS.`,
        };
        document.getElementById('aiRecommendText').textContent = recs[risk.level];
        document.getElementById('aiRecommendBox').style.display = 'block';
        document.getElementById('aiRecommendBox').style.borderColor =
            risk.level === 'HIGH' ? 'rgba(239,68,68,.3)' :
            risk.level === 'MEDIUM' ? 'rgba(245,158,11,.3)' : 'rgba(34,197,94,.3)';
    }

    // Load ports for this country
    loadPorts(code);

    // News feed (simulated from timeline)
    renderNewsFeed(name, code);

    document.getElementById('analysisStatus').textContent = 'ACTIVE';
    addEvent(`Intelligence sync complete for <strong style="color:var(--pw-green)">${name}</strong>. System fully operational.`, 'ok');
}

// ---- News Feed ----
function renderNewsFeed(country, code) {
    const feed = document.getElementById('newsFeed');
    const topics = ['logistics disruption', 'export volume', 'port congestion', 'currency stability', 'inflation report'];
    feed.innerHTML = '';

    // Show link to News page with pre-filled context
    const items = [
        { title: `${country} trade route analysis updated`, sentiment: 'Positive', time: '2m ago' },
        { title: `${country} logistics infrastructure assessment`, sentiment: 'Neutral', time: '8m ago' },
        { title: `Supply chain update: ${country} port operations`, sentiment: 'Positive', time: '15m ago' },
    ];

    items.forEach(item => {
        const sentClass = item.sentiment === 'Positive' ? 'sentiment-positive' :
                          item.sentiment === 'Negative' ? 'sentiment-negative' : 'sentiment-neutral';
        const el = document.createElement('div');
        el.className = 'pw-news-item';
        el.innerHTML = `
            <div class="pw-news-title">${item.title}</div>
            <div class="pw-news-meta">
                <span class="${sentClass}">⬤ ${item.sentiment}</span>
                &nbsp;·&nbsp; ${item.time}
            </div>
        `;
        feed.appendChild(el);
    });

    const linkEl = document.createElement('div');
    linkEl.style.cssText = 'text-align:center;padding:12px;';
    linkEl.innerHTML = `<a href="/news" class="pw-auth-link" style="font-size:12px;">
        <i class="bi bi-arrow-right me-1"></i>View Full News Intelligence
    </a>`;
    feed.appendChild(linkEl);

    document.getElementById('newsCount').textContent = items.length + ' NEW';
}
</script>

<style>
/* ---- Mission Bar ---- */
.pw-mission-bar {
    display: flex;
    align-items: flex-end;
    gap: 16px;
    flex-wrap: wrap;
    background: var(--pw-bg2);
    border: 1px solid var(--pw-border);
    border-radius: var(--pw-radius);
    padding: 18px 22px;
}
.pw-mission-item { display: flex; flex-direction: column; gap: 6px; }
.pw-mission-item label {
    font-size: 10px; letter-spacing: 2px; color: var(--pw-text-dim);
    font-family: 'JetBrains Mono', monospace;
}
.pw-mission-date {
    font-family: 'JetBrains Mono', monospace;
    font-size: 14px; color: var(--pw-cyan);
    padding: 10px 14px;
    background: var(--pw-bg3);
    border: 1px solid var(--pw-border);
    border-radius: 9px;
}
.pw-mission-status {
    display: flex; flex-direction: column; align-items: flex-end; gap: 4px;
    margin-left: 12px;
}
.pw-mission-status-label { font-size: 10px; letter-spacing: 2px; color: var(--pw-text-dim); font-family: 'JetBrains Mono', monospace; }
.pw-mission-status-val   { font-size: 13px; font-weight: 700; letter-spacing: 1.5px; color: var(--pw-cyan); font-family: 'JetBrains Mono', monospace; }

/* ---- HUD Grid ---- */
.pw-hud-grid {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 18px;
    min-height: 560px;
}

.pw-map-col { display: flex; flex-direction: column; }
.pw-intel-col { display: flex; flex-direction: column; gap: 0; }

/* Map */
.pw-map-header {
    display: flex; align-items: center; gap: 12px;
    padding: 14px 18px;
    border-bottom: 1px solid var(--pw-border);
    background: var(--pw-bg3);
}
.pw-map-label { font-size: 11px; letter-spacing: 2px; color: var(--pw-cyan); font-family: 'JetBrains Mono', monospace; font-weight: 700; }
.pw-map-country { font-size: 12px; font-weight: 700; color: #fff; margin-left: auto; letter-spacing: 1px; }
.pw-map-live { display: flex; align-items: center; gap: 6px; font-size: 11px; color: var(--pw-green); font-family: 'JetBrains Mono', monospace; letter-spacing: 1px; }

#dashMap { height: 480px; width: 100%; }

.pw-map-footer {
    display: flex; justify-content: space-between; align-items: center;
    padding: 10px 18px;
    border-top: 1px solid var(--pw-border);
    background: var(--pw-bg3);
}

/* Mini cards inside intel panel */
.pw-mini-card {
    background: var(--pw-bg3);
    border: 1px solid var(--pw-border);
    border-radius: 9px;
    padding: 12px;
}
.pw-mini-card .pw-card-label { font-size: 9px; letter-spacing: 1.5px; }
.pw-mini-card .pw-card-value { font-size: 20px; }

@media (max-width: 1100px) {
    .pw-hud-grid { grid-template-columns: 1fr; }
    #dashMap { height: 320px; }
}
</style>
@endsection
