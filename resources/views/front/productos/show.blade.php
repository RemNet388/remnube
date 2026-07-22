@extends('front.layout')

@section('title', $producto->nombre)

@section('content')
<div class="container py-5">

    <div class="row">

{{-- IMAGEN --}}
<div class="col-md-6 mb-4">
    <img
        src="{{ $producto->imagen
            ? asset('storage/'.$producto->imagen)
            : asset('empresa.png') }}"
        class="img-fluid rounded shadow"
        style="max-height: 500px; object-fit: {{ $producto->imagen ? 'cover' : 'contain' }};"
        alt="{{ $producto->nombre }}">
</div>


 {{-- INFO --}}
<div class="col-md-6">

    {{-- Nombre del producto --}}
    <h1 class="fw-bold mb-3">
        {{ $producto->nombre }}
    </h1>

    {{-- Precio --}}
    <h3 class="mb-4 text-primary">
        $ {{ number_format($producto->precio_venta, 0, ',', '.') }}
    </h3>

    {{-- Categoría --}}
    @if($producto->categoria)
        <p class="text-muted mb-3">
            Categoría: {{ $producto->categoria->nombre }}
        </p>
    @endif

    {{-- Descripción --}}
    @if($producto->descripcion)
        <h5 class="mt-4 mb-2">Descripción</h5>
        <p class="text-secondary">
            {{ $producto->descripcion }}
        </p>
    @else
        <p class="text-secondary mt-3">
            Producto de nuestra colección.
        </p>
    @endif

    {{-- Botón WhatsApp --}}
@php
    $link = request()->fullUrl();
    $texto = urlencode(
        "Hola! 👋\n".
        "Me interesa este producto:\n\n".
        "{$producto->nombre}\n".
        "$ ".number_format($producto->precio_venta, 0, ',', '.')."\n\n".
        "Ver producto 👉 ".$link
    );
@endphp

<div class="mt-4">
    <a href="https://wa.me/5493513635577?text={{ $texto }}"
       target="_blank"
       class="btn btn-success btn-lg w-100">
        📲 Consultar por WhatsApp
    </a>
</div>

@php
    $link = request()->fullUrl();
    $textoCompartir = urlencode(
        "Mirá este producto 👀\n\n".
        "{$producto->nombre}\n".
        "$ ".number_format($producto->precio_venta, 0, ',', '.')."\n\n".
        "👉 ".$link
    );
@endphp

<div class="mt-3">
    <a href="https://wa.me/?text={{ $textoCompartir }}"
       target="_blank"
       class="btn btn-outline-dark btn-lg w-100">
        📤 Compartir producto
    </a>
</div>

</div>
</div>
@endsection
