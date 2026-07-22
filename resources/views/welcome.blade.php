<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bienvenido</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container vh-100 d-flex flex-column justify-content-center align-items-center">
        <!-- Logo centrado -->
        <div class="mb-4">
            <img src="{{ asset('/logo.jfif') }}" alt="Logo" class="mx-auto d-block" style="width: 180px; height: 180px;">
        </div>

        <!-- Card con botones -->
        <div class="card shadow" style="max-width: 350px; width: 100%;">
            <div class="card-body text-center">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-primary w-100 mb-2">Menu de Inicio</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary w-100 mb-2">Iniciar sesión</a>
                    @endauth
                @endif
            </div>
        </div>
    </div>
</body>
</html>