@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4 text-uppercase" style="font-family: 'Montserrat', 'Segoe UI', 'Helvetica Neue', sans-serif; font-weight: 600;">
    📊 INFORMES
    </h3>
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">Ventas por Mes</div>
                <div class="card-body">
                    <div id="chartMes" style="height: 400px;"></div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">Ventas por Categoría</div>
                <div class="card-body">
                    <div id="chartCategoria" style="height: 400px;"></div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">Productos más Vendidos</div>
                <div class="card-body">
                    <div id="chartProductos" style="height: 400px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@include('informes.scripts.scripts_amcharts')
@endsection
@push('submenu')
<a href="{{ route('informes.ventas') }}" class="btn btn-primary btn-sm rounded-0 fw-semibold small">
    <i class="bi bi-cash-stack"></i> Ventas
</a>
<span style="display: inline-block; width: 10px;"></span>

<a href="{{ route('informes.ganancias') }}" class="btn btn-primary btn-sm rounded-0 fw-semibold small">
    <i class="bi bi-graph-up"></i> Ganancias
</a>
<span style="display: inline-block; width: 10px;"></span>

<a href="{{ route('informes.movimientos_stock') }}" class="btn btn-primary btn-sm rounded-0 fw-semibold small">
    <i class="bi bi-arrow-left-right"></i> Movimientos Stock
</a>
<span style="display: inline-block; width: 10px;"></span>

<a href="{{ route('informes.stock') }}" class="btn btn-primary btn-sm rounded-0 fw-semibold small">
    <i class="bi bi-box"></i> Stock
</a>
<span style="display: inline-block; width: 10px;"></span>

<a href="{{ route('productos.por-vencer') }}" class="btn btn-danger btn-sm rounded-0 fw-semibold small">
    <i class="bi bi-exclamation-triangle"></i> Productos por vencer
</a>
<a href="{{ route('informes.productos_a_comprar') }}"  class="btn btn-danger btn-sm rounded-0 fw-semibold small">
        <i class="bi bi-box-seam"></i> Productos a Comprar
</a>
<a href="{{ route('informes.ventas_por_vendedor') }}" class="btn btn-danger btn-sm rounded-0 fw-semibold small">
    <i class="bi bi-person-badge"></i> Ventas por Vendedor
</a>
@endpush
