@extends('layouts.app')

@section('content')
<div class="container">
<h2>Caja del {{ $caja->fecha }}</h2>

@foreach($ventasPorFormaPago as $formaPago => $ventas)
    <h4>{{ $formaPago }}</h4>
    <table class="table table-sm table-bordered table-striped align-middle">
    <thead class="table-light">
            <tr>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ventas as $venta)
                <tr>
                    <td>{{ $venta->fecha }}</td>
                    <td>{{ $venta->cliente->nombre ?? 'Consumidor Final' }}</td>
                    <td>${{ number_format($venta->total, 2) }}</td>
                </tr>
            @endforeach
            <tr class="fw-bold">
                <td colspan="2">Subtotal {{ $formaPago }}</td>
                <td>${{ number_format($ventas->sum('total'), 2) }}</td>
            </tr>
        </tbody>
    </table>
@endforeach

<hr>
<h3>Total general: ${{ number_format($totalGeneral, 2) }}</h3>
</div>
<div class="mb-3">
    <a href="{{ route('caja.index') }}" class="btn btn-outline-primary">
        ← Volver a Cajas
    </a>
</div>
@endsection