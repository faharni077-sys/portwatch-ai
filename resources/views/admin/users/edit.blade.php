@extends('layouts.admin')
@section('title', 'Edit User')
@section('breadcrumb', 'EDIT USER')

@section('content')

<div class="mb-4">
    <a href="{{ route('admin.users.index') }}" class="btn-adm-outline">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="adm-card" style="max-width:600px;">
    <div class="adm-section-title">
        <i class="bi bi-pencil me-2 text-cyan"></i>EDIT USER — {{ strtoupper($user->name) }}
    </div>

    <form method="POST" action="{{ route('admin.users.update', $user) }}">
        @csrf @method('PUT')

        <div class="mb-3">
            <label class="adm-form-label">NAMA LENGKAP <span style="color:var(--pw-red);">*</span></label>
            <input type="text" name="name" class="adm-form-control @error('name') border-danger @enderror"
                   value="{{ old('name', $user->name) }}" required>
            @error('name')
                <div style="color:var(--pw-red);font-size:12px;margin-top:4px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="adm-form-label">EMAIL <span style="color:var(--pw-red);">*</span></label>
            <input type="email" name="email" class="adm-form-control @error('email') border-danger @enderror"
                   value="{{ old('email', $user->email) }}" required>
            @error('email')
                <div style="color:var(--pw-red);font-size:12px;margin-top:4px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="adm-form-label">ROLE <span style="color:var(--pw-red);">*</span></label>
            <select name="role" class="adm-form-control" required>
                <option value="user"  {{ old('role', $user->role) === 'user'  ? 'selected' : '' }}>User</option>
                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
        </div>

        <div class="mb-1">
            <label class="adm-form-label">PASSWORD BARU <span style="color:var(--pw-text-dim);font-weight:400;">(kosongkan jika tidak diubah)</span></label>
            <input type="password" name="password"
                   class="adm-form-control @error('password') border-danger @enderror"
                   placeholder="Minimal 8 karakter">
            @error('password')
                <div style="color:var(--pw-red);font-size:12px;margin-top:4px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label class="adm-form-label">KONFIRMASI PASSWORD BARU</label>
            <input type="password" name="password_confirmation"
                   class="adm-form-control" placeholder="Ulangi password baru">
        </div>

        <div style="background:var(--pw-bg3);border:1px solid var(--pw-border);border-radius:9px;padding:12px 14px;margin-bottom:20px;font-size:12px;color:var(--pw-text-dim);">
            <i class="bi bi-info-circle me-1"></i>
            ID User: <strong style="color:#fff;font-family:'JetBrains Mono',monospace;">#{{ $user->id }}</strong> ·
            Bergabung: <strong style="color:#fff;">{{ $user->created_at->format('d F Y') }}</strong>
        </div>

        <div class="d-flex gap-3">
            <button type="submit" class="btn-adm-primary">
                <i class="bi bi-check-lg"></i> Perbarui User
            </button>
            <a href="{{ route('admin.users.index') }}" class="btn-adm-outline">Batal</a>
        </div>
    </form>
</div>

@endsection
