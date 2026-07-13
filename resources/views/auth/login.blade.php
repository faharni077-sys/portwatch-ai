<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — PortWatch AI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body>
<div class="pw-auth-page">

    {{-- Background globe --}}
    <div class="pw-auth-bg"></div>
    <div class="pw-auth-overlay"></div>

    {{-- Decorative grid lines --}}
    <div class="pw-auth-grid" aria-hidden="true"></div>

    <div class="pw-auth-card">

        {{-- Logo --}}
        <div class="pw-auth-logo">
            <div class="pw-auth-logo-icon"><i class="bi bi-globe-americas"></i></div>
        </div>

        <h1 class="pw-auth-title">Welcome Back</h1>
        <p class="pw-auth-sub">Sign in to your intelligence platform</p>

        {{-- Session status --}}
        @if (session('status'))
            <div class="alert alert-success mb-3 py-2 px-3" style="background:rgba(34,197,94,.12);border:1px solid rgba(34,197,94,.3);color:#86efac;border-radius:9px;font-size:13px;">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- Email --}}
            <div class="pw-auth-input-group">
                <label class="pw-auth-label" for="email">
                    <i class="bi bi-envelope me-1"></i> EMAIL ADDRESS
                </label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="pw-auth-input @error('email') is-error @enderror"
                    placeholder="you@company.com"
                    required
                    autofocus
                    autocomplete="username">
                @error('email')
                    <div class="pw-auth-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                @enderror
            </div>

            {{-- Password --}}
            <div class="pw-auth-input-group">
                <label class="pw-auth-label" for="password">
                    <i class="bi bi-lock me-1"></i> PASSWORD
                </label>
                <div style="position:relative;">
                    <input
                        id="password"
                        type="password"
                        name="password"
                        class="pw-auth-input @error('password') is-error @enderror"
                        placeholder="••••••••"
                        required
                        autocomplete="current-password">
                    <button type="button" onclick="togglePwd('password')"
                        style="position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;color:rgba(255,255,255,.35);cursor:pointer;font-size:15px;">
                        <i class="bi bi-eye" id="password-eye"></i>
                    </button>
                </div>
                @error('password')
                    <div class="pw-auth-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                @enderror
            </div>

            {{-- Remember + Forgot --}}
            <div class="d-flex align-items-center justify-content-between mb-3" style="font-size:13px;">
                <label class="d-flex align-items-center gap-2" style="color:rgba(255,255,255,.45);cursor:pointer;">
                    <input type="checkbox" name="remember" style="accent-color:#29c5ff;">
                    Remember me
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="pw-auth-link" style="font-size:13px;">Forgot password?</a>
                @endif
            </div>

            <button type="submit" class="pw-auth-btn">
                <i class="bi bi-box-arrow-in-right me-2"></i> Sign In
            </button>
        </form>

        <div class="pw-auth-divider"></div>

        <div class="pw-auth-link-row">
            Don't have an account?
            <a href="{{ route('register') }}" class="pw-auth-link"> Create one free</a>
        </div>

        {{-- Back to home --}}
        <div class="text-center mt-3">
            <a href="/" style="color:rgba(255,255,255,.25);font-size:12px;text-decoration:none;">
                <i class="bi bi-arrow-left me-1"></i> Back to PortWatch AI
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePwd(id) {
    const input = document.getElementById(id);
    const eye   = document.getElementById(id + '-eye');
    if (input.type === 'password') {
        input.type = 'text';
        eye.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        eye.className = 'bi bi-eye';
    }
}
</script>

<style>
.pw-auth-input.is-error { border-color: rgba(239,68,68,.5) !important; }
.pw-auth-grid {
    position: absolute;
    inset: 0;
    z-index: 2;
    background-image:
        linear-gradient(rgba(41,197,255,.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(41,197,255,.03) 1px, transparent 1px);
    background-size: 48px 48px;
    pointer-events: none;
}
</style>
</body>
</html>
