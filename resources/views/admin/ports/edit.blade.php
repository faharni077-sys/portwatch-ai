@extends('layouts.admin')
@section('title', 'Edit Pelabuhan')
@section('breadcrumb', 'EDIT PELABUHAN')

@section('content')

<div class="mb-4">
    <a href="{{ route('admin.ports.index') }}" class="btn-adm-outline">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="adm-card" style="max-width:640px;">
    <div class="adm-section-title">
        <i class="bi bi-pencil me-2 text-cyan"></i>EDIT PELABUHAN — {{ strtoupper($port->port_name) }}
    </div>

    <form method="POST" action="{{ route('admin.ports.update', $port) }}">
        @csrf @method('PUT')

        <div class="mb-3">
            <label class="adm-form-label">NEGARA <span style="color:var(--pw-red);">*</span></label>
            <select name="country_id" class="adm-form-control @error('country_id') border-danger @enderror" required>
                <option value="">— Pilih Negara —</option>
                @foreach($countries as $c)
                    <option value="{{ $c->id }}" {{ old('country_id', $port->country_id) == $c->id ? 'selected' : '' }}>
                        {{ $c->name }} ({{ $c->code }})
                    </option>
                @endforeach
            </select>
            @error('country_id')
                <div style="color:var(--pw-red);font-size:12px;margin-top:4px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="adm-form-label">NAMA PELABUHAN <span style="color:var(--pw-red);">*</span></label>
            <input type="text" name="port_name"
                   class="adm-form-control @error('port_name') border-danger @enderror"
                   value="{{ old('port_name', $port->port_name) }}" required>
            @error('port_name')
                <div style="color:var(--pw-red);font-size:12px;margin-top:4px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="adm-form-label">KOTA</label>
            <input type="text" name="city" class="adm-form-control"
                   value="{{ old('city', $port->city) }}" placeholder="Opsional">
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="adm-form-label">LATITUDE</label>
                <input type="number" name="latitude" step="0.0000001"
                       class="adm-form-control @error('latitude') border-danger @enderror"
                       value="{{ old('latitude', $port->latitude) }}">
                @error('latitude')
                    <div style="color:var(--pw-red);font-size:12px;margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label class="adm-form-label">LONGITUDE</label>
                <input type="number" name="longitude" step="0.0000001"
                       class="adm-form-control @error('longitude') border-danger @enderror"
                       value="{{ old('longitude', $port->longitude) }}">
                @error('longitude')
                    <div style="color:var(--pw-red);font-size:12px;margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div style="background:var(--pw-bg3);border:1px solid var(--pw-border);border-radius:9px;padding:12px 14px;margin-bottom:20px;font-size:12px;color:var(--pw-text-dim);">
            <i class="bi bi-info-circle me-1"></i>
            ID Pelabuhan: <strong style="color:#fff;font-family:'JetBrains Mono',monospace;">#{{ $port->id }}</strong>
        </div>

        <div class="d-flex gap-3">
            <button type="submit" class="btn-adm-primary">
                <i class="bi bi-check-lg"></i> Perbarui Pelabuhan
            </button>
            <a href="{{ route('admin.ports.index') }}" class="btn-adm-outline">Batal</a>
        </div>
    </form>
</div>

@endsection
