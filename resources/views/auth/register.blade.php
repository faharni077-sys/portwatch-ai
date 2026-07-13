<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — PortWatch AI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body>
<div class="pw-auth-page">
    <div class="pw-auth-bg"></div>
    <div class="pw-auth-overlay"></div>
    <div class="pw-auth-grid" aria-hidden="true"></div>

    <div class="pw-auth-card">

        <div class="pw-auth-logo">
            <div class="pw-auth-logo-icon"><i class="bi bi-globe-americas"></i></div>
        </div>

        <h1 class="pw-auth-title">Create Account</h1>
        <p class="pw-auth-sub">Join the global supply chain intelligence network</p>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            {{-- Name --}}
            <div class="pw-auth-input-group">
                <label class="pw-auth-label" for="name">
                    <i class="bi bi-person me-1"></i> FULL NAME
                </label>
                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    class="pw-auth-input @error('name') is-error @enderror"
                    placeholder="Your full name"
                    required
                    autofocus
                    autocomplete="name">
                @error('name')
                    <div class="pw-auth-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                @enderror
            </div>

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
                        placeholder="Min. 8 characters"
                        required
                        autocomplete="new-password">
                    <button type="button" onclick="togglePwd('password')"
                        style="position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;color:rgba(255,255,255,.35);cursor:pointer;font-size:15px;">
                        <i class="bi bi-eye" id="password-eye"></i>
                    </button>
                </div>
                @error('password')
                    <div class="pw-auth-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                @enderror
            </div>

            {{-- Confirm Password --}}
            <div class="pw-auth-input-group">
                <label class="pw-auth-label" for="password_confirmation">
                    <i class="bi bi-lock-fill me-1"></i> CONFIRM PASSWORD
                </label>
                <div style="position:relative;">
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        class="pw-auth-input"
                        placeholder="Repeat password"
                        required
                        autocomplete="new-password">
                    <button type="button" onclick="togglePwd('password_confirmation')"
                        style="position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;color:rgba(255,255,255,.35);cursor:pointer;font-size:15px;">
                        <i class="bi bi-eye" id="password_confirmation-eye"></i>
                    </button>
                </div>
            </div>

            {{-- Strength hint --}}
            <div id="strengthBar" style="display:none;margin-bottom:14px;">
                <div style="height:3px;background:rgba(255,255,255,.08);border-radius:2px;">
                    <div id="strengthFill" style="height:100%;border-radius:2px;transition:.3s;width:0;"></div>
                </div>
                <div id="strengthLabel" style="font-size:11px;margin-top:4px;font-family:'JetBrains Mono',monospace;"></div>
            </div>

            <button type="submit" class="pw-auth-btn">
                <i class="bi bi-person-plus-fill me-2"></i> Create Account
            </button>
        </form>

        <div class="pw-auth-divider"></div>

        <div class="pw-auth-link-row">
            Already have an account?
            <a href="{{ route('login') }}" class="pw-auth-link"> Sign in here</a>
        </div>

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

// Password strength
document.getElementById('password').addEventListener('input', function () {
    const val = this.value;
    const bar  = document.getElementById('strengthBar');
    const fill = document.getElementById('strengthFill');
    const lbl  = document.getElementById('strengthLabel');
    if (!val) { bar.style.display = 'none'; return; }
    bar.style.display = 'block';
    let score = 0;
    if (val.length >= 8)  score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    const levels = [
        { w: '25%', c: '#ef4444', t: 'WEAK' },
        { w: '50%', c: '#f59e0b', t: 'FAIR' },
        { w: '75%', c: '#29c5ff', t: 'GOOD' },
        { w: '100%', c: '#22c55e', t: 'STRONG' },
    ];
    const lvl = levels[Math.max(0, score - 1)];
    fill.style.width = lvl.w;
    fill.style.background = lvl.c;
    lbl.style.color = lvl.c;
    lbl.textContent = 'PASSWORD STRENGTH: ' + lvl.t;
});
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
