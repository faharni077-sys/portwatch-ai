<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PortWatch AI') — Global Supply Chain Intelligence</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

    @vite(['resources/css/app.css'])

    @yield('head')
</head>
<body>

<div class="pw-layout">
    <!-- Sidebar -->
    <aside class="pw-sidebar" id="pwSidebar">
        @include('layouts.sidebar')
    </aside>

    <!-- Main Area -->
    <div class="pw-main-wrap">
        <!-- Topbar -->
        <header class="pw-topbar">
            <div class="pw-topbar-left">
                <button class="pw-sidebar-toggle" id="sidebarToggle">
                    <i class="bi bi-layout-sidebar-inset"></i>
                </button>
                <div class="pw-topbar-title">
                    <span class="pw-breadcrumb">@yield('breadcrumb', 'DASHBOARD')</span>
                    <span class="pw-topbar-sub">PortWatch AI — Supply Chain Intelligence</span>
                </div>
            </div>
            <div class="pw-topbar-right">
                <div class="pw-status-dot">
                    <span class="dot-pulse"></span>
                    <span class="pw-status-text">LIVE</span>
                </div>
                <div class="pw-topbar-time" id="topbarClock"></div>
                <div class="pw-topbar-user dropdown">
                    <button class="pw-user-btn dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i>
                        <span>{{ Auth::user()->name ?? 'USER' }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end pw-dropdown">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="pw-content">
            @yield('content')
        </main>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
// Live clock
function updateClock() {
    const now = new Date();
    const el = document.getElementById('topbarClock');
    if (el) {
        el.textContent = now.toUTCString().replace('GMT', 'UTC');
    }
}
setInterval(updateClock, 1000);
updateClock();

// Sidebar toggle
document.getElementById('sidebarToggle')?.addEventListener('click', function() {
    document.getElementById('pwSidebar').classList.toggle('collapsed');
    document.querySelector('.pw-main-wrap').classList.toggle('expanded');
});
</script>

@yield('scripts')
</body>
</html>
