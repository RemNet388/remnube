@extends('front.layout')

@section('title', 'Tu Tienda Online')

@section('content')

<!-- HERO -->
<section class="hero-home text-white d-flex align-items-center">
    <div class="overlay"></div>
    <div class="container position-relative">
        <div class="row align-items-center">
            
            {{-- TEXTO PRINCIPAL --}}
            <div class="col-md-6 mb-4 mb-md-0">
                <h1 class="fw-bold display-4">
                    Oversize que habla por vos
                </h1>

                <p class="lead mt-3">
                    Prendas cómodas, urbanas y con identidad propia. Diseños pensados para destacar sin esfuerzo, con estilo auténtico para todos los días.
                </p>

                <a href="{{ url('/tienda') }}" class="btn btn-light btn-lg mt-3">
                    Descubrí el catálogo
                </a>
            </div>

            {{-- IMAGEN SECUNDARIA (modelo / producto) --}}
            <div class="col-md-6 d-none d-md-block text-center">
                <img 
                    src="{{ asset('images/modelo-oversize.jpg') }}" 
                    class="img-fluid rounded shadow"
                    alt="Modelo oversize"
                >
            </div>

        </div>
    </div>
</section>

<!-- BENEFICIOS -->
<section class="py-5">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-4">
                <h5 class="fw-bold">Envíos a todo el país</h5>
                <p class="text-muted">
                    Recibí tus productos donde estés.
                </p>
            </div>

            <div class="col-md-4">
                <h5 class="fw-bold">Pagos seguros</h5>
                <p class="text-muted">
                    Transferencias, tarjetas y más.
                </p>
            </div>

            <div class="col-md-4">
                <h5 class="fw-bold">Atención personalizada</h5>
                <p class="text-muted">
                    Estamos para ayudarte.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- DESTACADOS -->
@include('front.partials.slider-productos', [
    'productos' => $destacados,
    'titulo' => 'Productos destacados'
])


<!-- CTA -->
<section class="py-5 text-center">
    <div class="container">
        <h2 class="fw-bold">
            Empezá a comprar hoy
        </h2>

        <p class="text-muted">
            Explorá nuestro catálogo completo.
        </p>

        <a href="/tienda" class="btn btn-dark btn-lg">
            Ir a la tienda
        </a>
    </div>
</section>

@endsection
