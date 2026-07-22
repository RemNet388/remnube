@if($productos->count())
<section class="py-5 bg-light">
    <div class="container">

        <h3 class="mb-4 text-center">{{ $titulo ?? 'Productos' }}</h3>

        <div id="sliderProductos" class="carousel slide" data-bs-ride="carousel">

            <div class="carousel-inner">

                @foreach($productos->chunk(4) as $chunkIndex => $grupo)
                    <div class="carousel-item {{ $chunkIndex == 0 ? 'active' : '' }}">
                        <div class="row">

@foreach($grupo as $producto)
    <div class="col-md-3">
        <div class="card h-100 shadow-sm">

{{-- LINK A DETALLE --}}
<a href="{{ url('/front/producto/'.$producto->id) }}">
    <img
        src="{{ $producto->imagen
            ? asset('storage/'.$producto->imagen)
            : asset('empresa.png') }}"
        class="card-img-top"
        style="height:220px; object-fit:cover;"
        alt="{{ $producto->nombre }}">
</a>

            <div class="card-body text-center">
                <h6 class="mb-1">
                    {{ Str::limit($producto->nombre, 40) }}
                </h6>

                <strong class="text-primary">
                    ${{ number_format($producto->precio_venta, 0, ',', '.') }}
                </strong>
            </div>

        </div>
    </div>
@endforeach


                        </div>
                    </div>
                @endforeach

            </div>

            <!-- CONTROLES -->
            <button class="carousel-control-prev" type="button" data-bs-target="#sliderProductos" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>

            <button class="carousel-control-next" type="button" data-bs-target="#sliderProductos" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>

        </div>
    </div>
</section>
@endif
