@extends('layouts.admin')
@section('title', 'Watchlist Pengguna')
@section('breadcrumb', 'WATCHLIST PENGGUNA')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h5 style="color:#fff;font-weight:800;margin:0;">Watchlist Pengguna</h5>
        <p style="color:var(--pw-text-dim);font-size:13px;margin:0;">
            Daftar negara yang dipantau oleh setiap pengguna platform.
        </p>
    </div>
    <div style="font-size:11px;font-family:'JetBrains Mono',monospace;color:var(--pw-text-dim);
                background:var(--pw-bg3);border:1px solid var(--pw-border);padding:8px 14px;border-radius:8px;">
        <i class="bi bi-eye me-1"></i> READ ONLY — Data watchlist user
    </div>
</div>

{{-- Filter --}}
<div class="adm-card mb-4">
    <form method="GET" action="{{ route('admin.watchlists.index') }}" class="d-flex gap-3 flex-wrap align-items-end">
        <div style="flex:1;min-width:200px;">
            <label class="adm-form-label">CARI NAMA USER / NEGARA</label>
            <input type="text" name="search" class="adm-form-control"
                   placeholder="Nama user atau negara..." value="{{ request('search') }}">
        </div>
        <div style="min-width:200px;">
            <label class="adm-form-label">FILTER USER</label>
            <select name="user_id" class="adm-form-control">
                <option value="">Semua User</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                        {{ $u->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn-adm-primary"><i class="bi bi-search"></i> Cari</button>
            <a href="{{ route('admin.watchlists.index') }}" class="btn-adm-outline"><i class="bi bi-x"></i> Reset</a>
        </div>
    </form>
</div>

{{-- Stats row --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="adm-stat-card">
            <div class="adm-stat-label"><i class="bi bi-bookmark-star me-1"></i>TOTAL WATCHLIST</div>
            <div class="adm-stat-value" style="color:#f59e0b;">{{ $watchlists->total() }}</div>
            <div class="adm-stat-sub">Entri aktif dari semua user</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="adm-stat-card">
            <div class="adm-stat-label"><i class="bi bi-people me-1"></i>USER DENGAN WATCHLIST</div>
            <div class="adm-stat-value text-cyan">{{ $users->count() }}</div>
            <div class="adm-stat-sub">User aktif memantau</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="adm-stat-card">
            <div class="adm-stat-label"><i class="bi bi-globe2 me-1"></i>HALAMAN</div>
            <div class="adm-stat-value" style="color:var(--pw-green);">{{ $watchlists->lastPage() }}</div>
            <div class="adm-stat-sub">Total halaman data</div>
        </div>
    </div>
</div>

{{-- Table --}}
<div class="adm-card">
    <div class="adm-section-title">
        <i class="bi bi-bookmark-star me-2" style="color:#f59e0b;"></i>
        DATA WATCHLIST
        <span style="margin-left:auto;font-size:10px;font-family:'JetBrains Mono',monospace;color:var(--pw-text-dim);">
            {{ $watchlists->total() }} ENTRI
        </span>
    </div>

    @if($watchlists->isEmpty())
        <div style="text-align:center;padding:56px;color:var(--pw-text-dim);">
            <i class="bi bi-bookmark-star" style="font-size:40px;display:block;margin-bottom:12px;opacity:.3;"></i>
            Belum ada data watchlist.
        </div>
    @else
    <div style="overflow-x:auto;">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>USER</th>
                    <th>EMAIL</th>
                    <th>NEGARA DIPANTAU</th>
                    <th>KODE</th>
                    <th>DITAMBAHKAN</th>
                </tr>
            </thead>
            <tbody>
                @foreach($watchlists as $wl)
                <tr>
                    <td style="color:var(--pw-text-dim);font-family:'JetBrains Mono',monospace;font-size:12px;">
                        {{ $watchlists->firstItem() + $loop->index }}
                    </td>
                    <td>
                        <div style="font-weight:600;color:#fff;">
                            {{ $wl->user?->name ?? '—' }}
                        </div>
                    </td>
                    <td style="color:var(--pw-text-dim);font-size:12px;">
                        {{ $wl->user?->email ?? '—' }}
                    </td>
                    <td>
                        <div style="color:var(--pw-cyan);font-weight:600;">
                            {{ $wl->country?->name ?? '—' }}
                        </div>
                    </td>
                    <td style="font-family:'JetBrains Mono',monospace;font-size:12px;color:var(--pw-text-dim);">
                        {{ $wl->country?->code ?? '—' }}
                    </td>
                    <td style="color:var(--pw-text-dim);font-size:12px;font-family:'JetBrains Mono',monospace;white-space:nowrap;">
                        {{ $wl->created_at->format('d/m/Y H:i') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $watchlists->links() }}</div>
    @endif
</div>

@endsection
