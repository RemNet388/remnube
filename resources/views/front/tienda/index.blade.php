@extends('front.layout')

@section('title', 'Tienda')

@section('content')
<div class="container py-5">

    <h1 class="fw-bold mb-4">
        Catálogo
    </h1>

    {{-- GRID PRODUCTOS --}}
    <div class="row">
        @forelse($productos as $producto)
            <div class="col-6 col-md-4 col-lg-3 mb-4">
                <div class="card h-100">

<img
    src="{{ $producto->imagen
        ? asset('storage/'.$producto->imagen)
        : asset('empresa.png') }}"
    class="card-img-top"
    style="height:220px; object-fit:{{ $producto->imagen ? 'cover' : 'contain' }};"
    alt="{{ $producto->nombre }}">


                    <div class="card-body text-center">
                        <h6 class="fw-bold">
                            {{ $producto->nombre }}
                        </h6>

                        <p class="mb-2">
                            $ {{ number_format($producto->precio_venta, 0, ',', '.') }}
                        </p>

<a href="{{ url('front/producto/'.$producto->id) }}"
   class="btn btn-sm btn-dark">
    Ver producto
</a>

                    </div>

                </div>
            </div>
        @empty
            <div class="col-12 text-center text-muted">
                No hay productos disponibles.
            </div>
        @endforelse
    </div>

    {{-- PAGINADO --}}
    <div class="mt-4">
        {{ $productos->withQueryString()->links() }}
    </div>

</div>
@endsection
