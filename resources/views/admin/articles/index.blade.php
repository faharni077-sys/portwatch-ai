@extends('layouts.admin')
@section('title', 'Artikel Analisis')
@section('breadcrumb', 'ARTIKEL ANALISIS')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h5 style="color:#fff;font-weight:800;margin:0;">Artikel Analisis</h5>
        <p style="color:var(--pw-text-dim);font-size:13px;margin:0;">
            Kelola artikel dan laporan analisis supply chain.
        </p>
    </div>
    <a href="{{ route('admin.articles.create') }}" class="btn-adm-primary">
        <i class="bi bi-file-earmark-plus"></i> Tulis Artikel
    </a>
</div>

{{-- Filter --}}
<div class="adm-card mb-4">
    <form method="GET" action="{{ route('admin.articles.index') }}" class="d-flex gap-3 flex-wrap align-items-end">
        <div style="flex:1;min-width:220px;">
            <label class="adm-form-label">CARI JUDUL / PENULIS</label>
            <input type="text" name="search" class="adm-form-control"
                   placeholder="Judul atau nama penulis..." value="{{ request('search') }}">
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn-adm-primary"><i class="bi bi-search"></i> Cari</button>
            <a href="{{ route('admin.articles.index') }}" class="btn-adm-outline"><i class="bi bi-x"></i> Reset</a>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="adm-card">
    <div class="adm-section-title">
        <i class="bi bi-file-earmark-text me-2" style="color:#f59e0b;"></i>
        DAFTAR ARTIKEL
        <span style="margin-left:auto;font-size:10px;font-family:'JetBrains Mono',monospace;color:var(--pw-text-dim);">
            {{ $articles->total() }} ARTIKEL
        </span>
    </div>

    @if($articles->isEmpty())
        <div style="text-align:center;padding:56px;color:var(--pw-text-dim);">
            <i class="bi bi-file-earmark-text" style="font-size:40px;display:block;margin-bottom:12px;opacity:.3;"></i>
            Belum ada artikel. Mulai tulis sekarang!
            <div class="mt-3">
                <a href="{{ route('admin.articles.create') }}" class="btn-adm-primary">
                    <i class="bi bi-file-earmark-plus"></i> Tulis Artikel Pertama
                </a>
            </div>
        </div>
    @else
    <div style="overflow-x:auto;">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>JUDUL</th>
                    <th>PENULIS</th>
                    <th>ISI (RINGKASAN)</th>
                    <th>TANGGAL</th>
                    <th>AKSI</th>
                </tr>
            </thead>
            <tbody>
                @foreach($articles as $article)
                <tr>
                    <td style="color:var(--pw-text-dim);font-family:'JetBrains Mono',monospace;font-size:12px;">
                        {{ $articles->firstItem() + $loop->index }}
                    </td>
                    <td>
                        <div style="font-weight:600;color:#fff;max-width:260px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                            {{ $article->title }}
                        </div>
                    </td>
                    <td style="color:var(--pw-text-dim);font-size:13px;">
                        {{ $article->author ?? '—' }}
                    </td>
                    <td style="color:var(--pw-text-dim);font-size:12px;max-width:220px;">
                        <div style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                            {{ Str::limit(strip_tags($article->content), 80) }}
                        </div>
                    </td>
                    <td style="color:var(--pw-text-dim);font-size:12px;font-family:'JetBrains Mono',monospace;white-space:nowrap;">
                        {{ $article->created_at->format('d/m/Y') }}
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.articles.edit', $article) }}" class="btn-adm-outline">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('admin.articles.destroy', $article) }}">
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
    <div class="mt-4">{{ $articles->links() }}</div>
    @endif
</div>

@endsection
