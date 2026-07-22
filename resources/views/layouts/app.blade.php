<!DOCTYPE html>
<html lang="es">
<head>
    @livewireStyles
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Sistema de Gestion RemNube') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="manifest" href="/manifest.json">
<script>
    if ("serviceWorker" in navigator) {
        navigator.serviceWorker.register("/sw.js");
    }
</script>

    @stack('styles')

    <style>
        body > .container {
            padding-bottom: 70px; /* espacio para la barra inferior */
        }

        /* -------------------- */
        /* Navbar superior */
        /* -------------------- */
        .navbar-nav .nav-link {
            font-size: 0.85rem;
            font-weight: 400;
            padding: 0.35rem 0.75rem;
            color: #f8f9fa;
            position: relative;
        }

        .navbar-nav .nav-link:hover {
            text-decoration: none; /* quitamos subrayado en hover */
        }

        .navbar-nav .nav-link.active {
            font-weight: 500;
            border-bottom: 2px solid #ffc107; /* subrayado solo en activo */
        }

        .navbar-nav .nav-item + .nav-item {
            border-left: 1px solid rgba(255, 255, 255, 0.2); /* separador fino */
        }

        /* Botón VENTA RÁPIDA cuadrado */
        .nav-link-venta-rapida {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 120px;
            height: 48px;
            background-color: #0d6efd; /* azul destacado */
            color: #fff;
            font-weight: 600;
            text-transform: uppercase; /* texto en mayúscula */
            border-radius: 4px;        /* bordes rectos */
            text-decoration: none;
            transition: background-color 0.2s;
            font-size: 0.85rem;
        }

        .nav-link-venta-rapida:hover {
            background-color: #0b5ed7; /* hover sutil */
        }
    </style>
</head>
<body>

<!-- 🔹 Navbar superior (solo visible en móvil) -->
<nav class="navbar navbar-dark bg-dark d-lg-none py-2">
    <div class="container d-flex justify-content-between align-items-center">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
            <img src="{{ asset('/logo.png') }}" alt="Logo" height="30" class="me-2">
            {{ config('app.name') }}
        </a>

        {{-- Botón hamburguesa visible solo en móviles --}}
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#menuLateral"
                aria-controls="menuLateral" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
</nav>

<!-- 🔸 Sidebar lateral (fijo en escritorio, deslizable en móvil) -->
<div class="offcanvas offcanvas-start text-bg-dark" tabindex="-1" id="menuLateral"
     aria-labelledby="menuLateralLabel"
     style="background-color: rgba(0,0,0,0.95); width: 240px; border-right: 1px solid rgba(255,255,255,0.1);">

    <div class="offcanvas-header border-bottom border-secondary d-flex justify-content-between align-items-center">
        <h5 class="offcanvas-title text-uppercase fw-bold" id="menuLateralLabel">Menú</h5>
        <button type="button" class="btn-close btn-close-white d-lg-none" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body p-0">
        <ul class="nav flex-column">

            {{-- 🟦 Venta Rápida --}}
            <li class="nav-item border-bottom border-secondary">
                <a href="{{ route('ventas.rapida') }}" class="nav-link text-white fs-6 menu-link d-flex justify-content-between align-items-center">
                    🛒 Venta Rápida
                </a>
            </li>

            {{-- Ventas --}}
            <li class="nav-item border-bottom border-secondary">
                <a href="{{ route('ventas.index') }}" class="nav-link text-white fs-6 menu-link {{ request()->routeIs('ventas.*') ? 'active' : '' }}">Ventas</a>
            </li>

            {{-- Productos --}}
            <li class="nav-item border-bottom border-secondary">
                <a href="{{ route('productos.index') }}" class="nav-link text-white fs-6 menu-link {{ request()->routeIs('productos.*') ? 'active' : '' }}">Productos</a>
            </li>

            {{-- Servicios --}}
            @if(config('custom.plan') >= 2)
            <li class="nav-item border-bottom border-secondary">
                <button class="nav-link w-100 text-start text-white fs-6 menu-link d-flex justify-content-between align-items-center"
                        data-bs-toggle="collapse" data-bs-target="#submenuServicios" aria-expanded="false">
                    Servicios
                    <span class="submenu-arrow"><i class="bi bi-chevron-down"></i></span>
                </button>
                <div class="collapse" id="submenuServicios">
                    <ul class="list-unstyled ms-3 mb-2">
                        @if(config('custom.plan') >= 3)
                            <li><a href="{{ route('turnos.index') }}" class="nav-link py-2 ps-4 text-white-50 sub-link {{ request()->routeIs('turnos.*') ? 'active' : '' }}">Turnero</a></li>
                        @endif
                        <li><a href="{{ route('ordenes.index') }}" class="nav-link py-2 ps-4 text-white-50 sub-link {{ request()->routeIs('ordenes.*') ? 'active' : '' }}">Órdenes de Servicio</a></li>
                    </ul>
                </div>
            </li>
            @endif

            {{-- Caja --}}
            @if(auth()->user()->role === 'admin')
            <li class="nav-item border-bottom border-secondary">
                <a href="{{ route('cajas.index') }}" class="nav-link text-white fs-6 menu-link {{ request()->routeIs('cajas.*') ? 'active' : '' }}">Caja Diaria</a>
            </li>
            @endif

            {{-- Clientes --}}
            <li class="nav-item border-bottom border-secondary">
                <a href="{{ route('clientes.index') }}" class="nav-link text-white fs-6 menu-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}">Clientes</a>
            </li>

            {{-- Proveedores / Compras / Informes / Formas de pago --}}
            @if(auth()->user()->role === 'admin')
                <li class="nav-item border-bottom border-secondary">
                    <a href="{{ route('proveedores.index') }}" class="nav-link text-white fs-6 menu-link {{ request()->routeIs('proveedores.*') ? 'active' : '' }}">Proveedores</a>
                </li>
                <li class="nav-item border-bottom border-secondary">
                    <a href="{{ route('compras.index') }}" class="nav-link text-white fs-6 menu-link {{ request()->routeIs('compras.*') ? 'active' : '' }}">Compras</a>
                </li>
                <li class="nav-item border-bottom border-secondary">
                    <a href="{{ route('informes.ventas') }}" class="nav-link text-white fs-6 menu-link {{ request()->routeIs('informes.*') ? 'active' : '' }}">📊 Informes</a>
                </li>
                <li class="nav-item border-bottom border-secondary">
                    <a href="{{ route('formas_pago.index') }}" class="nav-link text-white fs-6 menu-link {{ request()->routeIs('formas_pago.*') ? 'active' : '' }}">Formas de Pago</a>
                </li>
            @endif

            {{-- 👤 Usuario --}}
            <li class="nav-item border-top border-secondary mt-3">
                <button class="nav-link w-100 text-start text-white fs-6 menu-link d-flex justify-content-between align-items-center"
                        data-bs-toggle="collapse" data-bs-target="#submenuUser" aria-expanded="false">
                    {{ Auth::user()->name }}
                    <span class="submenu-arrow"><i class="bi bi-chevron-down"></i></span>
                </button>
                <div class="collapse" id="submenuUser">
                    <ul class="list-unstyled ms-3 mb-2">
                        <li><a href="{{ route('profile.edit') }}" class="nav-link py-2 ps-4 text-white-50 sub-link">Perfil</a></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="nav-link py-2 ps-4 text-danger sub-link text-start w-100" type="submit">Cerrar sesión</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </li>
            <!-- 🔻 Logo fijo abajo del menú lateral -->
<div class="text-center mt-3 mb-3">
    <a href="{{ route('dashboard') }}">
        <img src="{{ asset('empresa.png') }}" alt="Logo" style="width: 120px; opacity: 0.85;">
    </a>
</div>

        </ul>
    </div>
</div>

<!-- 💅 CSS -->
<style>
@media (min-width: 992px) {
    /* Sidebar fijo solo en pantallas grandes */
    body {
        padding-left: 240px; /* espacio para el sidebar */
    }
    #menuLateral {
        position: fixed;
        top: 0;
        left: 0;
        transform: none !important;
        visibility: visible !important;
        height: 100vh;
        background-color: rgba(0,0,0,0.95);
        border-right: 1px solid rgba(255,255,255,0.1);
        z-index: 1030;
    }
    .offcanvas-backdrop {
        display: none !important;
    }
}

/* Links */
.menu-link {
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: background-color 0.2s ease, color 0.2s ease;
}
.menu-link:hover {
    background-color: rgba(255,255,255,0.1);
}
.sub-link:hover {
    color: #fff !important;
}

/* Flecha submenu */
.submenu-arrow {
    background-color: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.15);
    padding: 4px 6px;
    border-radius: 4px;
    font-size: 0.8rem;
    transition: transform 0.3s ease;
}
button[aria-expanded="true"] .submenu-arrow {
    transform: rotate(180deg);
}
</style>


<style>
/* -------------------- */
/* Submenu inferior */
/* -------------------- */
#barra-submenu {
    background-color: #212529; /* igual que navbar-dark bg-dark */
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    padding: 0.5rem 1rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    overflow-x: auto;
    white-space: nowrap;
}

/* Links estilo submenu (fondo claro, texto oscuro) */
#barra-submenu a,
#barra-submenu button,
#barra-submenu .submenu-link {
    font-size: 0.85rem;
    font-weight: 500;
    color: #212529;             /* texto oscuro */
    background: transparent;    /* sin fondo */
    border: none;               /* sin borde */
    padding: 0.35rem 0.75rem;
    border-radius: 4px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    transition: background-color 0.2s;
}

#barra-submenu a:hover,
#barra-submenu button:hover,
#barra-submenu .submenu-link:hover {
    background-color: rgba(0, 0, 0, 0.05); /* gris muy suave al hover */
    text-decoration: none;
    color: #000;
}

#barra-submenu a.active {
    border-bottom: 2px solid #0d6efd; /* azul como el navbar */
    font-weight: 600;
}

/* Input de búsqueda */
#barra-submenu input[type="text"] {
    font-size: 0.85rem;
    border-radius: 4px;
    border: 1px solid #ced4da;
    padding: 0.25rem 0.5rem;
}

/* Botón buscar como link */
#barra-submenu .btn-search {
    background: transparent;
    border: none;
    color: #212529;
    font-size: 0.9rem;
    padding: 0.25rem 0.5rem;
    transition: background-color 0.2s;
}

#barra-submenu .btn-search:hover {
    background-color: rgba(0, 0, 0, 0.05);
}


</style>


    <!-- Layout en card -->
    <div class="container mb-5">
        <div class="card shadow-sm">
            <div class="card-body">
                @yield('content')
                {{ $slot ?? '' }}
            </div>
        </div>
    </div>

    <!-- Submenu inferior (sin cambios) -->
<div id="barra-submenu" 
     class="bg-light border-top shadow-sm" 
     style="position: fixed; bottom: 0; left: 0; width: 100%; z-index: 1030;">
    <div class="container d-flex align-items-center p-2" style="overflow-x: auto; white-space: nowrap; gap: 12px;">
        
        <!-- Logo -->
        <div class="d-flex align-items-center justify-content-center me-3" 
             style="background-color: #343a40; padding: 4px 8px; border-radius: 4px; flex-shrink: 0;">
            <img src="{{ asset('logo.png') }}" alt="Logo" style="height: 32px;">
        </div>
        
        <!-- Submenu dinámico -->
        <div class="d-flex align-items-center" style="gap: 8px; flex-shrink: 0;">
            @stack('submenu')
        </div>
    </div>
</div>

    @stack('scripts')
    @yield('scripts')
    @livewireScripts
</body>
</html>
