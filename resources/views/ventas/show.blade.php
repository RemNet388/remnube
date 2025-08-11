@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detalles de la Venta #{{ $venta->id }}</h1>

    <p><strong>Cliente:</strong> {{ $venta->cliente->nombre ?? 'Consumidor Final' }}</p>
    <p><strong>Fecha:</strong> {{ $venta->fecha }}</p>
    <p><strong>Forma de pago:</strong> {{ $venta->formaPago->nombre ?? '-' }}</p>
    <p><strong>Total:</strong> ${{ number_format($venta->total, 2) }}</p>

    <h4>Productos</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venta->detalles as $detalle)
                <tr>
                    <td>{{ $detalle->producto->nombre }}</td>
                    <td>{{ $detalle->cantidad }}</td>
                    <td>${{ number_format($detalle->precio_unitario, 2) }}</td>
                    <td>${{ number_format($detalle->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('ventas.index') }}" class="btn btn-secondary">Volver</a>
    <a href="{{ route('ventas.print', $venta->id) }}" target="_blank" class="btn btn-primary">
    🖨 Imprimir boleta
</a>

</div>
@endsection
