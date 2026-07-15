@extends('layouts.admin')
@section('title', 'Tambah User')
@section('breadcrumb', 'TAMBAH USER')

@section('content')

<div class="mb-4">
    <a href="{{ route('admin.users.index') }}" class="btn-adm-outline">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="adm-card" style="max-width:600px;">
    <div class="adm-section-title"><i class="bi bi-person-plus me-2 text-cyan"></i>FORM TAMBAH USER</div>

    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf

        <div class="mb-3">
            <label class="adm-form-label">NAMA LENGKAP <span style="color:var(--pw-red);">*</span></label>
            <input type="text" name="name" class="adm-form-control @error('name') border-danger @enderror"
                   value="{{ old('name') }}" placeholder="Nama lengkap user" required>
            @error('name')
                <div style="color:var(--pw-red);font-size:12px;margin-top:4px;"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="adm-form-label">EMAIL <span style="color:var(--pw-red);">*</span></label>
            <input type="email" name="email" class="adm-form-control @error('email') border-danger @enderror"
                   value="{{ old('email') }}" placeholder="email@contoh.com" required>
            @error('email')
                <div style="color:var(--pw-red);font-size:12px;margin-top:4px;"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="adm-form-label">ROLE <span style="color:var(--pw-red);">*</span></label>
            <select name="role" class="adm-form-control @error('role') border-danger @enderror" required>
                <option value="user"  {{ old('role', 'user') === 'user'  ? 'selected' : '' }}>User</option>
                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
            @error('role')
                <div style="color:var(--pw-red);font-size:12px;margin-top:4px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="adm-form-label">PASSWORD <span style="color:var(--pw-red);">*</span></label>
            <input type="password" name="password"
                   class="adm-form-control @error('password') border-danger @enderror"
                   placeholder="Minimal 8 karakter" required>
            @error('password')
                <div style="color:var(--pw-red);font-size:12px;margin-top:4px;"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label class="adm-form-label">KONFIRMASI PASSWORD <span style="color:var(--pw-red);">*</span></label>
            <input type="password" name="password_confirmation"
                   class="adm-form-control" placeholder="Ulangi password" required>
        </div>

        <div class="d-flex gap-3">
            <button type="submit" class="btn-adm-primary">
                <i class="bi bi-check-lg"></i> Simpan User
            </button>
            <a href="{{ route('admin.users.index') }}" class="btn-adm-outline">Batal</a>
        </div>
    </form>
</div>

@endsection
