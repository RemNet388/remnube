@extends('layouts.app')

@section('content')
<div class="container" style="font-family: 'Roboto', sans-serif; font-size: 13px;">
    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0 text-uppercase">Detalle de Compra #{{ $compra->id }}</h4>
    </div>

    <!-- Card con datos principales -->
    <div class="card border-light shadow-sm mb-3">
        <div class="card-body py-2">
            <div class="d-flex flex-wrap justify-content-between" style="row-gap: 5px;">
                <span><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($compra->fecha)->format('d/m/Y') }}</span>
                <span><strong>Comprobante:</strong> {{ $compra->numero_comprobante ?? '-' }}</span>
                <span><strong>Proveedor:</strong> {{ $compra->proveedor->nombre ?? '-' }}</span>
                <span><strong>Forma de Pago:</strong> {{ $compra->formaPago->nombre ?? '-' }}</span>
                <span><strong>Total:</strong> ${{ number_format($compra->total, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Tabla de productos -->
    <div class="card border-light shadow-sm">
        <div class="card-body p-2">
            <h6 class="mb-3 text-uppercase">Productos</h6>
            <table class="table table-sm table-bordered table-striped align-middle mb-0" style="font-size: 13px;">
                <thead class="table-light">
                    <tr>
                        <th>Producto</th>
                        <th class="text-center">Cant.</th>
                        <th class="text-end">Precio Compra</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @php $total = 0; @endphp
                    @foreach($compra->detalles as $detalle)
                        @php $subtotal = $detalle->cantidad * $detalle->precio_unitario; $total += $subtotal; @endphp
                        <tr>
                            <td>{{ $detalle->producto->nombre ?? '-' }}</td>
                            <td class="text-center">{{ $detalle->cantidad }}</td>
                            <td class="text-end">${{ number_format($detalle->precio_unitario, 2) }}</td>
                            <td class="text-end">${{ number_format($subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">Total</th>
                        <th class="text-end">${{ number_format($total, 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
 @push('submenu')
<a href="{{ route('compras.index') }}" class="btn btn-primary btn-sm" style="border-radius: 0; font-size: 13px;">
    ⬅ Volver
</a>
        <span style="display: inline-block; width: 20px;"></span>

@endpush 