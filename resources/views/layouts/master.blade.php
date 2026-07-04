<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PortWatch AI - Global Logistics & Risk Dashboard</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body class="bg-light">

    {{-- Navbar --}}
    @include('layouts.navbar')

    <div class="container-fluid">
        <div class="row">

            {{-- Sidebar --}}
            @include('layouts.sidebar')

            {{-- Content --}}
            <main class="col-md-10 ms-sm-auto px-4 py-4">
                @yield('content')
            </main>

        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>