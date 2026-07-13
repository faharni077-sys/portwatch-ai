@extends('layouts.app')
@section('title', 'Profile')
@section('breadcrumb', 'ACCOUNT PROFILE')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <div class="pw-section-title" style="margin-bottom:4px;">
            <i class="bi bi-person-badge me-2 text-cyan"></i>ACCOUNT PROFILE
        </div>
        <p style="color:var(--pw-text-dim);font-size:13px;margin:0;">
            Manage your PortWatch AI account credentials and security settings.
        </p>
    </div>
    <div style="display:flex;align-items:center;gap:12px;">
        <div style="width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,var(--pw-cyan),#0ea5e9);display:flex;align-items:center;justify-content:center;font-size:20px;color:var(--pw-bg);font-weight:800;">
            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
        </div>
        <div>
            <div style="font-weight:700;color:#fff;">{{ Auth::user()->name }}</div>
            <div style="font-size:12px;color:var(--pw-text-dim);">{{ Auth::user()->email }}</div>
        </div>
    </div>
</div>

{{-- Session status --}}
@if(session('status') === 'profile-updated')
<div style="background:rgba(34,197,94,.12);border:1px solid rgba(34,197,94,.3);color:#86efac;border-radius:10px;padding:12px 18px;font-size:13px;margin-bottom:20px;">
    <i class="bi bi-check-circle me-2"></i> Profile updated successfully.
</div>
@endif
@if(session('status') === 'password-updated')
<div style="background:rgba(34,197,94,.12);border:1px solid rgba(34,197,94,.3);color:#86efac;border-radius:10px;padding:12px 18px;font-size:13px;margin-bottom:20px;">
    <i class="bi bi-check-circle me-2"></i> Password updated successfully.
</div>
@endif

<div class="row g-4">

    {{-- ---- Update Profile Info ---- --}}
    <div class="col-lg-6">
        <div class="pw-card">
            <div class="pw-section-title"><i class="bi bi-person me-2 text-cyan"></i>PROFILE INFORMATION</div>
            <p style="font-size:13px;color:var(--pw-text-dim);margin-bottom:20px;">
                Update your account name and email address.
            </p>

            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('patch')

                <div class="mb-3">
                    <label style="font-size:11px;letter-spacing:1.5px;color:var(--pw-text-dim);font-family:'JetBrains Mono',monospace;display:block;margin-bottom:6px;">FULL NAME</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                        class="pw-input @error('name') pw-input-error @enderror"
                        required autofocus autocomplete="name">
                    @error('name')
                        <div style="color:var(--pw-red);font-size:12px;margin-top:4px;"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label style="font-size:11px;letter-spacing:1.5px;color:var(--pw-text-dim);font-family:'JetBrains Mono',monospace;display:block;margin-bottom:6px;">EMAIL ADDRESS</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                        class="pw-input @error('email') pw-input-error @enderror"
                        required autocomplete="username">
                    @error('email')
                        <div style="color:var(--pw-red);font-size:12px;margin-top:4px;"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn-pw-primary">
                    <i class="bi bi-check2 me-2"></i> Save Changes
                </button>
            </form>
        </div>
    </div>

    {{-- ---- Update Password ---- --}}
    <div class="col-lg-6">
        <div class="pw-card">
            <div class="pw-section-title"><i class="bi bi-lock me-2 text-cyan"></i>CHANGE PASSWORD</div>
            <p style="font-size:13px;color:var(--pw-text-dim);margin-bottom:20px;">
                Use a strong, unique password to keep your account secure.
            </p>

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                @method('put')

                <div class="mb-3">
                    <label style="font-size:11px;letter-spacing:1.5px;color:var(--pw-text-dim);font-family:'JetBrains Mono',monospace;display:block;margin-bottom:6px;">CURRENT PASSWORD</label>
                    <div style="position:relative;">
                        <input type="password" id="cur_pwd" name="current_password"
                            class="pw-input @error('current_password', 'updatePassword') pw-input-error @enderror"
                            autocomplete="current-password">
                        <button type="button" onclick="togglePwd('cur_pwd','cur_eye')"
                            style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--pw-text-dim);cursor:pointer;">
                            <i class="bi bi-eye" id="cur_eye"></i>
                        </button>
                    </div>
                    @error('current_password', 'updatePassword')
                        <div style="color:var(--pw-red);font-size:12px;margin-top:4px;"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label style="font-size:11px;letter-spacing:1.5px;color:var(--pw-text-dim);font-family:'JetBrains Mono',monospace;display:block;margin-bottom:6px;">NEW PASSWORD</label>
                    <div style="position:relative;">
                        <input type="password" id="new_pwd" name="password"
                            class="pw-input @error('password', 'updatePassword') pw-input-error @enderror"
                            autocomplete="new-password">
                        <button type="button" onclick="togglePwd('new_pwd','new_eye')"
                            style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--pw-text-dim);cursor:pointer;">
                            <i class="bi bi-eye" id="new_eye"></i>
                        </button>
                    </div>
                    @error('password', 'updatePassword')
                        <div style="color:var(--pw-red);font-size:12px;margin-top:4px;"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label style="font-size:11px;letter-spacing:1.5px;color:var(--pw-text-dim);font-family:'JetBrains Mono',monospace;display:block;margin-bottom:6px;">CONFIRM NEW PASSWORD</label>
                    <input type="password" name="password_confirmation"
                        class="pw-input"
                        autocomplete="new-password">
                </div>

                <button type="submit" class="btn-pw-primary">
                    <i class="bi bi-shield-lock me-2"></i> Update Password
                </button>
            </form>
        </div>
    </div>

    {{-- ---- Account Info ---- --}}
    <div class="col-lg-6">
        <div class="pw-card">
            <div class="pw-section-title"><i class="bi bi-info-circle me-2 text-cyan"></i>ACCOUNT DETAILS</div>
            <div style="display:flex;flex-direction:column;gap:0;">
                <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--pw-border);font-size:13px;">
                    <span style="color:var(--pw-text-dim);">User ID</span>
                    <span style="font-family:'JetBrains Mono',monospace;color:var(--pw-cyan);">#{{ Auth::user()->id }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--pw-border);font-size:13px;">
                    <span style="color:var(--pw-text-dim);">Name</span>
                    <span style="font-weight:600;">{{ Auth::user()->name }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--pw-border);font-size:13px;">
                    <span style="color:var(--pw-text-dim);">Email</span>
                    <span style="font-weight:600;">{{ Auth::user()->email }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--pw-border);font-size:13px;">
                    <span style="color:var(--pw-text-dim);">Member Since</span>
                    <span style="font-family:'JetBrains Mono',monospace;">{{ Auth::user()->created_at->format('d M Y') }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:10px 0;font-size:13px;">
                    <span style="color:var(--pw-text-dim);">Platform Access</span>
                    <span class="risk-badge low">ACTIVE</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ---- Danger Zone ---- --}}
    <div class="col-lg-6">
        <div class="pw-card" style="border-color:rgba(239,68,68,.25);">
            <div class="pw-section-title" style="color:var(--pw-red);">
                <i class="bi bi-exclamation-triangle me-2"></i>DANGER ZONE
            </div>
            <p style="font-size:13px;color:var(--pw-text-dim);margin-bottom:20px;">
                Once you delete your account, all data will be permanently removed.
                This action cannot be undone.
            </p>

            <button onclick="document.getElementById('deleteModal').style.display='flex'"
                style="background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.3);color:var(--pw-red);
                       border-radius:9px;padding:10px 22px;font-size:13px;font-weight:700;cursor:pointer;
                       display:flex;align-items:center;gap:8px;transition:.2s;">
                <i class="bi bi-trash3"></i> Delete Account
            </button>
        </div>
    </div>

</div>

{{-- ---- Delete Modal ---- --}}
<div id="deleteModal" style="
    display:none;position:fixed;inset:0;z-index:9999;
    background:rgba(7,17,29,.85);backdrop-filter:blur(4px);
    align-items:center;justify-content:center;
">
    <div style="
        background:var(--pw-bg2);border:1px solid rgba(239,68,68,.3);
        border-radius:16px;padding:32px;max-width:440px;width:90%;
        box-shadow:0 24px 80px rgba(0,0,0,.6);
    ">
        <div style="font-size:11px;letter-spacing:3px;color:var(--pw-red);font-family:'JetBrains Mono',monospace;margin-bottom:12px;">⚠ CONFIRM DELETION</div>
        <h3 style="font-size:20px;font-weight:800;color:#fff;margin-bottom:10px;">Delete your account?</h3>
        <p style="font-size:14px;color:var(--pw-text-dim);margin-bottom:24px;line-height:1.6;">
            This will permanently delete all your data. Enter your password to confirm.
        </p>

        <form method="POST" action="{{ route('profile.destroy') }}">
            @csrf
            @method('delete')

            <div class="mb-4">
                <label style="font-size:11px;letter-spacing:1.5px;color:var(--pw-text-dim);font-family:'JetBrains Mono',monospace;display:block;margin-bottom:6px;">PASSWORD</label>
                <input type="password" name="password"
                    class="pw-input @error('password', 'userDeletion') pw-input-error @enderror"
                    placeholder="Your current password">
                @error('password', 'userDeletion')
                    <div style="color:var(--pw-red);font-size:12px;margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            <div style="display:flex;gap:10px;">
                <button type="button" onclick="document.getElementById('deleteModal').style.display='none'"
                    class="btn-pw-outline" style="flex:1;">
                    Cancel
                </button>
                <button type="submit"
                    style="flex:1;background:rgba(239,68,68,.8);border:none;color:#fff;
                           border-radius:9px;padding:10px;font-weight:700;font-size:13px;cursor:pointer;">
                    <i class="bi bi-trash3 me-2"></i> Delete
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
function togglePwd(inputId, eyeId) {
    const input = document.getElementById(inputId);
    const eye   = document.getElementById(eyeId);
    if (input.type === 'password') { input.type = 'text'; eye.className = 'bi bi-eye-slash'; }
    else { input.type = 'password'; eye.className = 'bi bi-eye'; }
}

// Auto-open delete modal if there are deletion errors
@if($errors->userDeletion->isNotEmpty())
document.getElementById('deleteModal').style.display = 'flex';
@endif
</script>

<style>
.pw-input-error { border-color: rgba(239,68,68,.5) !important; }
</style>
@endsection
