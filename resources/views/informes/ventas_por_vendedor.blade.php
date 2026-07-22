@extends('layouts.app')

@section('content')
<div class="mb-3">
    <h2 class="text-uppercase fw-bold" style="font-family: 'Roboto', sans-serif; font-size: 16px;">Ventas por Vendedor</h2>
</div>

<!-- Filtro por fechas -->
<form action="{{ route('informes.ventas_por_vendedor') }}" method="GET" class="row g-2 mb-3">
    <div class="col-auto">
        <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="form-control form-control-sm">
    </div>
    <div class="col-auto">
        <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" class="form-control form-control-sm">
    </div>
    <div class="col-auto">
        <button class="btn btn-primary btn-sm">Filtrar</button>
    </div>
</form>

<table class="table table-sm table-bordered table-striped" style="font-size: 13px;">
    <thead class="table-light">
        <tr>
            <th>Vendedor</th>
            <th>Cantidad de Ventas</th>
            <th>Total Vendido</th>
            <th>Promedio por Venta</th>
        </tr>
    </thead>
    <tbody>
        @forelse($ventas as $venta)
            <tr>
                <td>{{ $venta->usuario->name ?? '-' }}</td>
                <td>{{ $venta->total_ventas }}</td>
                <td>${{ number_format($venta->monto_total, 2) }}</td>
                <td>${{ number_format($venta->monto_total / $venta->total_ventas, 2) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center">No hay ventas registradas.</td>
            </tr>
        @endforelse
    </tbody>
</table>
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
<a href="{{ route('informes.productos_a_comprar') }}" class="btn btn-danger btn-sm rounded-0 fw-semibold small">
        <i class="bi bi-box-seam"></i> Productos a Comprar
</a>
<a href="{{ route('informes.ventas_por_vendedor') }}" class="btn btn-danger btn-sm rounded-0 fw-semibold small">
    <i class="bi bi-person-badge"></i> Ventas por Vendedor
</a>
@endpush