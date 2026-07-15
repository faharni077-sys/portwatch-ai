@extends('layouts.app')
@section('title', $country['name']['common'])
@section('breadcrumb', 'COUNTRY INTEL')

@section('content')

{{-- Back --}}
<a href="{{ route('countries.index') }}" class="btn-pw-outline mb-4 d-inline-flex align-items-center gap-2" style="text-decoration:none;">
    <i class="bi bi-arrow-left"></i> Back to Countries
</a>

{{-- ============================================================
     HERO — Flag + Name + Quick Info
     ============================================================ --}}
<div class="pw-card mb-4" style="position:relative;overflow:hidden;padding:0;">
    {{-- Flag background blur --}}
    <div style="
        position:absolute;inset:0;
        background:url('https://flagsapi.com/{{ $country['cca2'] }}/flat/64.png') center/cover;
        opacity:.06;
        filter:blur(8px);
    "></div>

    <div style="position:relative;padding:28px;display:flex;align-items:center;gap:28px;flex-wrap:wrap;">
        <img src="https://flagsapi.com/{{ $country['cca2'] }}/flat/64.png"
             style="width:96px;height:64px;object-fit:cover;border-radius:8px;border:1px solid var(--pw-border);box-shadow:0 0 20px rgba(0,0,0,.4);"
             onerror="this.style.display='none'"
             alt="{{ $country['name']['common'] }} flag">
        <div>
            <div style="font-size:11px;letter-spacing:3px;color:var(--pw-cyan);font-family:'JetBrains Mono',monospace;margin-bottom:6px;">COUNTRY PROFILE</div>
            <h1 style="font-size:32px;font-weight:800;color:#fff;margin:0;line-height:1;">{{ $country['name']['common'] }}</h1>
            <div style="color:var(--pw-text-dim);margin-top:6px;font-size:14px;">
                <span>{{ $country['name']['official'] ?? '' }}</span>
            </div>
        </div>
        <div class="ms-auto d-flex gap-3 flex-wrap">
            {{-- Risk Badge --}}
            @php
                $riskLevel = 'LOW'; $riskScore = 0; $riskColor = '#22c55e';
                if (isset($worldBank['inflation'])) {
                    $inf = $worldBank['inflation'];
                    if ($inf > 10) { $riskLevel = 'HIGH'; $riskScore = 75; $riskColor = '#ef4444'; }
                    elseif ($inf > 5) { $riskLevel = 'MEDIUM'; $riskScore = 45; $riskColor = '#f59e0b'; }
                    else { $riskScore = 22; }
                }
                if ($weather) {
                    $wind = $weather['current']['wind_speed_10m'] ?? 0;
                    if ($wind > 30 && $riskLevel !== 'HIGH') { $riskLevel = 'MEDIUM'; $riskColor = '#f59e0b'; $riskScore += 20; }
                }
            @endphp
            <div class="risk-badge {{ strtolower($riskLevel) }}" style="font-size:14px;padding:10px 20px;">
                <i class="bi bi-shield-{{ $riskLevel === 'LOW' ? 'check' : ($riskLevel === 'HIGH' ? 'exclamation' : 'half') }}"></i>
                {{ $riskLevel }} RISK
            </div>
            <a href="{{ route('watchlist') }}" class="btn-pw-outline" style="text-decoration:none;display:flex;align-items:center;gap:6px;font-size:13px;">
                <i class="bi bi-star"></i> Tambah ke Watchlist
            </a>
        </div>
    </div>
</div>

{{-- ============================================================
     DATA GRID
     ============================================================ --}}
<div class="row g-4">

    {{-- ---- Col 1: Economic ---- --}}
    <div class="col-xl-4 col-lg-6">
        <div class="pw-card h-100">
            <div class="pw-section-title"><i class="bi bi-bank2 me-2 text-cyan"></i>WORLD BANK DATA</div>

            @if(!empty($worldBank))
            <div class="pw-data-list">
                <div class="pw-data-row">
                    <span class="pw-data-label"><i class="bi bi-people me-2"></i>Population</span>
                    <span class="pw-data-val text-cyan">{{ isset($worldBank['population']) ? number_format($worldBank['population']) : '—' }}</span>
                </div>
                <div class="pw-data-row">
                    <span class="pw-data-label"><i class="bi bi-cash-stack me-2"></i>GDP</span>
                    <span class="pw-data-val text-green">
                        @if(isset($worldBank['gdp']))
                            ${{ number_format($worldBank['gdp'] / 1e9, 1) }}B
                        @else —
                        @endif
                    </span>
                </div>
                <div class="pw-data-row">
                    <span class="pw-data-label"><i class="bi bi-graph-up me-2"></i>Inflation</span>
                    <span class="pw-data-val" style="color:var(--pw-amber);">
                        {{ isset($worldBank['inflation']) ? number_format($worldBank['inflation'], 2) . '%' : '—' }}
                    </span>
                </div>
                <div class="pw-data-row">
                    <span class="pw-data-label"><i class="bi bi-box-arrow-up me-2"></i>Exports</span>
                    <span class="pw-data-val">
                        @if(isset($worldBank['exports']))
                            ${{ number_format($worldBank['exports'] / 1e9, 1) }}B
                        @else —
                        @endif
                    </span>
                </div>
                <div class="pw-data-row">
                    <span class="pw-data-label"><i class="bi bi-box-arrow-in-down me-2"></i>Imports</span>
                    <span class="pw-data-val">
                        @if(isset($worldBank['imports']))
                            ${{ number_format($worldBank['imports'] / 1e9, 1) }}B
                        @else —
                        @endif
                    </span>
                </div>
            </div>
            @else
            <div style="color:var(--pw-text-dim);font-size:13px;padding:20px 0;text-align:center;">
                <i class="bi bi-exclamation-circle d-block mb-2" style="font-size:24px;"></i>
                World Bank data not available
            </div>
            @endif
        </div>
    </div>

    {{-- ---- Col 2: Weather ---- --}}
    <div class="col-xl-4 col-lg-6">
        <div class="pw-card h-100">
            <div class="pw-section-title"><i class="bi bi-cloud-lightning me-2 text-cyan"></i>WEATHER INTEL</div>

            @if($weather)
            @php
                $temp  = $weather['current']['temperature_2m'] ?? 0;
                $wind  = $weather['current']['wind_speed_10m'] ?? 0;
                $stormRisk = $wind > 30 ? 'HIGH' : ($wind > 20 ? 'MEDIUM' : 'LOW');
                $stormClass = $stormRisk === 'HIGH' ? 'risk-high' : ($stormRisk === 'MEDIUM' ? 'risk-medium' : 'risk-low');
            @endphp
            <div class="text-center mb-4">
                <div style="font-size:60px;line-height:1;color:var(--pw-cyan);font-weight:800;">
                    {{ $temp }}<span style="font-size:28px;">°C</span>
                </div>
                <div style="color:var(--pw-text-dim);font-size:13px;margin-top:4px;">
                    Current Temperature · {{ $country['name']['common'] }}
                </div>
            </div>
            <div class="pw-data-list">
                <div class="pw-data-row">
                    <span class="pw-data-label"><i class="bi bi-wind me-2"></i>Wind Speed</span>
                    <span class="pw-data-val text-green">{{ $wind }} km/h</span>
                </div>
                <div class="pw-data-row">
                    <span class="pw-data-label"><i class="bi bi-shield-exclamation me-2"></i>Storm Risk</span>
                    <span class="pw-data-val {{ $stormClass }}">{{ $stormRisk }}</span>
                </div>
                <div class="pw-data-row">
                    <span class="pw-data-label"><i class="bi bi-geo me-2"></i>Coordinates</span>
                    <span class="pw-data-val" style="font-family:'JetBrains Mono',monospace;font-size:12px;">
                        {{ $country['latlng'][0] ?? '—' }}, {{ $country['latlng'][1] ?? '—' }}
                    </span>
                </div>
            </div>

            {{-- Wind bar --}}
            <div class="mt-3">
                <div style="display:flex;justify-content:space-between;font-size:11px;color:var(--pw-text-dim);margin-bottom:4px;">
                    <span>WIND INTENSITY</span><span>{{ $wind }} km/h</span>
                </div>
                <div class="pw-progress">
                    <div class="pw-progress-bar {{ $wind > 30 ? 'red' : ($wind > 20 ? 'amber' : 'green') }}"
                         style="width:{{ min(($wind / 60) * 100, 100) }}%;"></div>
                </div>
            </div>
            @else
            <div style="color:var(--pw-text-dim);font-size:13px;padding:20px 0;text-align:center;">
                <i class="bi bi-cloud-slash d-block mb-2" style="font-size:24px;"></i>
                Weather data not available
            </div>
            @endif
        </div>
    </div>

    {{-- ---- Col 3: Currency ---- --}}
    <div class="col-xl-4 col-lg-12">
        <div class="pw-card h-100">
            <div class="pw-section-title"><i class="bi bi-currency-exchange me-2 text-cyan"></i>EXCHANGE RATES</div>

            @php $baseCurrency = isset($country['currencies']) ? array_key_first($country['currencies']) : '—'; @endphp

            @if($exchange)
            <div class="text-center mb-4">
                <div style="font-size:11px;letter-spacing:2px;color:var(--pw-text-dim);font-family:'JetBrains Mono',monospace;margin-bottom:4px;">BASE CURRENCY</div>
                <div style="font-size:32px;font-weight:800;color:var(--pw-cyan);">{{ $baseCurrency }}</div>
                <div style="font-size:12px;color:var(--pw-text-dim);">
                    {{ $country['currencies'][$baseCurrency]['name'] ?? '' }}
                </div>
            </div>
            <div class="pw-data-list">
                @foreach(['IDR' => '🇮🇩', 'USD' => '🇺🇸', 'EUR' => '🇪🇺', 'JPY' => '🇯🇵', 'CNY' => '🇨🇳'] as $target => $flag)
                    @if(isset($exchange['rates'][$target]))
                    <div class="pw-data-row">
                        <span class="pw-data-label">{{ $flag }} {{ $target }}</span>
                        <span class="pw-data-val text-green" style="font-family:'JetBrains Mono',monospace;">
                            {{ number_format($exchange['rates'][$target], $target === 'IDR' ? 0 : 4) }}
                        </span>
                    </div>
                    @endif
                @endforeach
            </div>
            @else
            <div style="color:var(--pw-text-dim);font-size:13px;padding:20px 0;text-align:center;">
                <i class="bi bi-currency-exchange d-block mb-2" style="font-size:24px;"></i>
                Exchange rate data not available
            </div>
            @endif
        </div>
    </div>

    {{-- ---- Col 4: Country Info ---- --}}
    <div class="col-xl-4 col-lg-6">
        <div class="pw-card h-100">
            <div class="pw-section-title"><i class="bi bi-info-circle me-2 text-cyan"></i>COUNTRY PROFILE</div>
            <div class="pw-data-list">
                <div class="pw-data-row">
                    <span class="pw-data-label"><i class="bi bi-geo me-2"></i>Region</span>
                    <span class="pw-data-val">{{ $country['region'] ?? '—' }}</span>
                </div>
                <div class="pw-data-row">
                    <span class="pw-data-label"><i class="bi bi-building me-2"></i>Capital</span>
                    <span class="pw-data-val text-cyan">{{ $country['capital'][0] ?? '—' }}</span>
                </div>
                <div class="pw-data-row">
                    <span class="pw-data-label"><i class="bi bi-translate me-2"></i>Language</span>
                    <span class="pw-data-val" style="font-size:12px;">
                        {{ isset($country['languages']) ? implode(', ', array_slice(array_values($country['languages']), 0, 2)) : '—' }}
                    </span>
                </div>
                <div class="pw-data-row">
                    <span class="pw-data-label"><i class="bi bi-flag me-2"></i>ISO Code</span>
                    <span class="pw-data-val text-cyan" style="font-family:'JetBrains Mono',monospace;">
                        {{ $country['cca2'] }} / {{ $country['cca3'] ?? '—' }}
                    </span>
                </div>
                <div class="pw-data-row">
                    <span class="pw-data-label"><i class="bi bi-phone me-2"></i>Calling Code</span>
                    <span class="pw-data-val">
                        {{ isset($country['idd']) ? ($country['idd']['root'] ?? '') . implode('', array_slice($country['idd']['suffixes'] ?? [], 0, 1)) : '—' }}
                    </span>
                </div>
                <div class="pw-data-row">
                    <span class="pw-data-label"><i class="bi bi-clock me-2"></i>Timezone</span>
                    <span class="pw-data-val" style="font-size:12px;">{{ $country['timezones'][0] ?? '—' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ---- Col 5: Risk Score ---- --}}
    <div class="col-xl-8 col-lg-6">
        <div class="pw-card h-100">
            <div class="pw-section-title"><i class="bi bi-shield-shaded me-2 text-cyan"></i>COMPOSITE RISK ANALYSIS</div>
            @php
                $weatherRisk   = 20;
                $inflationRisk = 30;
                $currencyRisk  = 20;
                $newsRisk      = 20;

                if ($weather) {
                    $w = $weather['current']['wind_speed_10m'] ?? 0;
                    if ($w > 30) $weatherRisk = 80;
                    elseif ($w > 20) $weatherRisk = 50;
                }
                if (isset($worldBank['inflation'])) {
                    $inf = $worldBank['inflation'];
                    if ($inf > 10) $inflationRisk = 80;
                    elseif ($inf > 5) $inflationRisk = 50;
                }
                if ($exchange && isset($exchange['rates']['USD'])) {
                    $usd = $exchange['rates']['USD'];
                    if ($usd < 0.5 || $usd > 2) $currencyRisk = 70;
                }
                $totalRisk = round(($weatherRisk * 0.30) + ($inflationRisk * 0.20) + ($newsRisk * 0.40) + ($currencyRisk * 0.10));
                $finalLevel = $totalRisk >= 70 ? 'HIGH' : ($totalRisk >= 40 ? 'MEDIUM' : 'LOW');
                $finalClass = strtolower($finalLevel);
            @endphp

            <div class="row g-3">
                <div class="col-md-3 col-6">
                    <div class="pw-mini-card text-center">
                        <div class="pw-card-label">WEATHER</div>
                        <div class="pw-card-value" style="font-size:28px;color:{{ $weatherRisk >= 70 ? 'var(--pw-red)' : ($weatherRisk >= 40 ? 'var(--pw-amber)' : 'var(--pw-green)') }};">
                            {{ $weatherRisk }}
                        </div>
                        <div class="pw-progress mt-2"><div class="pw-progress-bar {{ $weatherRisk >= 70 ? 'red' : ($weatherRisk >= 40 ? 'amber' : 'green') }}" style="width:{{ $weatherRisk }}%;"></div></div>
                        <div style="font-size:10px;color:var(--pw-text-dim);margin-top:4px;">30% weight</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="pw-mini-card text-center">
                        <div class="pw-card-label">INFLATION</div>
                        <div class="pw-card-value" style="font-size:28px;color:{{ $inflationRisk >= 70 ? 'var(--pw-red)' : ($inflationRisk >= 40 ? 'var(--pw-amber)' : 'var(--pw-green)') }};">
                            {{ $inflationRisk }}
                        </div>
                        <div class="pw-progress mt-2"><div class="pw-progress-bar {{ $inflationRisk >= 70 ? 'red' : ($inflationRisk >= 40 ? 'amber' : 'green') }}" style="width:{{ $inflationRisk }}%;"></div></div>
                        <div style="font-size:10px;color:var(--pw-text-dim);margin-top:4px;">20% weight</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="pw-mini-card text-center">
                        <div class="pw-card-label">NEWS</div>
                        <div class="pw-card-value" style="font-size:28px;color:{{ $newsRisk >= 70 ? 'var(--pw-red)' : ($newsRisk >= 40 ? 'var(--pw-amber)' : 'var(--pw-green)') }};">
                            {{ $newsRisk }}
                        </div>
                        <div class="pw-progress mt-2"><div class="pw-progress-bar {{ $newsRisk >= 70 ? 'red' : ($newsRisk >= 40 ? 'amber' : 'green') }}" style="width:{{ $newsRisk }}%;"></div></div>
                        <div style="font-size:10px;color:var(--pw-text-dim);margin-top:4px;">40% weight</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="pw-mini-card text-center">
                        <div class="pw-card-label">CURRENCY</div>
                        <div class="pw-card-value" style="font-size:28px;color:{{ $currencyRisk >= 70 ? 'var(--pw-red)' : ($currencyRisk >= 40 ? 'var(--pw-amber)' : 'var(--pw-green)') }};">
                            {{ $currencyRisk }}
                        </div>
                        <div class="pw-progress mt-2"><div class="pw-progress-bar {{ $currencyRisk >= 70 ? 'red' : ($currencyRisk >= 40 ? 'amber' : 'green') }}" style="width:{{ $currencyRisk }}%;"></div></div>
                        <div style="font-size:10px;color:var(--pw-text-dim);margin-top:4px;">10% weight</div>
                    </div>
                </div>
            </div>

            {{-- Total risk --}}
            <div class="mt-4 d-flex align-items-center gap-4 flex-wrap">
                <div>
                    <div style="font-size:11px;letter-spacing:2px;color:var(--pw-text-dim);font-family:'JetBrains Mono',monospace;">COMPOSITE RISK SCORE</div>
                    <div style="font-size:52px;font-weight:800;line-height:1;color:{{ $finalLevel === 'HIGH' ? 'var(--pw-red)' : ($finalLevel === 'MEDIUM' ? 'var(--pw-amber)' : 'var(--pw-green)') }};">
                        {{ $totalRisk }}
                    </div>
                </div>
                <div>
                    <div class="risk-badge {{ $finalClass }}" style="font-size:16px;padding:12px 24px;">
                        <i class="bi bi-shield-{{ $finalLevel === 'LOW' ? 'check' : ($finalLevel === 'HIGH' ? 'exclamation' : 'half') }}"></i>
                        {{ $finalLevel }} RISK
                    </div>
                    <div style="font-size:12px;color:var(--pw-text-dim);margin-top:8px;">
                        Formula: (W×30% + I×20% + N×40% + C×10%)
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
.pw-data-list { display: flex; flex-direction: column; gap: 0; }
.pw-data-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid var(--pw-border);
    font-size: 13px;
}
.pw-data-row:last-child { border-bottom: none; }
.pw-data-label { color: var(--pw-text-dim); display: flex; align-items: center; }
.pw-data-val   { font-weight: 600; color: var(--pw-text); text-align: right; }
</style>
@endsection
