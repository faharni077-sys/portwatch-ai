@extends('layouts.admin')
@section('title', 'Dashboard Admin')
@section('breadcrumb', 'DASHBOARD ADMIN')

@section('content')

{{-- ── Page header ── --}}
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 style="color:#fff;font-weight:800;margin:0;">Selamat datang, {{ Auth::user()->name }}</h4>
        <p style="color:var(--pw-text-dim);font-size:13px;margin:0;">
            Panel administrasi PortWatch AI — {{ now()->format('d F Y, H:i') }} WIB
        </p>
    </div>
    <div style="font-size:11px;font-family:'JetBrains Mono',monospace;color:#f59e0b;background:rgba(245,158,11,.1);border:1px solid rgba(245,158,11,.3);padding:6px 14px;border-radius:8px;">
        ⬡ PORTWATCH AI · ADMIN MODE
    </div>
</div>

{{-- ── Stat Cards ── --}}
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="adm-stat-card">
            <div class="adm-stat-label"><i class="bi bi-people me-1"></i>TOTAL USER</div>
            <div class="adm-stat-value text-cyan">{{ number_format($stats['total_users']) }}</div>
            <div class="adm-stat-sub">{{ $stats['total_admins'] }} admin · {{ $stats['total_users'] - $stats['total_admins'] }} pengguna</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="adm-stat-card">
            <div class="adm-stat-label"><i class="bi bi-anchor me-1"></i>TOTAL PELABUHAN</div>
            <div class="adm-stat-value" style="color:#f59e0b;">{{ number_format($stats['total_ports']) }}</div>
            <div class="adm-stat-sub">Di {{ $stats['total_countries'] }} negara</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="adm-stat-card">
            <div class="adm-stat-label"><i class="bi bi-file-earmark-text me-1"></i>ARTIKEL ANALISIS</div>
            <div class="adm-stat-value" style="color:var(--pw-green);">{{ number_format($stats['total_articles']) }}</div>
            <div class="adm-stat-sub">Diterbitkan</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="adm-stat-card">
            <div class="adm-stat-label"><i class="bi bi-bookmark-star me-1"></i>TOTAL WATCHLIST</div>
            <div class="adm-stat-value" style="color:var(--pw-red);">{{ number_format($stats['total_watchlists']) }}</div>
            <div class="adm-stat-sub">Dari semua pengguna</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="adm-stat-card">
            <div class="adm-stat-label"><i class="bi bi-globe2 me-1"></i>NEGARA</div>
            <div class="adm-stat-value text-cyan">{{ number_format($stats['total_countries']) }}</div>
            <div class="adm-stat-sub">Terdaftar di database</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="adm-stat-card">
            <div class="adm-stat-label"><i class="bi bi-newspaper me-1"></i>CACHE BERITA</div>
            <div class="adm-stat-value" style="color:#a855f7;">{{ number_format($stats['total_news_cache']) }}</div>
            <div class="adm-stat-sub">Artikel dari GNews API</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="adm-stat-card">
            <div class="adm-stat-label"><i class="bi bi-shield-shaded me-1"></i>RISK SCORE</div>
            <div class="adm-stat-value" style="color:var(--pw-amber);">{{ number_format($stats['total_risk_scores']) }}</div>
            <div class="adm-stat-sub">Kalkulasi tersimpan</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="adm-stat-card">
            <div class="adm-stat-label"><i class="bi bi-cpu me-1"></i>STATUS SISTEM</div>
            <div class="adm-stat-value" style="color:var(--pw-green);font-size:20px;margin-top:4px;">
                <span class="dot-pulse" style="width:10px;height:10px;margin-right:6px;"></span>ONLINE
            </div>
            <div class="adm-stat-sub">Semua API aktif</div>
        </div>
    </div>
</div>

{{-- ── Quick Links ── --}}
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="adm-card">
            <div class="adm-section-title"><i class="bi bi-lightning-charge me-2" style="color:#f59e0b;"></i>AKSI CEPAT</div>
            <div class="d-flex gap-3 flex-wrap">
                <a href="{{ route('admin.users.create') }}"    class="btn-adm-primary"><i class="bi bi-person-plus"></i> Tambah User</a>
                <a href="{{ route('admin.ports.create') }}"    class="btn-adm-primary"><i class="bi bi-anchor"></i> Tambah Pelabuhan</a>
                <a href="{{ route('admin.articles.create') }}" class="btn-adm-primary"><i class="bi bi-file-earmark-plus"></i> Tulis Artikel</a>
                <a href="{{ route('admin.watchlists.index') }}" class="btn-adm-outline"><i class="bi bi-bookmark-star"></i> Lihat Watchlist</a>
            </div>
        </div>
    </div>
</div>

{{-- ── Recent Data ── --}}
<div class="row g-4">
    {{-- Recent Users --}}
    <div class="col-lg-6">
        <div class="adm-card">
            <div class="adm-section-title"><i class="bi bi-people me-2 text-cyan"></i>USER TERBARU</div>
            @if($recent_users->isEmpty())
                <p style="color:var(--pw-text-dim);font-size:13px;">Belum ada user.</p>
            @else
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>NAMA</th>
                        <th>EMAIL</th>
                        <th>ROLE</th>
                        <th>BERGABUNG</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recent_users as $u)
                    <tr>
                        <td style="font-weight:600;color:#fff;">{{ $u->name }}</td>
                        <td style="color:var(--pw-text-dim);font-size:12px;">{{ $u->email }}</td>
                        <td>
                            <span class="{{ $u->role === 'admin' ? 'badge-admin' : 'badge-user' }}">
                                {{ strtoupper($u->role) }}
                            </span>
                        </td>
                        <td style="color:var(--pw-text-dim);font-size:12px;font-family:'JetBrains Mono',monospace;">
                            {{ $u->created_at->format('d/m/Y') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-3">
                <a href="{{ route('admin.users.index') }}" class="btn-adm-outline" style="font-size:12px;">
                    <i class="bi bi-arrow-right"></i> Lihat Semua User
                </a>
            </div>
            @endif
        </div>
    </div>

    {{-- Recent Articles --}}
    <div class="col-lg-6">
        <div class="adm-card">
            <div class="adm-section-title"><i class="bi bi-file-earmark-text me-2" style="color:#f59e0b;"></i>ARTIKEL TERBARU</div>
            @if($recent_articles->isEmpty())
                <p style="color:var(--pw-text-dim);font-size:13px;">Belum ada artikel.</p>
                <a href="{{ route('admin.articles.create') }}" class="btn-adm-primary mt-2">
                    <i class="bi bi-file-earmark-plus"></i> Tulis Artikel Pertama
                </a>
            @else
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>JUDUL</th>
                        <th>PENULIS</th>
                        <th>TANGGAL</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recent_articles as $art)
                    <tr>
                        <td style="font-weight:600;color:#fff;max-width:200px;">
                            <div style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                {{ $art->title }}
                            </div>
                        </td>
                        <td style="color:var(--pw-text-dim);font-size:12px;">{{ $art->author ?? '—' }}</td>
                        <td style="color:var(--pw-text-dim);font-size:12px;font-family:'JetBrains Mono',monospace;">
                            {{ $art->created_at->format('d/m/Y') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-3">
                <a href="{{ route('admin.articles.index') }}" class="btn-adm-outline" style="font-size:12px;">
                    <i class="bi bi-arrow-right"></i> Lihat Semua Artikel
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection
