@extends('layouts.admin')
@section('title', 'Tulis Artikel')
@section('breadcrumb', 'TULIS ARTIKEL ANALISIS')

@section('content')

<div class="mb-4">
    <a href="{{ route('admin.articles.index') }}" class="btn-adm-outline">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="adm-card" style="max-width:780px;">
    <div class="adm-section-title">
        <i class="bi bi-file-earmark-plus me-2" style="color:#f59e0b;"></i>TULIS ARTIKEL ANALISIS BARU
    </div>

    <form method="POST" action="{{ route('admin.articles.store') }}">
        @csrf

        <div class="mb-3">
            <label class="adm-form-label">JUDUL ARTIKEL <span style="color:var(--pw-red);">*</span></label>
            <input type="text" name="title"
                   class="adm-form-control @error('title') border-danger @enderror"
                   value="{{ old('title') }}"
                   placeholder="Contoh: Analisis Risiko Supply Chain Eropa Q3 2025"
                   required>
            @error('title')
                <div style="color:var(--pw-red);font-size:12px;margin-top:4px;">
                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="adm-form-label">PENULIS</label>
            <input type="text" name="author" class="adm-form-control"
                   value="{{ old('author', Auth::user()->name) }}"
                   placeholder="Nama penulis (default: nama admin)">
        </div>

        <div class="mb-4">
            <label class="adm-form-label">ISI ARTIKEL <span style="color:var(--pw-red);">*</span></label>
            <textarea name="content"
                      class="adm-form-control @error('content') border-danger @enderror"
                      style="min-height:280px;"
                      placeholder="Tulis isi artikel analisis supply chain di sini..."
                      required>{{ old('content') }}</textarea>
            @error('content')
                <div style="color:var(--pw-red);font-size:12px;margin-top:4px;">
                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                </div>
            @enderror
            <div style="font-size:11px;color:var(--pw-text-dim);margin-top:5px;">
                <i class="bi bi-info-circle me-1"></i>
                Artikel ini akan tampil sebagai konten analisis pada platform PortWatch AI.
            </div>
        </div>

        <div class="d-flex gap-3">
            <button type="submit" class="btn-adm-primary">
                <i class="bi bi-send-fill"></i> Publikasikan Artikel
            </button>
            <a href="{{ route('admin.articles.index') }}" class="btn-adm-outline">Batal</a>
        </div>
    </form>
</div>

@endsection
