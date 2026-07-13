@extends('layouts.app')
@section('title', 'Watchlist')
@section('breadcrumb', 'FAVORITE MONITORING LIST')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <div class="pw-section-title" style="margin-bottom:4px;">
            <i class="bi bi-bookmark-star me-2 text-cyan"></i>FAVORITE MONITORING LIST
        </div>
        <p style="color:var(--pw-text-dim);font-size:13px;margin:0;">
            Save and monitor countries for supply chain risk tracking. Data synced in real-time.
        </p>
    </div>
</div>

{{-- Add country form --}}
<div class="pw-card mb-4">
    <div class="pw-section-title"><i class="bi bi-plus-circle me-2 text-cyan"></i>ADD COUNTRY TO WATCHLIST</div>
    <div class="row g-3 align-items-end">
        <div class="col-md-4">
            <label style="font-size:11px;letter-spacing:1.5px;color:var(--pw-text-dim);font-family:'JetBrains Mono',monospace;">COUNTRY NAME</label>
            <input type="text" id="watchCountryName" class="pw-input mt-1" placeholder="e.g. Germany">
        </div>
        <div class="col-md-2">
            <label style="font-size:11px;letter-spacing:1.5px;color:var(--pw-text-dim);font-family:'JetBrains Mono',monospace;">ISO2 CODE</label>
            <input type="text" id="watchCountryCode" class="pw-input mt-1" placeholder="e.g. DE" maxlength="2" style="text-transform:uppercase;">
        </div>
        <div class="col-md-3">
            <label style="font-size:11px;letter-spacing:1.5px;color:var(--pw-text-dim);font-family:'JetBrains Mono',monospace;">PRIORITY</label>
            <select id="watchPriority" class="pw-select mt-1">
                <option value="HIGH">🔴 High Priority</option>
                <option value="MEDIUM" selected>🟡 Medium Priority</option>
                <option value="LOW">🟢 Low Priority</option>
            </select>
        </div>
        <div class="col-md-3">
            <button class="btn-pw-primary w-100" onclick="addToWatchlist()">
                <i class="bi bi-bookmark-plus me-1"></i> Add to Watchlist
            </button>
        </div>
    </div>
</div>

{{-- Quick add popular countries --}}
<div class="mb-4">
    <div style="font-size:11px;letter-spacing:2px;color:var(--pw-text-dim);font-family:'JetBrains Mono',monospace;margin-bottom:10px;">QUICK ADD</div>
    <div style="display:flex;flex-wrap:wrap;gap:8px;">
        @foreach([
            ['Germany','DE'],['China','CN'],['Indonesia','ID'],
            ['Australia','AU'],['Japan','JP'],['USA','US'],
            ['India','IN'],['Brazil','BR'],['Singapore','SG'],
        ] as [$name,$code])
        <button class="pw-quick-btn" onclick="quickAdd('{{ $name }}','{{ $code }}')">
            <img src="https://flagsapi.com/{{ $code }}/flat/16.png" style="vertical-align:middle;margin-right:4px;">
            {{ $name }}
        </button>
        @endforeach
    </div>
</div>

{{-- Stats --}}
<div class="pw-stat-row mb-4">
    <div class="pw-card" style="padding:14px 18px;">
        <div class="pw-card-label">WATCHING</div>
        <div class="pw-card-value text-cyan" id="watchCount">0</div>
    </div>
    <div class="pw-card" style="padding:14px 18px;">
        <div class="pw-card-label">HIGH PRIORITY</div>
        <div class="pw-card-value" style="color:var(--pw-red);" id="highCount">0</div>
    </div>
    <div class="pw-card" style="padding:14px 18px;">
        <div class="pw-card-label">MEDIUM PRIORITY</div>
        <div class="pw-card-value" style="color:var(--pw-amber);" id="medCount">0</div>
    </div>
    <div class="pw-card" style="padding:14px 18px;">
        <div class="pw-card-label">LOW PRIORITY</div>
        <div class="pw-card-value text-green" id="lowCount">0</div>
    </div>
</div>

{{-- Watchlist grid --}}
<div id="watchlistGrid" class="row g-3">
    <div id="watchlistEmpty" style="text-align:center;padding:80px;color:var(--pw-text-dim);width:100%;">
        <i class="bi bi-bookmark-star" style="font-size:48px;display:block;margin-bottom:16px;color:var(--pw-border);"></i>
        <div style="font-family:'JetBrains Mono',monospace;font-size:13px;margin-bottom:8px;">NO COUNTRIES MONITORED</div>
        <div style="font-size:13px;">Add a country above to begin monitoring.</div>
    </div>
</div>

{{-- Alert toast --}}
<div id="toastBox" style="
    position:fixed;bottom:24px;right:24px;z-index:9999;
    display:none;
    background:var(--pw-bg2);border:1px solid var(--pw-border2);
    border-radius:12px;padding:14px 20px;
    box-shadow:0 8px 32px rgba(0,0,0,.5);
    font-size:13px;font-family:'JetBrains Mono',monospace;
    min-width:260px;color:var(--pw-cyan);
    animation: slideIn .3s ease;
">
    <i class="bi bi-check-circle me-2"></i> <span id="toastMsg"></span>
</div>

@endsection

@section('scripts')
<script>
let watchlist = JSON.parse(localStorage.getItem('pw_watchlist') ?? '[]');

function save() {
    localStorage.setItem('pw_watchlist', JSON.stringify(watchlist));
    updateStats();
    renderWatchlist();
}

function updateStats() {
    document.getElementById('watchCount').textContent = watchlist.length;
    document.getElementById('highCount').textContent  = watchlist.filter(w => w.priority === 'HIGH').length;
    document.getElementById('medCount').textContent   = watchlist.filter(w => w.priority === 'MEDIUM').length;
    document.getElementById('lowCount').textContent   = watchlist.filter(w => w.priority === 'LOW').length;
}

function addToWatchlist() {
    const name = document.getElementById('watchCountryName').value.trim();
    const code = document.getElementById('watchCountryCode').value.trim().toUpperCase();
    const priority = document.getElementById('watchPriority').value;

    if (!name || !code) { showToast('Please enter country name and ISO2 code.'); return; }
    if (watchlist.find(w => w.code === code)) { showToast(`${name} is already in your watchlist.`); return; }

    watchlist.push({ name, code, priority, addedAt: new Date().toISOString() });
    save();
    document.getElementById('watchCountryName').value = '';
    document.getElementById('watchCountryCode').value = '';
    showToast(`${name} added to watchlist!`);
}

function quickAdd(name, code) {
    if (watchlist.find(w => w.code === code)) { showToast(`${name} already in watchlist.`); return; }
    watchlist.push({ name, code, priority: 'MEDIUM', addedAt: new Date().toISOString() });
    save();
    showToast(`${name} added to watchlist!`);
}

function removeFromWatchlist(code) {
    watchlist = watchlist.filter(w => w.code !== code);
    save();
    showToast('Country removed from watchlist.');
}

function setPriority(code, priority) {
    const item = watchlist.find(w => w.code === code);
    if (item) { item.priority = priority; save(); }
}

function renderWatchlist() {
    const grid  = document.getElementById('watchlistGrid');
    const empty = document.getElementById('watchlistEmpty');

    if (!watchlist.length) {
        grid.innerHTML = '';
        grid.appendChild(empty);
        empty.style.display = 'block';
        return;
    }
    empty.style.display = 'none';

    const priorityOrder = { HIGH: 0, MEDIUM: 1, LOW: 2 };
    const sorted = [...watchlist].sort((a, b) => (priorityOrder[a.priority] ?? 1) - (priorityOrder[b.priority] ?? 1));

    grid.innerHTML = sorted.map(w => {
        const prioColor = w.priority === 'HIGH' ? 'var(--pw-red)' : w.priority === 'MEDIUM' ? 'var(--pw-amber)' : 'var(--pw-green)';
        const prioBorder = w.priority === 'HIGH' ? 'rgba(239,68,68,.35)' : w.priority === 'MEDIUM' ? 'rgba(245,158,11,.35)' : 'rgba(34,197,94,.35)';
        const addedDate = new Date(w.addedAt).toLocaleDateString('en-GB', { day:'2-digit', month:'short', year:'numeric' });

        return `
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="pw-card" style="border-color:${prioBorder};position:relative;overflow:hidden;">
                {{-- Flag blur bg --}}
                <div style="position:absolute;inset:0;background:url('https://flagsapi.com/${w.code}/flat/64.png') center/cover;opacity:.04;filter:blur(6px);"></div>
                <div style="position:relative;">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
                        <img src="https://flagsapi.com/${w.code}/flat/64.png"
                             style="width:44px;height:30px;object-fit:cover;border-radius:5px;border:1px solid rgba(255,255,255,.1);"
                             onerror="this.style.display='none'">
                        <div>
                            <div style="font-size:15px;font-weight:700;color:#fff;">${w.name}</div>
                            <div style="font-size:11px;color:var(--pw-text-dim);font-family:'JetBrains Mono',monospace;">${w.code}</div>
                        </div>
                        <div style="margin-left:auto;">
                            <span style="
                                padding:3px 10px;border-radius:12px;font-size:10px;font-weight:700;
                                font-family:'JetBrains Mono',monospace;letter-spacing:1px;
                                background:${prioColor}22;color:${prioColor};border:1px solid ${prioColor}44;
                            ">${w.priority}</span>
                        </div>
                    </div>

                    <div style="font-size:11px;color:var(--pw-text-dim);font-family:'JetBrains Mono',monospace;margin-bottom:14px;">
                        ADDED: ${addedDate}
                    </div>

                    <div style="display:flex;gap:6px;flex-wrap:wrap;">
                        <a href="/country/${w.code}" class="btn-pw-outline" style="text-decoration:none;font-size:12px;padding:7px 12px;display:flex;align-items:center;gap:4px;">
                            <i class="bi bi-eye"></i> View Intel
                        </a>
                        <select onchange="setPriority('${w.code}', this.value)"
                            style="background:var(--pw-bg3);border:1px solid var(--pw-border);color:var(--pw-text-dim);border-radius:8px;padding:6px 10px;font-size:12px;cursor:pointer;">
                            <option value="HIGH"   ${w.priority==='HIGH'   ? 'selected' : ''}>🔴 High</option>
                            <option value="MEDIUM" ${w.priority==='MEDIUM' ? 'selected' : ''}>🟡 Medium</option>
                            <option value="LOW"    ${w.priority==='LOW'    ? 'selected' : ''}>🟢 Low</option>
                        </select>
                        <button onclick="removeFromWatchlist('${w.code}')"
                            style="background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.3);color:var(--pw-red);
                                   border-radius:8px;padding:7px 12px;font-size:12px;cursor:pointer;display:flex;align-items:center;gap:4px;">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>`;
    }).join('');
}

function showToast(msg) {
    const box = document.getElementById('toastBox');
    document.getElementById('toastMsg').textContent = msg;
    box.style.display = 'block';
    setTimeout(() => box.style.display = 'none', 3000);
}

// Init
updateStats();
renderWatchlist();
</script>

<style>
.pw-quick-btn {
    background: var(--pw-bg3); border: 1px solid var(--pw-border);
    color: var(--pw-text-dim); padding: 7px 14px; border-radius: 8px;
    font-size: 12px; cursor: pointer; transition: .2s;
}
.pw-quick-btn:hover { border-color: var(--pw-border2); color: var(--pw-cyan); }
#toastBox { animation: slideIn .3s ease; }
@keyframes slideIn {
    from { transform: translateY(20px); opacity: 0; }
    to   { transform: translateY(0);    opacity: 1; }
}
</style>
@endsection
