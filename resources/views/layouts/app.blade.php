<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Sistema de Gestion RemNube') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">Almacén</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav">
                    <li><a href="{{ route('clientes.index') }}" class="nav-link">Clientes</a></li>
                    <li><a href="{{ route('categorias.index') }}" class="nav-link">Categorías</a></li>
                    <li><a href="{{ route('formas_pago.index') }}" class="nav-link">Formas de pago</a></li>
                    <li><a href="{{ route('productos.index') }}" class="nav-link">Productos</a></li>
                    <li><a href="{{ route('proveedores.index') }}" class="nav-link">Proveedores</a></li>
                    <li><a href="{{ route('ventas.index') }}" class="nav-link">Ventas</a></li>
                    <li><a href="{{ route('compras.index') }}" class="nav-link">Compras</a></li>
                    <li><a href="{{ route('caja.index') }}" class="nav-link">Caja</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
    @yield('scripts')
</body>
</html>
