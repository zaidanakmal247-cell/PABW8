<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Aplikasi Tugas Akhir')</title>
    
    <!-- Link CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100 bg-light">

    {{-- Parsial Menu Navigasi --}}
    @include('partials.navbar')

    {{-- Konten Utama --}}
    <main class="container my-4">
        @yield('content')
    </main>

    {{-- Parsial Footer --}}
    @include('partials.footer')

    <!-- Script JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>