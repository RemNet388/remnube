@extends('layouts.app')

@section('content')
<div class="container my-4">

    <h4 class="mb-2">📅 Informe de productos por vencer</h4>
    <p class="text-muted small">
        Mostrando productos con fecha de vencimiento entre 
        <strong>{{ $hoy->format('d/m/Y') }}</strong> y 
        <strong>{{ $limite->format('d/m/Y') }}</strong>.
    </p>

    @if($productos->isEmpty())
        <div class="alert alert-success small mb-0">
            🎉 No hay productos próximos a vencer en los próximos 30 días.
        </div>
    @else
        <table class="table table-sm table-bordered align-middle text-center small">
            <thead class="table-light">
                <tr>
                    <th style="width: 50%">Producto</th>
                    <th style="width: 25%">Vencimiento</th>
                    <th style="width: 25%">Días restantes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productos as $producto)
                    @php
                        $diasRestantes = intval(now()->diffInDays($producto->fecha_vencimiento, false));
                    @endphp
                    <tr class="
                        {{ $diasRestantes < 0 ? 'table-danger' : 
                           ($diasRestantes <= 7 ? 'table-warning' : '') }}
                    ">
                        <td class="text-start">{{ $producto->nombre }}</td>
                        <td class="fw-bold">{{ $producto->fecha_vencimiento->format('d/m/Y') }}</td>
                        <td>
                            {{ $diasRestantes >= 0 ? $diasRestantes : 'Vencido' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
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