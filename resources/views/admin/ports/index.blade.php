@extends('layouts.admin')
@section('title', 'Dataset Pelabuhan')
@section('breadcrumb', 'DATASET PELABUHAN')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h5 style="color:#fff;font-weight:800;margin:0;">Dataset Pelabuhan</h5>
        <p style="color:var(--pw-text-dim);font-size:13px;margin:0;">
            Kelola data pelabuhan di seluruh dunia.
        </p>
    </div>
    <a href="{{ route('admin.ports.create') }}" class="btn-adm-primary">
        <i class="bi bi-plus-lg"></i> Tambah Pelabuhan
    </a>
</div>

{{-- Filter --}}
<div class="adm-card mb-4">
    <form method="GET" action="{{ route('admin.ports.index') }}" class="d-flex gap-3 flex-wrap align-items-end">
        <div style="flex:1;min-width:200px;">
            <label class="adm-form-label">CARI NAMA / KOTA</label>
            <input type="text" name="search" class="adm-form-control"
                   placeholder="Nama pelabuhan atau kota..." value="{{ request('search') }}">
        </div>
        <div style="min-width:200px;">
            <label class="adm-form-label">NEGARA</label>
            <select name="country_id" class="adm-form-control">
                <option value="">Semua Negara</option>
                @foreach($countries as $c)
                    <option value="{{ $c->id }}" {{ request('country_id') == $c->id ? 'selected' : '' }}>
                        {{ $c->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn-adm-primary"><i class="bi bi-search"></i> Cari</button>
            <a href="{{ route('admin.ports.index') }}" class="btn-adm-outline"><i class="bi bi-x"></i> Reset</a>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="adm-card">
    <div class="adm-section-title">
        <i class="bi bi-anchor me-2" style="color:#f59e0b;"></i>
        DAFTAR PELABUHAN
        <span style="margin-left:auto;font-size:10px;font-family:'JetBrains Mono',monospace;color:var(--pw-text-dim);">
            {{ $ports->total() }} DATA
        </span>
    </div>

    @if($ports->isEmpty())
        <div style="text-align:center;padding:48px;color:var(--pw-text-dim);">
            <i class="bi bi-anchor" style="font-size:40px;display:block;margin-bottom:12px;opacity:.3;"></i>
            Tidak ada pelabuhan ditemukan.
        </div>
    @else
    <div style="overflow-x:auto;">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>NAMA PELABUHAN</th>
                    <th>KOTA</th>
                    <th>NEGARA</th>
                    <th>KOORDINAT</th>
                    <th>AKSI</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ports as $port)
                <tr>
                    <td style="color:var(--pw-text-dim);font-family:'JetBrains Mono',monospace;font-size:12px;">
                        {{ $ports->firstItem() + $loop->index }}
                    </td>
                    <td style="font-weight:600;color:#fff;">{{ $port->port_name }}</td>
                    <td style="color:var(--pw-text-dim);">{{ $port->city ?? '—' }}</td>
                    <td>
                        <span style="color:var(--pw-cyan);font-size:13px;">
                            {{ $port->country->name ?? '—' }}
                        </span>
                    </td>
                    <td style="font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--pw-text-dim);">
                        @if($port->latitude && $port->longitude)
                            {{ number_format($port->latitude, 4) }}, {{ number_format($port->longitude, 4) }}
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.ports.edit', $port) }}" class="btn-adm-outline">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('admin.ports.destroy', $port) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-adm-danger btn-confirm-delete">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $ports->links() }}</div>
    @endif
</div>

@endsection
