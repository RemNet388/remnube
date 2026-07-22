<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'BAF · Oversize Clothes')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Open Graph -->
<meta property="og:type" content="website">
<meta property="og:title" content="BAF · Oversize Clothes">
<meta property="og:description" content="Oversize que habla por vos. Prendas urbanas con identidad propia.">
<meta property="og:image" content="{{ asset('images/og-empresa.jpg') }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:site_name" content="BAF">

<!-- WhatsApp / Facebook -->
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">

    {{-- BOOTSTRAP --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- CSS FRONT --}}
    <style>
        /* NAV CATEGORÍAS */
        .categorias-nav {
            flex-wrap: nowrap;
            overflow-x: auto;
            white-space: nowrap;
            scrollbar-width: none;
        }
        .categorias-nav::-webkit-scrollbar {
            display: none;
        }
        .categoria-link {
            font-size: 0.7rem;
            text-transform: uppercase;
            padding: 0.4rem 0.6rem;
            color: #333 !important;
            letter-spacing: .05em;
        }
        .categoria-link:hover {
            color: #000 !important;
            text-decoration: underline;
        }

        /* SLIDER */
        .slider-productos {
            white-space: nowrap;
            scrollbar-width: none;
            padding-bottom: 5px;
        }
        .slider-productos::-webkit-scrollbar {
            display: none;
        }
        .slider-card {
            min-width: 220px;
            max-width: 220px;
            border: none;
        }
        .slider-card img {
            height: 180px;
            object-fit: cover;
        }

        /* HERO HOME */
        .hero-home {
            min-height: 70vh;
            background-image: url("{{ asset('images/ropa-oversize.jpg') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
        }
        .hero-home .overlay {
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,.55);
        }
    </style>
</head>

<body>

{{-- NAVBAR --}}
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
    <div class="container">

        {{-- LOGO --}}
        <a class="navbar-brand" href="/">
            <img src="{{ asset('empresa.png') }}"
                 style="width:120px; opacity:.85">
        </a>

        {{-- TOGGLER --}}
        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarFront">
            <span class="navbar-toggler-icon"></span>
        </button>

    {{-- MENU --}}
<div class="collapse navbar-collapse" id="navbarFront">

    @php
        $catHombre = $categorias->filter(fn ($c) =>
            str_contains(strtolower($c->nombre), 'hombre')
        );

        $catMujer = $categorias->filter(fn ($c) =>
            str_contains(strtolower($c->nombre), 'mujer')
        );

        $catOtras = $categorias->reject(fn ($c) =>
            str_contains(strtolower($c->nombre), 'hombre') ||
            str_contains(strtolower($c->nombre), 'mujer')
        );
    @endphp

    <ul class="navbar-nav ms-auto align-items-lg-center">

        {{-- HOMBRE --}}
        @if($catHombre->count())
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle categoria-link"
                   href="#"
                   data-bs-toggle="dropdown">
                    Hombre
                </a>
                <ul class="dropdown-menu">
                    @foreach($catHombre as $cat)
                        <li>
                            <a class="dropdown-item"
                               href="{{ url('tienda?categoria=' . $cat->id) }}">
                                {{ ucwords(strtolower($cat->nombre)) }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
        @endif

        {{-- MUJER --}}
        @if($catMujer->count())
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle categoria-link"
                   href="#"
                   data-bs-toggle="dropdown">
                    Mujer
                </a>
                <ul class="dropdown-menu">
                    @foreach($catMujer as $cat)
                        <li>
                            <a class="dropdown-item"
                               href="{{ url('tienda?categoria=' . $cat->id) }}">
                                {{ ucwords(strtolower($cat->nombre)) }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
        @endif

        {{-- OTRAS CATEGORÍAS --}}
        @if($catOtras->count() <= 5)

            {{-- Mostrar como links --}}
            @foreach($catOtras as $cat)
                <li class="nav-item">
                    <a class="nav-link categoria-link"
                       href="{{ url('tienda?categoria=' . $cat->id) }}">
                        {{ ucwords(strtolower($cat->nombre)) }}
                    </a>
                </li>
            @endforeach

        @else

            {{-- Agrupar en dropdown --}}
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle categoria-link"
                   href="#"
                   data-bs-toggle="dropdown">
                    Más
                </a>
                <ul class="dropdown-menu">
                    @foreach($catOtras as $cat)
                        <li>
                            <a class="dropdown-item"
                               href="{{ url('tienda?categoria=' . $cat->id) }}">
                                {{ ucwords(strtolower($cat->nombre)) }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>

        @endif

        {{-- SEPARADOR --}}
        <li class="nav-item d-none d-lg-block mx-2 text-muted">|</li>

        {{-- SECCIONES CMS --}}
        @php
            $secciones = \App\Models\Seccion::where('activo', 1)
                ->orderBy('orden')
                ->get();
        @endphp

        @foreach($secciones as $sec)
            <li class="nav-item">
                <a class="nav-link categoria-link"
                   href="{{ url('front/seccion/' . $sec->slug) }}">
                    {{ $sec->titulo }}
                </a>
            </li>
        @endforeach

    </ul>

</div>

    </div>
</nav>

{{-- CONTENIDO --}}
@yield('content')

{{-- FOOTER --}}
<footer class="bg-dark text-white py-4 mt-5">
    <div class="container text-center">
        <small>
            {!! $footer->contenido ?? "© ".date('Y')." RemNube · Tu negocio, siempre en la nube — Todos los derechos reservados" !!}
        </small>
    </div>
</footer>

{{-- JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
