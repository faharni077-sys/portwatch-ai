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

{{-- Empty state — lives OUTSIDE the grid so innerHTML rewrites never destroy it --}}
<div id="watchlistEmpty" style="text-align:center;padding:80px;color:var(--pw-text-dim);width:100%;display:block;">
    <i class="bi bi-bookmark-star" style="font-size:48px;display:block;margin-bottom:16px;color:var(--pw-border);"></i>
    <div style="font-family:'JetBrains Mono',monospace;font-size:13px;margin-bottom:8px;">NO COUNTRIES MONITORED</div>
    <div style="font-size:13px;">Add a country above to begin monitoring.</div>
</div>

{{-- Watchlist grid — only card columns are injected here --}}
<div id="watchlistGrid" class="row g-3"></div>

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
// ================================================================
// PORTWATCH AI — Watchlist Engine (Database-backed)
// ================================================================

// CSRF token — required for POST/DELETE/PATCH
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

let watchlist = []; // In-memory state (source of truth = database)

// ---- Helpers ----
function csrfHeaders() {
    return {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': CSRF,
    };
}

function updateStats() {
    document.getElementById('watchCount').textContent = watchlist.length;
    document.getElementById('highCount').textContent  = watchlist.filter(w => w.priority === 'HIGH').length;
    document.getElementById('medCount').textContent   = watchlist.filter(w => w.priority === 'MEDIUM').length;
    document.getElementById('lowCount').textContent   = watchlist.filter(w => w.priority === 'LOW').length;
}

// ---- Load from database on page init ----
async function loadWatchlist() {
    try {
        const res  = await fetch('/api/watchlist', { headers: { 'Accept': 'application/json' } });
        const data = await res.json();
        watchlist  = Array.isArray(data) ? data : [];
        updateStats();
        renderWatchlist();
    } catch (e) {
        console.error('Gagal memuat watchlist:', e);
    }
}

// ---- Add country to watchlist ----
async function addToWatchlist() {
    const name     = document.getElementById('watchCountryName').value.trim();
    const code     = document.getElementById('watchCountryCode').value.trim().toUpperCase();
    const priority = document.getElementById('watchPriority').value;

    if (!name || !code) { showToast('Masukkan nama negara dan kode ISO2.', true); return; }
    if (code.length !== 2) { showToast('Kode ISO2 harus 2 huruf (contoh: ID, US, DE).', true); return; }
    if (watchlist.find(w => w.country_code === code)) {
        showToast(`${name} sudah ada di watchlist.`, true); return;
    }

    try {
        const res = await fetch('/api/watchlist', {
            method:  'POST',
            headers: csrfHeaders(),
            body:    JSON.stringify({ country_name: name, country_code: code, priority }),
        });

        // Parse response body once
        let data = {};
        try { data = await res.json(); } catch (_) {}

        if (res.status === 409) { showToast(data.message ?? `${name} sudah ada di watchlist.`, true); return; }
        if (res.status === 422) { showToast(data.message ?? 'Data tidak valid. Periksa kode ISO2.', true); return; }
        if (!res.ok)            { showToast(data.message ?? 'Gagal menyimpan. Coba lagi.', true); return; }

        // Clear input fields immediately
        document.getElementById('watchCountryName').value = '';
        document.getElementById('watchCountryCode').value = '';

        // Re-fetch from server for guaranteed sync, then update UI
        await loadWatchlist();

        showToast(`${data.country_name ?? name} ditambahkan ke watchlist!`);
    } catch (e) {
        console.error('addToWatchlist error:', e);
        showToast('Error: ' + e.message, true);
    }
}

// ---- Quick Add button ----
async function quickAdd(name, code) {
    if (watchlist.find(w => w.country_code === code.toUpperCase())) {
        showToast(`${name} sudah ada di watchlist.`, true); return;
    }

    try {
        const res = await fetch('/api/watchlist', {
            method:  'POST',
            headers: csrfHeaders(),
            body:    JSON.stringify({ country_name: name, country_code: code, priority: 'MEDIUM' }),
        });

        let data = {};
        try { data = await res.json(); } catch (_) {}

        if (res.status === 409) { showToast(data.message ?? `${name} sudah ada di watchlist.`, true); return; }
        if (res.status === 422) { showToast(data.message ?? 'Negara tidak ditemukan di database.', true); return; }
        if (!res.ok)            { showToast(data.message ?? 'Gagal menyimpan.', true); return; }

        await loadWatchlist();
        showToast(`${data.country_name ?? name} ditambahkan ke watchlist!`);
    } catch (e) {
        console.error('quickAdd error:', e);
        showToast('Error: ' + e.message, true);
    }
}

// ---- Remove from watchlist ----
// NOTE: id from onclick HTML attribute arrives as a string — use Number() to match
async function removeFromWatchlist(rawId) {
    const id = Number(rawId);
    try {
        const res = await fetch(`/api/watchlist/${id}`, {
            method:  'DELETE',
            headers: csrfHeaders(),
        });
        if (!res.ok) { showToast('Gagal menghapus.', true); return; }

        watchlist = watchlist.filter(w => Number(w.id) !== id);
        updateStats();
        renderWatchlist();
        showToast('Negara dihapus dari watchlist.');
    } catch (e) {
        console.error('removeFromWatchlist error:', e);
        showToast('Error: ' + e.message, true);
    }
}

// ---- Set priority ----
// NOTE: id from onclick HTML attribute arrives as a string — use Number() to match
async function setPriority(rawId, priority) {
    const id = Number(rawId);
    try {
        const res = await fetch(`/api/watchlist/${id}/priority`, {
            method:  'PATCH',
            headers: csrfHeaders(),
            body:    JSON.stringify({ priority }),
        });
        if (!res.ok) { showToast('Gagal update priority.', true); return; }

        const item = watchlist.find(w => Number(w.id) === id);
        if (item) item.priority = priority;
        updateStats();
        renderWatchlist();
    } catch (e) {
        console.error('setPriority error:', e);
        showToast('Error: ' + e.message, true);
    }
}

// ---- Render watchlist grid ----
function renderWatchlist() {
    const grid  = document.getElementById('watchlistGrid');
    const empty = document.getElementById('watchlistEmpty');

    // Guard: bail out if critical elements are missing
    if (!grid) return;

    if (!watchlist.length) {
        // Clear any existing cards and show empty state
        grid.innerHTML = '';
        if (empty) empty.style.display = 'block';
        return;
    }

    // Hide empty state (it lives outside the grid, so it's always findable)
    if (empty) empty.style.display = 'none';

    const priorityOrder = { HIGH: 0, MEDIUM: 1, LOW: 2 };
    const sorted = [...watchlist].sort((a, b) =>
        (priorityOrder[a.priority] ?? 1) - (priorityOrder[b.priority] ?? 1));

    // Overwrite only the grid — #watchlistEmpty is untouched because it's outside
    grid.innerHTML = sorted.map(w => {
        const prioColor  = w.priority === 'HIGH' ? 'var(--pw-red)' : w.priority === 'MEDIUM' ? 'var(--pw-amber)' : 'var(--pw-green)';
        const prioBorder = w.priority === 'HIGH' ? 'rgba(239,68,68,.35)' : w.priority === 'MEDIUM' ? 'rgba(245,158,11,.35)' : 'rgba(34,197,94,.35)';
        const addedDate  = new Date(w.added_at).toLocaleDateString('en-GB', { day:'2-digit', month:'short', year:'numeric' });
        const code       = w.country_code ?? '—';

        return `
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="pw-card" style="border-color:${prioBorder};position:relative;overflow:hidden;">
                <div style="position:absolute;inset:0;background:url('https://flagsapi.com/${code}/flat/64.png') center/cover;opacity:.04;filter:blur(6px);"></div>
                <div style="position:relative;">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
                        <img src="https://flagsapi.com/${code}/flat/64.png"
                             style="width:44px;height:30px;object-fit:cover;border-radius:5px;border:1px solid rgba(255,255,255,.1);"
                             onerror="this.style.display='none'">
                        <div>
                            <div style="font-size:15px;font-weight:700;color:#fff;">${w.country_name}</div>
                            <div style="font-size:11px;color:var(--pw-text-dim);font-family:'JetBrains Mono',monospace;">${code}</div>
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
                        <a href="/country/${code}" class="btn-pw-outline" style="text-decoration:none;font-size:12px;padding:7px 12px;display:flex;align-items:center;gap:4px;">
                            <i class="bi bi-eye"></i> View Intel
                        </a>
                        <select onchange="setPriority(${w.id}, this.value)"
                            style="background:var(--pw-bg3);border:1px solid var(--pw-border);color:var(--pw-text-dim);border-radius:8px;padding:6px 10px;font-size:12px;cursor:pointer;">
                            <option value="HIGH"   ${w.priority==='HIGH'   ? 'selected' : ''}>🔴 High</option>
                            <option value="MEDIUM" ${w.priority==='MEDIUM' ? 'selected' : ''}>🟡 Medium</option>
                            <option value="LOW"    ${w.priority==='LOW'    ? 'selected' : ''}>🟢 Low</option>
                        </select>
                        <button onclick="removeFromWatchlist(${w.id})"
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

function showToast(msg, isError = false) {
    const box = document.getElementById('toastBox');
    if (!box) return;
    const msgEl = document.getElementById('toastMsg');
    if (msgEl) msgEl.textContent = msg;
    box.style.color       = isError ? 'var(--pw-red)' : 'var(--pw-cyan)';
    box.style.borderColor = isError ? 'rgba(239,68,68,.35)' : 'var(--pw-border2)';
    const icon = box.querySelector('i');
    if (icon) icon.className = isError ? 'bi bi-exclamation-circle me-2' : 'bi bi-check-circle me-2';
    box.style.display = 'block';
    setTimeout(() => { if (box) box.style.display = 'none'; }, 4000);
}

// Init — load from database
loadWatchlist();
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
