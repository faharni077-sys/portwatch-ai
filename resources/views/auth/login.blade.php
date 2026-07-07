@extends('layouts.guest')

@section('content')

<div class="auth-page">

    <img src="{{ asset('images/hero-globe.jpg') }}" class="auth-globe">

    <div class="login-card">

        <div class="text-center mb-4">

            <h2 class="text-white fw-bold">
                🌍 PortWatch AI
            </h2>

            <p class="text-light">
                Global Logistics & Supply Chain Monitoring
            </p>

        </div>

        <form method="POST" action="{{ route('login') }}">

            @csrf

            <div class="mb-3">

                <label class="form-label">
                    Email
                </label>

                <input
                    type="email"
                    name="email"
                    class="form-control"
                    required
                    autofocus>

            </div>

            <div class="mb-4">

                <label class="form-label">
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    class="form-control"
                    required>

            </div>

            <button type="submit" class="btn btn-login">
                Login
            </button>

            <div class="text-center mt-4">

                <span class="text-light">
                    Belum punya akun?
                </span>

                <a href="{{ route('register') }}"
                   class="auth-link fw-bold text-decoration-none">
                    Register
                </a>

            </div>

        </form>

    </div>

</div>

@endsection