@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detalle de Compra #{{ $compra->id }}</h1>

    <div class="card mb-3">
        <div class="card-body">
            <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($compra->fecha)->format('d/m/Y') }}</p>
            <p><strong>Proveedor:</strong> {{ $compra->proveedor->nombre ?? '' }}</p>
            <p><strong>Forma de Pago:</strong> {{ $compra->formaPago->nombre ?? '' }}</p>
            <p><strong>Total:</strong> ${{ number_format($compra->total, 2) }}</p>
        </div>
    </div>

    <h4>Productos</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cant.</th>
                <th>Precio Compra</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($compra->detalles as $detalle)
                <tr>
                    <td>{{ $detalle->producto->nombre ?? '' }}</td>
                    <td>{{ $detalle->cantidad }}</td>
                    <td>${{ number_format($detalle->precio_unitario, 2) }}</td>
                    <td>${{ number_format($detalle->cantidad * $detalle->precio_unitario, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('compras.index') }}" class="btn btn-secondary">Regresar</a>
</div>
@endsection
