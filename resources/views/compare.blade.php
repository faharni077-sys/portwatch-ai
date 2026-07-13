@extends('layouts.app')
@section('title', 'Country Comparison')
@section('breadcrumb', 'COUNTRY COMPARISON ENGINE')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <div class="pw-section-title" style="margin-bottom:4px;">
            <i class="bi bi-intersect me-2 text-cyan"></i>COUNTRY COMPARISON ENGINE
        </div>
        <p style="color:var(--pw-text-dim);font-size:13px;margin:0;">
            Side-by-side analysis — GDP · Inflation · Currency · Weather · Risk Score
        </p>
    </div>
</div>

{{-- Country Selector --}}
<div class="pw-card mb-4">
    <div class="row g-4 align-items-end">
        <div class="col-md-4">
            <label style="font-size:11px;letter-spacing:1.5px;color:var(--pw-text-dim);font-family:'JetBrains Mono',monospace;">
                <i class="bi bi-flag me-1" style="color:#29c5ff;"></i> COUNTRY A
            </label>
            <input type="text" id="countryA" class="pw-input mt-1" placeholder="e.g. Germany, IDN, CN...">
            <div id="sugA" class="pw-suggest-box"></div>
        </div>
        <div class="col-md-1 text-center" style="padding-bottom:6px;">
            <div style="font-size:22px;color:var(--pw-border2);">⚔</div>
        </div>
        <div class="col-md-4">
            <label style="font-size:11px;letter-spacing:1.5px;color:var(--pw-text-dim);font-family:'JetBrains Mono',monospace;">
                <i class="bi bi-flag me-1" style="color:#22c55e;"></i> COUNTRY B
            </label>
            <input type="text" id="countryB" class="pw-input mt-1" placeholder="e.g. Australia, SGD, JP...">
            <div id="sugB" class="pw-suggest-box"></div>
        </div>
        <div class="col-md-3">
            <button class="btn-pw-primary w-100" onclick="runCompare()">
                <i class="bi bi-intersect me-1"></i> Run Comparison
            </button>
        </div>
    </div>
</div>

{{-- Quick compare pairs --}}
<div class="mb-4">
    <div style="font-size:11px;letter-spacing:2px;color:var(--pw-text-dim);font-family:'JetBrains Mono',monospace;margin-bottom:10px;">QUICK PAIRS</div>
    <div style="display:flex;flex-wrap:wrap;gap:8px;">
        @foreach([
            ['DE','AU','Germany vs Australia'],
            ['CN','ID','China vs Indonesia'],
            ['US','JP','USA vs Japan'],
            ['SG','MY','Singapore vs Malaysia'],
            ['GB','DE','UK vs Germany'],
        ] as [$a,$b,$label])
        <button class="pw-quick-btn" onclick="quickCompare('{{ $a }}','{{ $b }}')">{{ $label }}</button>
        @endforeach
    </div>
</div>

{{-- Loading --}}
<div id="compareLoading" style="display:none;text-align:center;padding:40px;color:var(--pw-cyan);font-family:'JetBrains Mono',monospace;">
    LOADING INTELLIGENCE DATA...
</div>

{{-- Results --}}
<div id="compareResults" style="display:none;">

    {{-- Country headers --}}
    <div style="display:grid;grid-template-columns:1fr 80px 1fr;gap:0;margin-bottom:24px;align-items:center;">
        <div class="pw-card" id="headerA" style="padding:20px;border-color:rgba(41,197,255,.4);text-align:center;">
            <img id="flagA" src="" style="height:50px;border-radius:6px;margin-bottom:8px;" onerror="this.style.display='none'">
            <div id="nameA" style="font-size:20px;font-weight:800;color:#fff;"></div>
            <div id="codeA" style="font-size:11px;color:var(--pw-cyan);font-family:'JetBrains Mono',monospace;letter-spacing:2px;"></div>
        </div>
        <div style="text-align:center;font-size:24px;color:var(--pw-border2);">VS</div>
        <div class="pw-card" id="headerB" style="padding:20px;border-color:rgba(34,197,94,.4);text-align:center;">
            <img id="flagB" src="" style="height:50px;border-radius:6px;margin-bottom:8px;" onerror="this.style.display='none'">
            <div id="nameB" style="font-size:20px;font-weight:800;color:#fff;"></div>
            <div id="codeB" style="font-size:11px;color:var(--pw-green);font-family:'JetBrains Mono',monospace;letter-spacing:2px;"></div>
        </div>
    </div>

    {{-- Comparison rows --}}
    <div id="compareRows"></div>

    {{-- Radar / Bar chart --}}
    <div class="row g-4 mt-2">
        <div class="col-lg-6">
            <div class="pw-card">
                <div class="pw-section-title"><i class="bi bi-bar-chart me-2 text-cyan"></i>SIDE-BY-SIDE COMPARISON</div>
                <div class="pw-chart-wrap-lg">
                    <canvas id="compareBarChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="pw-card">
                <div class="pw-section-title"><i class="bi bi-shield-shaded me-2 text-cyan"></i>RISK BREAKDOWN</div>
                <div class="pw-chart-wrap-lg">
                    <canvas id="compareRiskChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Winner box --}}
    <div class="pw-card mt-4" id="winnerBox" style="border-color:rgba(41,197,255,.3);text-align:center;padding:28px;">
        <div style="font-size:11px;letter-spacing:3px;color:var(--pw-cyan);font-family:'JetBrains Mono',monospace;margin-bottom:8px;">AI RECOMMENDATION</div>
        <div id="winnerText" style="font-size:20px;font-weight:700;color:#fff;margin-bottom:8px;"></div>
        <div id="winnerSub"  style="font-size:14px;color:var(--pw-text-dim);"></div>
    </div>
</div>

{{-- Placeholder --}}
<div id="comparePlaceholder" style="text-align:center;padding:80px;color:var(--pw-text-dim);">
    <i class="bi bi-intersect" style="font-size:56px;display:block;margin-bottom:16px;color:var(--pw-border);"></i>
    <div style="font-family:'JetBrains Mono',monospace;font-size:13px;">SELECT TWO COUNTRIES TO COMPARE</div>
</div>

@endsection

@section('scripts')
<script>
let compareBarChart = null, compareRiskChart = null;
let mledozeCache = null;

async function getMledozeData() {
    if (mledozeCache) return mledozeCache;
    try {
        const r = await fetch('https://raw.githubusercontent.com/mledoze/countries/master/countries.json');
        if (!r.ok) return null;
        mledozeCache = await r.json();
    } catch(e) { mledozeCache = null; }
    return mledozeCache;
}

async function fetchCountry(code) {
    const all = await getMledozeData();
    if (!all) return null;
    return all.find(c =>
        (c.cca2 ?? '').toUpperCase() === code.toUpperCase() ||
        (c.cca3 ?? '').toUpperCase() === code.toUpperCase() ||
        (c.name?.common ?? '').toUpperCase().startsWith(code.toUpperCase())
    ) ?? null;
}

async function fetchWeather(lat, lon) {
    const r = await fetch(`https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current=temperature_2m,wind_speed_10m`);
    if (!r.ok) return null;
    return r.json();
}

async function fetchWB(iso3, indicator) {
    const r = await fetch(`https://api.worldbank.org/v2/country/${iso3}/indicator/${indicator}?format=json&per_page=5`);
    if (!r.ok) return null;
    const d = await r.json();
    const rows = d[1]?.filter(x => x.value !== null) ?? [];
    return rows[0]?.value ?? null;
}

async function fetchRate(base) {
    const r = await fetch(`https://open.er-api.com/v6/latest/${base}`);
    if (!r.ok) return null;
    return r.json();
}

function calcRisk(wind, inflation, rateUsd) {
    let wr = wind > 30 ? 80 : wind > 20 ? 50 : 20;
    let ir = inflation > 10 ? 80 : inflation > 5 ? 50 : 30;
    let cr = (!rateUsd || rateUsd < 0.5 || rateUsd > 2) ? 60 : 20;
    return Math.round(wr * 0.3 + ir * 0.2 + 20 * 0.4 + cr * 0.1);
}

async function runCompare() {
    const cA = document.getElementById('countryA').value.trim().toUpperCase();
    const cB = document.getElementById('countryB').value.trim().toUpperCase();
    if (!cA || !cB) { alert('Please enter both countries.'); return; }

    document.getElementById('comparePlaceholder').style.display = 'none';
    document.getElementById('compareResults').style.display = 'none';
    document.getElementById('compareLoading').style.display = 'block';

    try {
        const [ciA, ciB] = await Promise.all([fetchCountry(cA), fetchCountry(cB)]);
        if (!ciA || !ciB) { alert('Country not found. Use ISO2 code (e.g. DE, ID, AU).'); return; }

        const latA = ciA.latlng?.[0] ?? 0, lonA = ciA.latlng?.[1] ?? 0;
        const latB = ciB.latlng?.[0] ?? 0, lonB = ciB.latlng?.[1] ?? 0;

        const iso3A = ciA.cca3 ?? cA;
        const iso3B = ciB.cca3 ?? cB;
        const curA  = Object.keys(ciA.currencies ?? {})[0] ?? 'USD';
        const curB  = Object.keys(ciB.currencies ?? {})[0] ?? 'USD';

        const [wA, wB, gdpA, gdpB, infA, infB, rateA, rateB] = await Promise.all([
            fetchWeather(latA, lonA), fetchWeather(latB, lonB),
            fetchWB(iso3A, 'NY.GDP.MKTP.CD'), fetchWB(iso3B, 'NY.GDP.MKTP.CD'),
            fetchWB(iso3A, 'FP.CPI.TOTL.ZG'), fetchWB(iso3B, 'FP.CPI.TOTL.ZG'),
            fetchRate(curA), fetchRate(curB),
        ]);

        const tempA = wA?.current?.temperature_2m ?? 0;
        const windA = wA?.current?.wind_speed_10m ?? 0;
        const tempB = wB?.current?.temperature_2m ?? 0;
        const windB = wB?.current?.wind_speed_10m ?? 0;
        const riskA = calcRisk(windA, infA ?? 0, rateA?.rates?.USD ?? 1);
        const riskB = calcRisk(windB, infB ?? 0, rateB?.rates?.USD ?? 1);
        const rateAusd = rateA?.rates?.USD ?? '—';
        const rateBusd = rateB?.rates?.USD ?? '—';

        // Headers
        document.getElementById('flagA').src = `https://flagsapi.com/${ciA.cca2}/flat/64.png`;
        document.getElementById('flagB').src = `https://flagsapi.com/${ciB.cca2}/flat/64.png`;
        document.getElementById('nameA').textContent = ciA.name?.common ?? cA;
        document.getElementById('nameB').textContent = ciB.name?.common ?? cB;
        document.getElementById('codeA').textContent = ciA.cca2 + ' / ' + iso3A;
        document.getElementById('codeB').textContent = ciB.cca2 + ' / ' + iso3B;

        // Comparison rows
        const rows = [
            { label: 'GDP', icon: 'bi-cash-stack', a: gdpA ? '$'+(gdpA/1e12).toFixed(2)+'T' : '—', b: gdpB ? '$'+(gdpB/1e12).toFixed(2)+'T' : '—', better: (gdpA ?? 0) > (gdpB ?? 0) ? 'a' : 'b' },
            { label: 'Inflation', icon: 'bi-graph-up', a: infA != null ? infA.toFixed(2)+'%' : '—', b: infB != null ? infB.toFixed(2)+'%' : '—', better: (infA ?? 999) < (infB ?? 999) ? 'a' : 'b' },
            { label: 'Temperature', icon: 'bi-thermometer', a: tempA+'°C', b: tempB+'°C', better: null },
            { label: 'Wind Speed', icon: 'bi-wind', a: windA+' km/h', b: windB+' km/h', better: windA < windB ? 'a' : 'b' },
            { label: `Currency (${curA}) vs USD`, icon: 'bi-currency-exchange', a: typeof rateAusd === 'number' ? rateAusd.toFixed(4) : '—', b: typeof rateBusd === 'number' ? rateBusd.toFixed(4) : '—', better: null },
            { label: 'Risk Score', icon: 'bi-shield-shaded', a: riskA, b: riskB, better: riskA < riskB ? 'a' : (riskB < riskA ? 'b' : null) },
        ];

        document.getElementById('compareRows').innerHTML = rows.map(row => `
            <div style="display:grid;grid-template-columns:1fr 200px 1fr;gap:0;margin-bottom:10px;align-items:center;">
                <div class="pw-card" style="padding:16px;text-align:center;${row.better==='a'?'border-color:rgba(41,197,255,.5);':''};transition:.2s;">
                    <div style="font-size:22px;font-weight:800;color:${row.better==='a'?'var(--pw-cyan)':'var(--pw-text)'};">${row.a}</div>
                    ${row.better==='a' ? '<div style="font-size:10px;color:var(--pw-cyan);font-family:JetBrains Mono,monospace;margin-top:4px;">▲ BETTER</div>' : ''}
                </div>
                <div style="text-align:center;padding:0 12px;">
                    <div style="font-size:11px;letter-spacing:1.5px;color:var(--pw-text-dim);font-family:JetBrains Mono,monospace;">
                        <i class="bi ${row.icon}" style="display:block;font-size:18px;color:var(--pw-cyan);margin-bottom:4px;"></i>
                        ${row.label.toUpperCase()}
                    </div>
                </div>
                <div class="pw-card" style="padding:16px;text-align:center;${row.better==='b'?'border-color:rgba(34,197,94,.5);':''};transition:.2s;">
                    <div style="font-size:22px;font-weight:800;color:${row.better==='b'?'var(--pw-green)':'var(--pw-text)'};">${row.b}</div>
                    ${row.better==='b' ? '<div style="font-size:10px;color:var(--pw-green);font-family:JetBrains Mono,monospace;margin-top:4px;">▲ BETTER</div>' : ''}
                </div>
            </div>
        `).join('');

        // Charts
        renderCompareCharts(
            [ciA.name?.common, ciB.name?.common],
            [gdpA, gdpB], [infA, infB], [riskA, riskB], [windA, windB]
        );

        // Winner
        const winner = riskA < riskB ? ciA.name?.common : ciB.name?.common;
        const loser  = riskA < riskB ? ciB.name?.common : ciA.name?.common;
        document.getElementById('winnerText').textContent = `✓ ${winner} is the safer supply chain partner`;
        document.getElementById('winnerSub').textContent  =
            `Lower composite risk score (${Math.min(riskA,riskB)}) vs ${loser} (${Math.max(riskA,riskB)}). ` +
            `Recommend prioritizing ${winner} for import operations.`;

        document.getElementById('compareLoading').style.display = 'none';
        document.getElementById('compareResults').style.display = 'block';

    } catch(e) {
        document.getElementById('compareLoading').style.display = 'none';
        document.getElementById('comparePlaceholder').style.display = 'block';
        alert('Comparison failed. Check country codes and try again.');
    }
}

function renderCompareCharts(names, gdp, inflation, risk, wind) {
    if (compareBarChart)  compareBarChart.destroy();
    if (compareRiskChart) compareRiskChart.destroy();

    const opts = {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { labels: { color: '#7a9ab8', font: { family: 'JetBrains Mono', size: 11 } } } },
        scales: {
            x: { ticks: { color: '#7a9ab8' }, grid: { color: 'rgba(255,255,255,.03)' } },
            y: { ticks: { color: '#7a9ab8' }, grid: { color: 'rgba(255,255,255,.03)' } }
        }
    };

    compareBarChart = new Chart(document.getElementById('compareBarChart'), {
        type: 'bar',
        data: {
            labels: names,
            datasets: [
                { label: 'GDP (USD Trillion)', data: gdp.map(v => v ? +(v/1e12).toFixed(2) : 0), backgroundColor: ['rgba(41,197,255,.5)','rgba(34,197,94,.5)'], borderColor: ['#29c5ff','#22c55e'], borderWidth: 1.5, borderRadius: 6 },
                { label: 'Inflation (%)',       data: inflation.map(v => v ?? 0), backgroundColor: ['rgba(245,158,11,.4)','rgba(245,158,11,.4)'],  borderColor: ['#f59e0b','#f59e0b'], borderWidth: 1.5, borderRadius: 6 },
            ]
        },
        options: opts
    });

    compareRiskChart = new Chart(document.getElementById('compareRiskChart'), {
        type: 'bar',
        data: {
            labels: ['Weather Risk','Inflation Risk','News Risk','Currency Risk'],
            datasets: [
                { label: names[0], data: [wind[0]>30?80:wind[0]>20?50:20, inflation[0]>10?80:inflation[0]>5?50:30, 20, 20], backgroundColor: 'rgba(41,197,255,.4)', borderColor: '#29c5ff', borderWidth: 1.5, borderRadius: 4 },
                { label: names[1], data: [wind[1]>30?80:wind[1]>20?50:20, inflation[1]>10?80:inflation[1]>5?50:30, 20, 20], backgroundColor: 'rgba(34,197,94,.4)',  borderColor: '#22c55e', borderWidth: 1.5, borderRadius: 4 },
            ]
        },
        options: { ...opts, scales: { ...opts.scales, y: { ...opts.scales.y, min: 0, max: 100 } } }
    });
}

function quickCompare(a, b) {
    document.getElementById('countryA').value = a;
    document.getElementById('countryB').value = b;
    runCompare();
}
</script>

<style>
.pw-quick-btn {
    background: var(--pw-bg3); border: 1px solid var(--pw-border);
    color: var(--pw-text-dim); padding: 7px 14px; border-radius: 8px;
    font-size: 12px; cursor: pointer; transition: .2s;
}
.pw-quick-btn:hover { border-color: var(--pw-border2); color: var(--pw-cyan); }
.pw-suggest-box {
    position: absolute; z-index: 999;
    background: var(--pw-bg2); border: 1px solid var(--pw-border2);
    border-radius: 10px; max-height: 200px; overflow-y: auto;
    box-shadow: 0 8px 24px rgba(0,0,0,.5); width: 280px;
}
</style>
@endsection
