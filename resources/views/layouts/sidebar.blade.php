{{-- PortWatch AI - Futuristic HUD Sidebar --}}
<div class="pw-sidebar-inner">

    {{-- Logo --}}
    <div class="pw-logo">
        <div class="pw-logo-icon">
            <i class="bi bi-globe-americas"></i>
        </div>
        <div class="pw-logo-text">
            <span class="pw-logo-name">PORTWATCH</span>
            <span class="pw-logo-sub">AI · INTELLIGENCE</span>
        </div>
    </div>

    {{-- Sector Badge --}}
    <div class="pw-sector-badge">
        <span class="dot-pulse"></span>
        <span>SYSTEM ACTIVE</span>
    </div>

    {{-- Navigation --}}
    <nav class="pw-nav">
        <div class="pw-nav-label">MAIN MODULES</div>

        <a href="{{ route('dashboard') }}"
           class="pw-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
            @if(request()->routeIs('dashboard'))
                <span class="pw-nav-dot"></span>
            @endif
        </a>

        <a href="{{ route('countries.index') }}"
           class="pw-nav-item {{ request()->routeIs('countries.*') ? 'active' : '' }}">
            <i class="bi bi-globe2"></i>
            <span>Countries</span>
        </a>

        <a href="{{ route('weather') }}"
           class="pw-nav-item {{ request()->routeIs('weather') ? 'active' : '' }}">
            <i class="bi bi-cloud-lightning-rain"></i>
            <span>Weather</span>
        </a>

        <a href="{{ route('currency') }}"
           class="pw-nav-item {{ request()->routeIs('currency') ? 'active' : '' }}">
            <i class="bi bi-currency-exchange"></i>
            <span>Currency</span>
        </a>

        <a href="{{ route('news') }}"
           class="pw-nav-item {{ request()->routeIs('news') ? 'active' : '' }}">
            <i class="bi bi-newspaper"></i>
            <span>News Intel</span>
        </a>

        <a href="{{ route('ports.index') }}"
           class="pw-nav-item {{ request()->routeIs('ports.*') ? 'active' : '' }}">
            <i class="bi bi-anchor"></i>
            <span>Ports</span>
        </a>

        <div class="pw-nav-divider"></div>
        <div class="pw-nav-label">ANALYTICS</div>

        <a href="{{ route('analytics') }}"
           class="pw-nav-item {{ request()->routeIs('analytics') ? 'active' : '' }}">
            <i class="bi bi-graph-up-arrow"></i>
            <span>Analytics</span>
        </a>

        <a href="{{ route('compare') }}"
           class="pw-nav-item {{ request()->routeIs('compare') ? 'active' : '' }}">
            <i class="bi bi-intersect"></i>
            <span>Compare</span>
        </a>

        <a href="{{ route('watchlist') }}"
           class="pw-nav-item {{ request()->routeIs('watchlist') ? 'active' : '' }}">
            <i class="bi bi-bookmark-star"></i>
            <span>Watchlist</span>
        </a>

        <div class="pw-nav-divider"></div>
        <div class="pw-nav-label">ACCOUNT</div>

        <a href="{{ route('profile.edit') }}"
           class="pw-nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <i class="bi bi-person-badge"></i>
            <span>Profile</span>
        </a>

        <form method="POST" action="{{ route('logout') }}" class="d-block">
            @csrf
            <button type="submit" class="pw-nav-item pw-nav-logout w-100">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </button>
        </form>
    </nav>

    {{-- System Status --}}
    <div class="pw-sidebar-footer">
        <div class="pw-sys-row">
            <span class="pw-sys-label">NODE</span>
            <span class="pw-sys-val text-success">ONLINE</span>
        </div>
        <div class="pw-sys-row">
            <span class="pw-sys-label">API SYNC</span>
            <span class="pw-sys-val text-info">ACTIVE</span>
        </div>
        <div class="pw-sys-row">
            <span class="pw-sys-label">SECTOR</span>
            <span class="pw-sys-val">GLOBAL</span>
        </div>
    </div>
</div>
