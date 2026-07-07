<div class="col-md-2 sidebar p-3">

    <div class="logo text-center py-4">

        <h3 class="fw-bold text-white">
            🌍 PortWatch AI
        </h3>

        <small class="text-info">
            Global Logistics
        </small>

    </div>

    <ul class="nav flex-column mt-4">

        <li class="nav-item">
            <a href="{{ route('dashboard') }}"
               class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                Dashboard
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('country') }}"
               class="nav-link {{ request()->routeIs('country') ? 'active' : '' }}">
                <i class="bi bi-globe2"></i>
                Country
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('weather') }}"
               class="nav-link">
                <i class="bi bi-cloud-sun"></i>
                Weather
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('currency') }}"
               class="nav-link">
                <i class="bi bi-currency-exchange"></i>
                Currency
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('news') }}"
               class="nav-link">
                <i class="bi bi-newspaper"></i>
                News
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('ports') }}"
               class="nav-link">
                <i class="bi bi-truck"></i>
                Ports
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('analytics') }}"
               class="nav-link">
                <i class="bi bi-graph-up-arrow"></i>
                Analytics
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('compare') }}"
               class="nav-link">
                <i class="bi bi-bar-chart"></i>
                Compare
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('watchlist') }}"
               class="nav-link">
                <i class="bi bi-star-fill"></i>
                Watchlist
            </a>
        </li>

    </ul>

</div>