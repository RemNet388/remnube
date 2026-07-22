@extends('front.layout')

@section('title', $seccion->titulo)

@section('content')

{{-- Banner si existe imagen --}}
@php
    $imagePath = public_path("images/{$seccion->slug}.jpg");
@endphp

@if(file_exists($imagePath))
    <div
        style="
            background-image: url('{{ asset("images/{$seccion->slug}.jpg") }}');
            background-size: cover;
            background-position: top center;
            height: 300px;
            width: 100vw;
            margin-left: calc(50% - 50vw);
        "
    ></div>
@endif

<div class="container py-5">

    <h1 class="fw-bold mb-4">{{ $seccion->titulo }}</h1>

    <div class="text-muted mb-5">
       {!! nl2br(e($seccion->contenido)) !!}
    </div>

    @if($seccion->slug === 'como_llegar')

    <div class="row g-4 mt-4">

        {{-- Dirección --}}
        <div class="col-md-4">
            <div class="border rounded p-4 h-100 shadow-sm">
                <div class="d-flex align-items-start gap-3">
                    <i class="bi bi-geo-alt-fill fs-3 text-dark"></i>
                    <div>
                        <h6 class="fw-bold mb-1">Dirección</h6>
                        <p class="mb-0 text-muted">
                            Emilio Pettoruti 2211<br>
                            Cerro de las Rosas, local C, Cordoba
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Teléfono --}}
        <div class="col-md-4">
            <div class="border rounded p-4 h-100 shadow-sm">
                <div class="d-flex align-items-start gap-3">
                    <i class="bi bi-telephone-fill fs-3 text-dark"></i>
                    <div>
                        <h6 class="fw-bold mb-1">Teléfono</h6>
                        <p class="mb-0 text-muted">
                            <a href="tel:+5491123456789" class="text-decoration-none text-muted">
                                +54 9 351-3635577
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Horarios --}}
        <div class="col-md-4">
            <div class="border rounded p-4 h-100 shadow-sm">
                <div class="d-flex align-items-start gap-3">
                    <i class="bi bi-clock-fill fs-3 text-dark"></i>
                    <div>
                        <h6 class="fw-bold mb-1">Horarios</h6>
                        <p class="mb-0 text-muted">
                            Lun a Vie: 10 a 19 hs<br>
                            Sábados: 10 a 14 hs
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Info extra --}}
    <div class="mt-4">
        <div class="border rounded p-4 shadow-sm">
            <div class="d-flex align-items-start gap-3">
                <i class="bi bi-info-circle-fill fs-4 text-dark"></i>
                <div>
                    <h6 class="fw-bold mb-1">Información adicional</h6>
                    <p class="mb-0 text-muted">
                        Local a la calle · Acceso fácil · Zona comercial<br>
                        Aceptamos efectivo, tarjetas y transferencias.
                    </p>
                </div>
            </div>
        </div>
    </div>

@endif

    {{-- 📍 Mapa solo para "como_llegar" --}}
    @if($seccion->slug === 'como_llegar')
        <div class="ratio ratio-16x9 rounded shadow">
<iframe
    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3405.487743114328!2d-64.2287758!3d-31.3726227!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x9432994b5d4c8b7b%3A0x9f94f9d9c7a7a0b6!2sEmilio%20Pettoruti%202211!5e0!3m2!1ses!2sar!4v1700000000000"
    width="100%"
    height="450"
    style="border:0;"
    allowfullscreen
    loading="lazy"
    referrerpolicy="no-referrer-when-downgrade">
</iframe>

        </div>
    @endif

@if($seccion->slug === 'contacto')
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>

    <script>
        window.open("{{ session('whatsapp_url') }}", "_blank");
    </script>
@endif
<div class="row mt-5">
    <div class="col-md-8 mx-auto">

        <div class="border rounded p-4 shadow-sm">
            <h4 class="fw-bold mb-3">Escribinos</h4>
            <p class="text-muted mb-4">
                Completá el formulario y nos pondremos en contacto a la brevedad.
            </p>

            <form method="POST" action="{{ url('contacto/enviar') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input
                        type="text"
                        name="nombre"
                        class="form-control"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Teléfono</label>
                    <input
                        type="text"
                        name="telefono"
                        class="form-control"
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Mensaje</label>
                    <textarea
                        name="mensaje"
                        rows="4"
                        class="form-control"
                        required
                    ></textarea>
                </div>

                <button class="btn btn-dark px-4">
                    Enviar mensaje
                </button>
            </form>
        </div>

    </div>
</div>

@endif

</div>

@endsection
