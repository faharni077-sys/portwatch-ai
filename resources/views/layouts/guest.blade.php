{{-- Guest layout: login/register pages are standalone HTML, this layout is a fallback --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PortWatch AI</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @yield('content')
</body>
</html>
