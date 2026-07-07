<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>PortWatch AI</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css" rel="stylesheet">

    @vite(['resources/css/app.css'])

</head>

<body>

<section class="hero-section">

<img src="{{ asset('images/hero-globe.jpg') }}"
         class="hero-globe"
         alt="Globe">

<nav class="navbar navbar-dark px-5 py-3">

    <a class="navbar-brand fw-bold fs-3">
        🌍 PortWatch AI
    </a>

    <div>

        <a href="{{ route('login') }}" class="btn btn-outline-light me-2">
            Login
        </a>

        <a href="{{ route('register') }}" class="btn btn-primary">
            Register
        </a>

    </div>

</nav>

<div class="container">

    <div class="row align-items-center" style="min-height:85vh;">

        <div class="col-lg-6">

            <h1 class="display-2 fw-bold text-white">
                PortWatch AI
            </h1>

            <h3 class="text-info">
                Global Supply Chain Monitoring
            </h3>

            <p class="text-light fs-4 mt-4">

                Monitor shipments, weather, currency,
                ports, world news and logistics risk
                in realtime using Artificial Intelligence.

            </p>

            <a href="{{ route('login') }}" class="btn btn-primary btn-lg mt-3 me-3">
                Get Started
            </a>

            <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg mt-3">
                Register
            </a>

        </div>

        <div class="col-lg-6 position-relative">

    

</div>

    </div>

</div>

</section>
</body>
</html>