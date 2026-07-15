<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — PortWatch AI</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])

    <style>
        /* ── Admin Layout ── */
        .adm-layout   { display:flex; min-height:100vh; }

        /* ── Sidebar ── */
        .adm-sidebar  {
            width: 240px; flex-shrink: 0;
            background: var(--pw-bg2);
            border-right: 1px solid var(--pw-border);
            display: flex; flex-direction: column;
            position: fixed; top:0; left:0; bottom:0;
            z-index: 1000; overflow-y:auto;
        }
        .adm-logo {
            display:flex; align-items:center; gap:10px;
            padding: 20px 18px;
            border-bottom: 1px solid var(--pw-border);
        }
        .adm-logo-icon {
            width:34px; height:34px;
            background: linear-gradient(135deg,#f59e0b,#d97706);
            border-radius:9px;
            display:flex; align-items:center; justify-content:center;
            color:#07111d; font-size:16px;
            box-shadow: 0 0 14px rgba(245,158,11,.35);
        }
        .adm-logo-name { font-size:12px; font-weight:700; letter-spacing:2px; color:#fff; line-height:1.2; }
        .adm-logo-sub  { font-size:9px;  letter-spacing:1.5px; color:#f59e0b; font-family:'JetBrains Mono',monospace; }

        .adm-badge {
            margin: 12px 16px;
            background: rgba(245,158,11,.12);
            border: 1px solid rgba(245,158,11,.3);
            border-radius:7px;
            padding: 6px 12px;
            font-size:10px; font-weight:700; letter-spacing:1.5px;
            color:#f59e0b; font-family:'JetBrains Mono',monospace;
            display:flex; align-items:center; gap:6px;
        }

        .adm-nav       { flex:1; padding:6px 10px; }
        .adm-nav-label {
            font-size:9px; letter-spacing:2px;
            color: var(--pw-text-dim);
            font-family:'JetBrains Mono',monospace;
            padding: 10px 8px 4px;
        }
        .adm-nav-divider { height:1px; background:var(--pw-border); margin:8px 8px; }

        .adm-nav-item {
            display:flex; align-items:center; gap:10px;
            padding: 9px 12px; border-radius:8px;
            color: var(--pw-text-dim); text-decoration:none;
            font-size:13px; font-weight:500;
            transition: .2s ease;
            border:none; background:transparent;
            width:100%; text-align:left; cursor:pointer;
        }
        .adm-nav-item i { font-size:15px; flex-shrink:0; }
        .adm-nav-item:hover { background:rgba(245,158,11,.1); color:#f59e0b; }
        .adm-nav-item.active {
            background: rgba(245,158,11,.12);
            color:#f59e0b;
            border-left: 3px solid #f59e0b;
        }
        .adm-nav-logout:hover { background:rgba(239,68,68,.12); color:var(--pw-red); }

        .adm-sidebar-footer {
            padding: 14px 18px;
            border-top: 1px solid var(--pw-border);
            font-size:11px; font-family:'JetBrains Mono',monospace;
        }
        .adm-sf-row { display:flex; justify-content:space-between; padding:2px 0; }
        .adm-sf-label { color:var(--pw-text-dim); letter-spacing:1px; }
        .adm-sf-val   { font-weight:600; color:#f59e0b; }

        /* ── Main wrap ── */
        .adm-main-wrap {
            margin-left: 240px;
            flex:1; display:flex; flex-direction:column; min-height:100vh;
        }

        /* ── Topbar ── */
        .adm-topbar {
            height: 56px;
            background: var(--pw-bg2);
            border-bottom: 1px solid var(--pw-border);
            display:flex; align-items:center; justify-content:space-between;
            padding: 0 24px;
            position:sticky; top:0; z-index:900; flex-shrink:0;
        }
        .adm-topbar-title {
            font-size:12px; font-weight:700; letter-spacing:2px;
            color:#fff; font-family:'JetBrains Mono',monospace;
        }
        .adm-topbar-sub { font-size:11px; color:var(--pw-text-dim); }
        .adm-topbar-right { display:flex; align-items:center; gap:16px; }
        .adm-user-badge {
            display:flex; align-items:center; gap:8px;
            font-size:13px; color:var(--pw-text);
        }
        .adm-user-dot {
            width:30px; height:30px; border-radius:50%;
            background:linear-gradient(135deg,#f59e0b,#d97706);
            display:flex; align-items:center; justify-content:center;
            font-size:12px; font-weight:800; color:#07111d;
        }

        /* ── Content ── */
        .adm-content { flex:1; padding:24px; }

        /* ── Cards ── */
        .adm-card {
            background: var(--pw-bg2);
            border: 1px solid var(--pw-border);
            border-radius: 12px;
            padding: 20px;
            transition: .2s;
        }
        .adm-card:hover { border-color: rgba(245,158,11,.25); }

        .adm-stat-card {
            background: var(--pw-bg2);
            border: 1px solid var(--pw-border);
            border-radius:12px; padding:18px 20px;
            transition:.2s;
        }
        .adm-stat-card:hover { border-color:rgba(245,158,11,.3); }
        .adm-stat-label { font-size:10px; letter-spacing:2px; color:var(--pw-text-dim); font-family:'JetBrains Mono',monospace; margin-bottom:6px; }
        .adm-stat-value { font-size:28px; font-weight:800; color:#fff; line-height:1; }
        .adm-stat-sub   { font-size:11px; color:var(--pw-text-dim); margin-top:4px; }

        .adm-section-title {
            font-size:10px; font-weight:700; letter-spacing:2.5px;
            color:var(--pw-text-dim); font-family:'JetBrains Mono',monospace;
            text-transform:uppercase; margin-bottom:14px;
            display:flex; align-items:center; gap:8px;
        }
        .adm-section-title::after { content:''; flex:1; height:1px; background:var(--pw-border); }

        /* ── Table ── */
        .adm-table { width:100%; border-collapse:collapse; }
        .adm-table th {
            padding:10px 14px; text-align:left;
            font-size:10px; letter-spacing:1.5px;
            color:var(--pw-text-dim); font-family:'JetBrains Mono',monospace;
            border-bottom:1px solid var(--pw-border);
            background:var(--pw-bg3);
        }
        .adm-table td {
            padding:11px 14px; font-size:13px;
            color:var(--pw-text); border-bottom:1px solid var(--pw-border);
            vertical-align:middle;
        }
        .adm-table tr:last-child td { border-bottom:none; }
        .adm-table tr:hover td { background:rgba(245,158,11,.04); }

        /* ── Badges ── */
        .badge-admin { background:rgba(245,158,11,.15); color:#f59e0b; border:1px solid rgba(245,158,11,.3); padding:3px 10px; border-radius:12px; font-size:11px; font-weight:700; font-family:'JetBrains Mono',monospace; }
        .badge-user  { background:rgba(41,197,255,.12); color:#29c5ff; border:1px solid rgba(41,197,255,.25); padding:3px 10px; border-radius:12px; font-size:11px; font-weight:700; font-family:'JetBrains Mono',monospace; }

        /* ── Buttons ── */
        .btn-adm-primary {
            background:linear-gradient(135deg,#f59e0b,#d97706);
            color:#07111d; border:none; padding:9px 20px;
            border-radius:8px; font-size:13px; font-weight:700;
            cursor:pointer; text-decoration:none;
            display:inline-flex; align-items:center; gap:6px;
            transition:.2s;
        }
        .btn-adm-primary:hover { opacity:.88; color:#07111d; transform:translateY(-1px); }

        .btn-adm-outline {
            background:transparent; color:var(--pw-cyan);
            border:1px solid var(--pw-border2); padding:7px 16px;
            border-radius:8px; font-size:12px; font-weight:600;
            cursor:pointer; text-decoration:none;
            display:inline-flex; align-items:center; gap:5px;
            transition:.2s;
        }
        .btn-adm-outline:hover { background:var(--pw-cyan-dim); color:var(--pw-cyan); }

        .btn-adm-danger {
            background:rgba(239,68,68,.12); color:var(--pw-red);
            border:1px solid rgba(239,68,68,.3); padding:7px 14px;
            border-radius:8px; font-size:12px; font-weight:600;
            cursor:pointer; text-decoration:none;
            display:inline-flex; align-items:center; gap:5px;
            transition:.2s;
        }
        .btn-adm-danger:hover { background:rgba(239,68,68,.22); }

        /* ── Form ── */
        .adm-form-label {
            font-size:11px; letter-spacing:.5px;
            color:var(--pw-text-dim); display:block; margin-bottom:6px;
        }
        .adm-form-control {
            width:100%; background:var(--pw-bg3);
            border:1px solid var(--pw-border); color:var(--pw-text);
            border-radius:9px; padding:10px 14px; font-size:13px;
            transition:.2s; outline:none;
        }
        .adm-form-control:focus { border-color:#f59e0b; box-shadow:0 0 0 3px rgba(245,158,11,.1); }
        .adm-form-control::placeholder { color:var(--pw-text-dim); }
        select.adm-form-control option { background:var(--pw-bg2); }
        textarea.adm-form-control { min-height:140px; resize:vertical; }

        /* ── Alert ── */
        .adm-alert { padding:12px 16px; border-radius:9px; font-size:13px; margin-bottom:18px; display:flex; align-items:center; gap:8px; }
        .adm-alert-success { background:rgba(34,197,94,.12); border:1px solid rgba(34,197,94,.3); color:#86efac; }
        .adm-alert-error   { background:rgba(239,68,68,.12);  border:1px solid rgba(239,68,68,.3);  color:#fca5a5; }

        /* ── Pagination overrides ── */
        .pagination { gap:4px; flex-wrap:wrap; }
        .page-link { background:var(--pw-bg3); border:1px solid var(--pw-border); color:var(--pw-text); border-radius:7px !important; font-size:12px; padding:6px 12px; }
        .page-link:hover { background:rgba(245,158,11,.12); color:#f59e0b; border-color:rgba(245,158,11,.3); }
        .page-item.active .page-link { background:#f59e0b; border-color:#f59e0b; color:#07111d; font-weight:700; }
        .page-item.disabled .page-link { opacity:.4; }
    </style>

    @yield('head')
</head>
<body>
<div class="adm-layout">

    {{-- ── Sidebar ── --}}
    <aside class="adm-sidebar">
        <div class="adm-logo">
            <div class="adm-logo-icon"><i class="bi bi-shield-lock-fill"></i></div>
            <div>
                <span class="adm-logo-name d-block">PORTWATCH</span>
                <span class="adm-logo-sub">ADMIN PANEL</span>
            </div>
        </div>

        <div class="adm-badge">
            <i class="bi bi-circle-fill" style="font-size:7px;"></i>
            ADMINISTRATOR
        </div>

        <nav class="adm-nav">
            <div class="adm-nav-label">MAIN</div>

            <a href="{{ route('admin.dashboard') }}"
               class="adm-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>

            <div class="adm-nav-divider"></div>
            <div class="adm-nav-label">KELOLA DATA</div>

            <a href="{{ route('admin.users.index') }}"
               class="adm-nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i>
                <span>Kelola User</span>
            </a>

            <a href="{{ route('admin.ports.index') }}"
               class="adm-nav-item {{ request()->routeIs('admin.ports.*') ? 'active' : '' }}">
                <i class="bi bi-anchor"></i>
                <span>Dataset Pelabuhan</span>
            </a>

            <a href="{{ route('admin.articles.index') }}"
               class="adm-nav-item {{ request()->routeIs('admin.articles.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text-fill"></i>
                <span>Artikel Analisis</span>
            </a>

            <a href="{{ route('admin.watchlists.index') }}"
               class="adm-nav-item {{ request()->routeIs('admin.watchlists.*') ? 'active' : '' }}">
                <i class="bi bi-bookmark-star-fill"></i>
                <span>Watchlist User</span>
            </a>

            <div class="adm-nav-divider"></div>
            <div class="adm-nav-label">AKSI</div>

            <a href="{{ route('dashboard') }}" class="adm-nav-item" target="_blank">
                <i class="bi bi-box-arrow-up-right"></i>
                <span>Lihat Platform User</span>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="adm-nav-item adm-nav-logout w-100">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </button>
            </form>
        </nav>

        <div class="adm-sidebar-footer">
            <div class="adm-sf-row">
                <span class="adm-sf-label">ROLE</span>
                <span class="adm-sf-val">ADMIN</span>
            </div>
            <div class="adm-sf-row">
                <span class="adm-sf-label">STATUS</span>
                <span class="adm-sf-val" style="color:var(--pw-green);">AKTIF</span>
            </div>
        </div>
    </aside>

    {{-- ── Main ── --}}
    <div class="adm-main-wrap">

        {{-- Topbar --}}
        <header class="adm-topbar">
            <div>
                <div class="adm-topbar-title">@yield('breadcrumb', 'ADMIN PANEL')</div>
                <div class="adm-topbar-sub">PortWatch AI — Sistem Manajemen</div>
            </div>
            <div class="adm-topbar-right">
                <div class="adm-user-badge">
                    <div class="adm-user-dot">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                    <div>
                        <div style="font-size:13px;font-weight:600;color:#fff;">{{ Auth::user()->name }}</div>
                        <div style="font-size:11px;color:#f59e0b;font-family:'JetBrains Mono',monospace;letter-spacing:1px;">ADMINISTRATOR</div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Content --}}
        <main class="adm-content">

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="adm-alert adm-alert-success">
                    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="adm-alert adm-alert-error">
                    <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

{{-- Konfirmasi hapus --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-confirm-delete').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            if (!confirm('Yakin ingin menghapus data ini? Tindakan tidak dapat dibatalkan.')) {
                e.preventDefault();
            }
        });
    });
});
</script>

@yield('scripts')
</body>
</html>
