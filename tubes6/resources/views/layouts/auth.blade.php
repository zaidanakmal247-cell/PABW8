<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - ParkTrack</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="d-flex align-items-center min-vh-100 bg-light">
    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <a class="d-block text-center text-decoration-none text-primary fw-bold fs-3 mb-4" href="{{ route('login') }}">ParkTrack</a>
                @yield('content')
            </div>
        </div>
    </main>
</body>
</html>
