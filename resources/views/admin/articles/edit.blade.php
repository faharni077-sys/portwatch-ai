@extends('layouts.admin')
@section('title', 'Edit Artikel')
@section('breadcrumb', 'EDIT ARTIKEL ANALISIS')

@section('content')

<div class="mb-4">
    <a href="{{ route('admin.articles.index') }}" class="btn-adm-outline">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="adm-card" style="max-width:780px;">
    <div class="adm-section-title">
        <i class="bi bi-pencil me-2 text-cyan"></i>EDIT ARTIKEL — #{{ $article->id }}
    </div>

    <form method="POST" action="{{ route('admin.articles.update', $article) }}">
        @csrf @method('PUT')

        <div class="mb-3">
            <label class="adm-form-label">JUDUL ARTIKEL <span style="color:var(--pw-red);">*</span></label>
            <input type="text" name="title"
                   class="adm-form-control @error('title') border-danger @enderror"
                   value="{{ old('title', $article->title) }}" required>
            @error('title')
                <div style="color:var(--pw-red);font-size:12px;margin-top:4px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="adm-form-label">PENULIS</label>
            <input type="text" name="author" class="adm-form-control"
                   value="{{ old('author', $article->author) }}">
        </div>

        <div class="mb-4">
            <label class="adm-form-label">ISI ARTIKEL <span style="color:var(--pw-red);">*</span></label>
            <textarea name="content"
                      class="adm-form-control @error('content') border-danger @enderror"
                      style="min-height:280px;"
                      required>{{ old('content', $article->content) }}</textarea>
            @error('content')
                <div style="color:var(--pw-red);font-size:12px;margin-top:4px;">{{ $message }}</div>
            @enderror
        </div>

        <div style="background:var(--pw-bg3);border:1px solid var(--pw-border);border-radius:9px;padding:12px 14px;margin-bottom:20px;font-size:12px;color:var(--pw-text-dim);">
            <i class="bi bi-info-circle me-1"></i>
            Dibuat: <strong style="color:#fff;">{{ $article->created_at->format('d F Y, H:i') }}</strong> ·
            Terakhir diperbarui: <strong style="color:#fff;">{{ $article->updated_at->format('d F Y, H:i') }}</strong>
        </div>

        <div class="d-flex gap-3">
            <button type="submit" class="btn-adm-primary">
                <i class="bi bi-check-lg"></i> Simpan Perubahan
            </button>
            <a href="{{ route('admin.articles.index') }}" class="btn-adm-outline">Batal</a>
        </div>
    </form>
</div>

@endsection
