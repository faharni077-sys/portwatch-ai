@extends('layouts.app')
@section('title', 'Currency Monitor')
@section('breadcrumb', 'CURRENCY IMPACT DASHBOARD')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <div class="pw-section-title" style="margin-bottom:4px;">
            <i class="bi bi-currency-exchange me-2 text-cyan"></i>CURRENCY IMPACT DASHBOARD
        </div>
        <p style="color:var(--pw-text-dim);font-size:13px;margin:0;">
            Real-time exchange rates via ExchangeRate API. Monitor forex volatility for import/export decisions.
        </p>
    </div>
</div>

{{-- Controls --}}
<div class="pw-card mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-md-4">
            <label style="font-size:11px;letter-spacing:1.5px;color:var(--pw-text-dim);font-family:'JetBrains Mono',monospace;">BASE CURRENCY</label>
            <select id="baseCurrency" class="pw-select mt-1">
                <option value="USD" selected>USD — US Dollar</option>
                <option value="EUR">EUR — Euro</option>
                <option value="GBP">GBP — British Pound</option>
                <option value="JPY">JPY — Japanese Yen</option>
                <option value="CNY">CNY — Chinese Yuan</option>
                <option value="IDR">IDR — Indonesian Rupiah</option>
                <option value="AUD">AUD — Australian Dollar</option>
                <option value="SGD">SGD — Singapore Dollar</option>
            </select>
        </div>
        <div class="col-md-4">
            <label style="font-size:11px;letter-spacing:1.5px;color:var(--pw-text-dim);font-family:'JetBrains Mono',monospace;">COMPARE WITH</label>
            <select id="compareCurrency" class="pw-select mt-1">
                <option value="IDR" selected>IDR — Indonesian Rupiah</option>
                <option value="USD">USD — US Dollar</option>
                <option value="EUR">EUR — Euro</option>
                <option value="GBP">GBP — British Pound</option>
                <option value="JPY">JPY — Japanese Yen</option>
                <option value="CNY">CNY — Chinese Yuan</option>
                <option value="AUD">AUD — Australian Dollar</option>
                <option value="BRL">BRL — Brazilian Real</option>
                <option value="INR">INR — Indian Rupee</option>
                <option value="KRW">KRW — South Korean Won</option>
            </select>
        </div>
        <div class="col-md-4">
            <button class="btn-pw-primary w-100" onclick="fetchRates()">
                <i class="bi bi-arrow-repeat me-1"></i> Fetch Live Rates
            </button>
        </div>
    </div>
</div>

{{-- Quick pairs --}}
<div class="mb-4">
    <div style="font-size:11px;letter-spacing:2px;color:var(--pw-text-dim);font-family:'JetBrains Mono',monospace;margin-bottom:10px;">MAJOR PAIRS</div>
    <div style="display:flex;flex-wrap:wrap;gap:8px;">
        @foreach([['USD','IDR'],['EUR','IDR'],['USD','EUR'],['USD','JPY'],['USD','CNY'],['GBP','USD'],['AUD','USD'],['SGD','IDR']] as [$b,$c])
        <button class="pw-quick-btn" onclick="quickPair('{{ $b }}','{{ $c }}')">{{ $b }}/{{ $c }}</button>
        @endforeach
    </div>
</div>

{{-- Stats --}}
<div id="rateStats" style="display:none;">
    <div class="pw-stat-row mb-4">
        <div class="pw-card text-center" style="padding:20px;">
            <div class="pw-card-label">BASE</div>
            <div class="pw-card-value text-cyan" id="rateBase" style="font-size:32px;">—</div>
        </div>
        <div class="pw-card text-center" style="padding:20px;">
            <div class="pw-card-label">RATE</div>
            <div class="pw-card-value text-green" id="rateMain" style="font-size:32px;">—</div>
        </div>
        <div class="pw-card text-center" style="padding:20px;">
            <div class="pw-card-label">VOLATILITY</div>
            <div class="pw-card-value" id="rateVol" style="font-size:22px;">—</div>
        </div>
        <div class="pw-card text-center" style="padding:20px;">
            <div class="pw-card-label">RISK FLAG</div>
            <div class="pw-card-value" id="rateRisk" style="font-size:22px;">—</div>
        </div>
    </div>

    {{-- Chart --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="pw-card">
                <div class="pw-section-title"><i class="bi bi-graph-up me-2 text-cyan"></i>RATE TREND · SIMULATED HISTORY</div>
                <div class="pw-chart-wrap-lg">
                    <canvas id="currencyChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="pw-card h-100">
                <div class="pw-section-title"><i class="bi bi-list-ul me-2 text-cyan"></i>MAJOR RATES</div>
                <div id="majorRatesList"></div>
            </div>
        </div>
    </div>

    {{-- All rates table --}}
    <div class="pw-card">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="pw-section-title" style="margin-bottom:0;">
                <i class="bi bi-table me-2 text-cyan"></i>ALL RATES
            </div>
            <input type="text" id="rateSearch" class="pw-input" placeholder="Filter currency..." style="width:200px;">
        </div>
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;" id="ratesTable">
                <thead>
                    <tr style="border-bottom:1px solid var(--pw-border);">
                        <th style="padding:10px;text-align:left;font-size:10px;letter-spacing:2px;color:var(--pw-text-dim);font-family:'JetBrains Mono',monospace;">CURRENCY</th>
                        <th style="padding:10px;text-align:right;font-size:10px;letter-spacing:2px;color:var(--pw-text-dim);font-family:'JetBrains Mono',monospace;">RATE</th>
                        <th style="padding:10px;text-align:center;font-size:10px;letter-spacing:2px;color:var(--pw-text-dim);font-family:'JetBrains Mono',monospace;">STATUS</th>
                    </tr>
                </thead>
                <tbody id="ratesBody"></tbody>
            </table>
        </div>
    </div>
</div>

{{-- Loading --}}
<div id="currencyLoading" style="text-align:center;padding:60px;color:var(--pw-text-dim);">
    <i class="bi bi-currency-exchange" style="font-size:48px;display:block;margin-bottom:16px;color:var(--pw-border);"></i>
    <div style="font-family:'JetBrains Mono',monospace;">SELECT A CURRENCY PAIR AND FETCH RATES</div>
</div>

@endsection

@section('scripts')
<script>
let currencyChartObj = null;
let allRates = {};

async function fetchRates() {
    const base    = document.getElementById('baseCurrency').value;
    const compare = document.getElementById('compareCurrency').value;

    document.getElementById('currencyLoading').innerHTML = `
        <div style="font-family:'JetBrains Mono',monospace;font-size:13px;color:var(--pw-cyan);">
            FETCHING ${base}/${compare} RATES...
        </div>`;

    try {
        const r = await fetch(`https://open.er-api.com/v6/latest/${base}`);
        if (!r.ok) throw new Error();
        const data = await r.json();
        allRates = data.rates ?? {};

        document.getElementById('currencyLoading').style.display = 'none';
        document.getElementById('rateStats').style.display = 'block';

        const rate = allRates[compare] ?? 0;
        document.getElementById('rateBase').textContent = base;
        document.getElementById('rateMain').textContent = rate.toLocaleString('en-US', { maximumFractionDigits: 4 });

        // Volatility and risk heuristic
        let vol = 'LOW', volColor = 'var(--pw-green)';
        let risk = 'STABLE', riskColor = 'var(--pw-green)';
        if (['IDR','PKR','NGN','VND','IRR'].includes(compare)) {
            vol = 'HIGH'; volColor = 'var(--pw-amber)';
            risk = 'MONITOR'; riskColor = 'var(--pw-amber)';
        }
        document.getElementById('rateVol').textContent  = vol;
        document.getElementById('rateVol').style.color  = volColor;
        document.getElementById('rateRisk').textContent = risk;
        document.getElementById('rateRisk').style.color = riskColor;

        // Major rates list
        const majors = ['USD','EUR','GBP','JPY','CNY','IDR','AUD','SGD','INR','KRW'];
        document.getElementById('majorRatesList').innerHTML = majors
            .filter(c => allRates[c])
            .map(c => `
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--pw-border);font-size:13px;">
                    <span style="color:var(--pw-text-dim);font-family:'JetBrains Mono',monospace;">${c}</span>
                    <span style="color:var(--pw-cyan);font-weight:600;font-family:'JetBrains Mono',monospace;">
                        ${(allRates[c]).toLocaleString('en-US', { maximumFractionDigits: 4 })}
                    </span>
                </div>
            `).join('');

        // Simulated 30-day trend
        renderCurrencyChart(base, compare, rate);

        // All rates table
        renderRatesTable(allRates, base);

    } catch(e) {
        document.getElementById('currencyLoading').innerHTML = `
            <div style="color:var(--pw-red);font-family:'JetBrains Mono',monospace;">
                ⚠ FAILED TO FETCH RATES. Please try again.
            </div>`;
    }
}

function quickPair(base, compare) {
    document.getElementById('baseCurrency').value    = base;
    document.getElementById('compareCurrency').value = compare;
    fetchRates();
}

function renderCurrencyChart(base, compare, currentRate) {
    // Simulate 30-day history with small random walk
    const labels = [];
    const values = [];
    const today = new Date();
    let val = currentRate;
    for (let i = 29; i >= 0; i--) {
        const d = new Date(today);
        d.setDate(today.getDate() - i);
        labels.push(d.toLocaleDateString('en-GB', { day:'2-digit', month:'short' }));
        val = val * (1 + (Math.random() - 0.5) * 0.01);
        values.push(parseFloat(val.toFixed(4)));
    }
    values[values.length - 1] = currentRate; // anchor to real value

    if (currencyChartObj) currencyChartObj.destroy();
    currencyChartObj = new Chart(document.getElementById('currencyChart'), {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: `${base}/${compare}`,
                data: values,
                borderColor: '#29c5ff',
                backgroundColor: 'rgba(41,197,255,.06)',
                borderWidth: 2,
                pointRadius: 2,
                pointBackgroundColor: '#29c5ff',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { labels: { color: '#7a9ab8', font: { family: 'JetBrains Mono', size: 11 } } },
                tooltip: { backgroundColor: 'rgba(11,25,41,.9)', titleColor: '#29c5ff', bodyColor: '#e2eaf4', borderColor: 'rgba(41,197,255,.3)', borderWidth: 1 }
            },
            scales: {
                x: { ticks: { color: '#7a9ab8', font: { family: 'JetBrains Mono', size: 10 }, maxRotation: 45 }, grid: { color: 'rgba(255,255,255,.03)' } },
                y: { ticks: { color: '#7a9ab8', font: { family: 'JetBrains Mono', size: 10 } }, grid: { color: 'rgba(255,255,255,.03)' } }
            }
        }
    });
}

function renderRatesTable(rates, base) {
    const currencies = Object.keys(rates);
    const body = document.getElementById('ratesBody');
    body.innerHTML = currencies.map(c => `
        <tr class="rate-row" data-code="${c.toLowerCase()}">
            <td style="padding:9px 10px;font-family:'JetBrains Mono',monospace;font-size:13px;color:var(--pw-text);">${c}</td>
            <td style="padding:9px 10px;text-align:right;font-family:'JetBrains Mono',monospace;font-size:13px;color:var(--pw-cyan);">
                ${rates[c].toLocaleString('en-US', { maximumFractionDigits: 6 })}
            </td>
            <td style="padding:9px 10px;text-align:center;">
                <span style="font-size:11px;color:var(--pw-green);font-family:'JetBrains Mono',monospace;">● LIVE</span>
            </td>
        </tr>
    `).join('');
}

// Table search filter
document.getElementById('rateSearch').addEventListener('input', function () {
    const kw = this.value.toLowerCase();
    document.querySelectorAll('.rate-row').forEach(row => {
        row.style.display = row.dataset.code.includes(kw) ? '' : 'none';
    });
});

// Auto-fetch USD/IDR on load
window.addEventListener('load', () => fetchRates());
</script>

<style>
.pw-quick-btn {
    background: var(--pw-bg3); border: 1px solid var(--pw-border);
    color: var(--pw-text-dim); padding: 7px 14px; border-radius: 8px;
    font-size: 12px; font-family: 'JetBrains Mono', monospace;
    cursor: pointer; transition: .2s;
}
.pw-quick-btn:hover { border-color: var(--pw-border2); color: var(--pw-cyan); }
#ratesTable tbody tr:hover td { background: var(--pw-bg3); }
#ratesTable tbody tr { border-bottom: 1px solid var(--pw-border); }
</style>
@endsection
