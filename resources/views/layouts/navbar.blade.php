<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container-fluid">

        <a class="navbar-brand fw-bold" href="#">
            🌍 PortWatch AI
        </a>

        <div class="ms-auto">

            <span class="text-white me-3">
                <i class="bi bi-person-circle"></i>
                {{ Auth::user()->name }}
            </span>

            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button class="btn btn-light btn-sm">
                    Logout
                </button>
            </form>

        </div>

    </div>
</nav>