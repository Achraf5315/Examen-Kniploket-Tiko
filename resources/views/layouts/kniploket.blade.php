{{-- Hoofdlayout van Kniploket Tiko: Bootstrap 5 via CDN, navigatiebalk, flash-meldingen en footer --}}
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titel', 'Kniploket Tiko')</title>

    {{-- Bootstrap 5 via CDN: standaard styling is voldoende voor dit project --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100 bg-white">

    {{-- Navigatiebalk conform de wireframe --}}
    <nav class="navbar navbar-expand-lg bg-light border-bottom">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ url('/') }}">Kniploket Tiko</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#hoofdNavigatie"
                    aria-controls="hoofdNavigatie" aria-expanded="false" aria-label="Menu tonen">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="hoofdNavigatie">
                <ul class="navbar-nav ms-auto me-lg-4">
                    {{-- Behandelingen en Producten worden door teamgenoten gebouwd --}}
                    <li class="nav-item"><a class="nav-link" href="{{ route('behandelingen.index') }}">Behandelingen</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Producten</a></li>

                    @if (Route::has('afspraken.create'))
                        <li class="nav-item"><a class="nav-link" href="{{ route('afspraken.create') }}">Afspraak maken</a></li>
                    @endif

                    @auth
                        <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a></li>
                    @endauth
                </ul>

                @guest
                    <a href="{{ route('login') }}" class="btn btn-outline-dark">Inloggen</a>
                @endguest

                @auth
                    {{-- Uitloggen moet via POST vanwege de CSRF-beveiliging --}}
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-dark">Uitloggen</button>
                    </form>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Terugkoppeling naar de eindgebruiker: succes- en foutmeldingen bovenaan iedere pagina --}}
    <div class="container mt-3">
        @if (session('succes'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('succes') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Sluiten"></button>
            </div>
        @endif

        @if (session('fout'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('fout') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Sluiten"></button>
            </div>
        @endif
    </div>

    {{-- Pagina-inhoud --}}
    <main class="flex-grow-1">
        @yield('inhoud')
    </main>

    {{-- Footer conform de wireframe van de homepagina --}}
    <footer class="bg-light border-top mt-5 py-4">
        <div class="container d-flex flex-column flex-md-row justify-content-between">
            <div>
                <strong>Kniploket Tiko</strong>
                <p class="text-secondary mb-0">Openingstijden: ma - za, 09:00 - 18:00</p>
            </div>
            <div class="text-md-end">
                <p class="text-secondary mb-0">Contact: info@kniploket-tiko.nl</p>
                <p class="text-secondary mb-0">Tel: 0123 - 45 67 89</p>
            </div>
        </div>
    </footer>

    {{-- Bootstrap JavaScript (nodig voor het sluiten van meldingen en het mobiele menu) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
