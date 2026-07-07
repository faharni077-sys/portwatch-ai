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
                Create Your Account
            </p>

        </div>

        <form method="POST" action="{{ route('register') }}">

            @csrf

            <div class="mb-3">

                <label class="form-label">
                    Nama
                </label>

                <input
                    type="text"
                    name="name"
                    class="form-control"
                    required>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Email
                </label>

                <input
                    type="email"
                    name="email"
                    class="form-control"
                    required>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    class="form-control"
                    required>

            </div>

            <div class="mb-4">

                <label class="form-label">
                    Konfirmasi Password
                </label>

                <input
                    type="password"
                    name="password_confirmation"
                    class="form-control"
                    required>

            </div>

            <button type="submit" class="btn btn-login">
                Register
            </button>

            <div class="text-center mt-4">

                <span class="text-light">
                    Sudah punya akun?
                </span>

                <a href="{{ route('login') }}"
                   class="auth-link fw-bold text-decoration-none">
                    Login
                </a>

            </div>

        </form>

    </div>

</div>

@endsection