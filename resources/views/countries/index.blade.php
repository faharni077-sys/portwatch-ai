@extends('layouts.app')
@section('title', 'Countries')
@section('breadcrumb', 'GLOBAL COUNTRIES')

@section('content')

{{-- Header + Search --}}
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <div class="pw-section-title" style="margin-bottom:4px;">
            <i class="bi bi-globe2 me-2 text-cyan"></i>COUNTRY INTELLIGENCE
        </div>
        <p style="color:var(--pw-text-dim);font-size:13px;margin:0;">
            Select a country to view GDP, inflation, weather, currency, and risk score.
        </p>
    </div>
    <div style="display:flex;gap:10px;align-items:center;">
        <div style="position:relative;">
            <i class="bi bi-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--pw-text-dim);font-size:14px;"></i>
            <input
                type="text"
                id="searchCountry"
                class="pw-input"
                placeholder="Search country..."
                style="padding-left:38px;width:240px;">
        </div>
        <select id="filterRegion" class="pw-select" style="width:160px;">
            <option value="">All Regions</option>
            <option>Africa</option>
            <option>Americas</option>
            <option>Asia</option>
            <option>Europe</option>
            <option>Oceania</option>
        </select>
    </div>
</div>

{{-- Stats row --}}
<div class="pw-stat-row mb-4">
    <div class="pw-card" style="padding:16px;">
        <div class="pw-card-label">TOTAL COUNTRIES</div>
        <div class="pw-card-value text-cyan" id="totalCount">{{ count($countries) }}</div>
    </div>
    <div class="pw-card" style="padding:16px;">
        <div class="pw-card-label">SHOWN</div>
        <div class="pw-card-value" id="shownCount">{{ count($countries) }}</div>
    </div>
    <div class="pw-card" style="padding:16px;">
        <div class="pw-card-label">DATA SOURCE</div>
        <div class="pw-card-value text-green" style="font-size:14px;">REST Countries</div>
    </div>
</div>

{{-- Country Grid --}}
<div class="row g-3" id="countryGrid">
    @foreach($countries as $country)
    <div class="col-xl-3 col-lg-4 col-md-6 country-card-wrap"
         data-name="{{ strtolower($country['name']['common']) }}"
         data-region="{{ strtolower($country['region'] ?? '') }}">
        <a href="{{ route('country.show', $country['cca2']) }}" class="pw-country-card">
            <div style="position:relative;overflow:hidden;height:90px;">
                <img
                    src="https://flagsapi.com/{{ $country['cca2'] }}/flat/64.png"
                    class="pw-country-card-flag"
                    onerror="this.style.display='none'"
                    loading="lazy"
                    alt="{{ $country['name']['common'] }} flag">
                {{-- Gradient overlay on flag --}}
                <div style="position:absolute;inset:0;background:linear-gradient(to bottom,transparent 40%,var(--pw-bg2) 100%);"></div>
                {{-- Region badge --}}
                <span style="
                    position:absolute;top:8px;right:8px;
                    background:rgba(7,17,29,.75);
                    border:1px solid var(--pw-border);
                    border-radius:6px;
                    padding:2px 8px;
                    font-size:10px;
                    color:var(--pw-text-dim);
                    font-family:'JetBrains Mono',monospace;
                    letter-spacing:1px;
                    backdrop-filter:blur(4px);
                ">{{ strtoupper($country['region'] ?? '—') }}</span>
            </div>
            <div class="pw-country-card-body">
                <div class="pw-country-card-name">{{ $country['name']['common'] }}</div>
                <div class="pw-country-card-meta">
                    <span style="color:var(--pw-text-dim);">
                        <i class="bi bi-geo-alt" style="color:var(--pw-cyan);"></i>
                        {{ $country['capital'][0] ?? '—' }}
                    </span><br>
                    <span style="color:var(--pw-text-dim);">
                        <i class="bi bi-cash-coin" style="color:var(--pw-green);"></i>
                        {{ isset($country['currencies']) ? implode(', ', array_keys($country['currencies'])) : '—' }}
                    </span>&nbsp;
                    <span style="color:var(--pw-text-dim);">
                        <i class="bi bi-flag" style="color:var(--pw-amber);"></i>
                        {{ $country['cca2'] }}
                    </span>
                </div>
                <div style="margin-top:10px;display:flex;align-items:center;justify-content:space-between;">
                    <span style="font-size:11px;color:var(--pw-cyan);font-family:'JetBrains Mono',monospace;letter-spacing:1px;">VIEW INTEL →</span>
                    <i class="bi bi-arrow-right-circle" style="color:var(--pw-border2);font-size:16px;"></i>
                </div>
            </div>
        </a>
    </div>
    @endforeach
</div>

{{-- No results --}}
<div id="noResults" style="display:none;text-align:center;padding:60px;color:var(--pw-text-dim);">
    <i class="bi bi-search" style="font-size:40px;display:block;margin-bottom:12px;"></i>
    No countries found matching your search.
</div>

@endsection

@section('scripts')
<script>
const searchInput  = document.getElementById('searchCountry');
const regionFilter = document.getElementById('filterRegion');
const shownCount   = document.getElementById('shownCount');
const noResults    = document.getElementById('noResults');

function filterCountries() {
    const keyword = searchInput.value.toLowerCase().trim();
    const region  = regionFilter.value.toLowerCase().trim();
    let shown = 0;

    document.querySelectorAll('.country-card-wrap').forEach(card => {
        const name = card.dataset.name;
        const reg  = card.dataset.region;
        const matchName   = !keyword || name.includes(keyword);
        const matchRegion = !region  || reg.includes(region);
        const visible = matchName && matchRegion;
        card.style.display = visible ? '' : 'none';
        if (visible) shown++;
    });

    shownCount.textContent = shown;
    noResults.style.display = shown === 0 ? 'flex' : 'none';
    noResults.style.flexDirection = 'column';
    noResults.style.alignItems = 'center';
}

searchInput.addEventListener('input',    filterCountries);
regionFilter.addEventListener('change', filterCountries);
</script>
@endsection
