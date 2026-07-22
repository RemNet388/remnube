<div class="p-3" style="font-family: 'Roboto', sans-serif; font-size: 13px;">

    @php
    $cliente = '-';
    $productos = [];    
    // Determinar si es venta o compra
        $esVenta = $movimiento->concepto && str_contains(strtolower($movimiento->concepto), 'venta');
        $esCompra = $movimiento->concepto && str_contains(strtolower($movimiento->concepto), 'compra');

        $operacion = null;
        if($esVenta) {
            $operacion = \App\Models\Venta::find(intval(str_replace('Venta #','',$movimiento->concepto)));
            $cliente = $operacion->cliente->nombre ?? 'Consumidor Final';
            $productos = $operacion ? $operacion->detalles : [];
        } elseif($esCompra) {
            $operacion = \App\Models\Compra::find(intval(str_replace('Compra #','',$movimiento->concepto)));
            $cliente = $operacion->proveedor->nombre ?? '';
            $productos = $operacion ? $operacion->detalles : [];
        }
    @endphp

    <div class="card border-light shadow-sm">
        <div class="card-body">
            <!-- Primera línea: Fecha | Concepto | Tipo -->
            <div class="mb-2">
                <strong>Fecha:</strong> {{ $movimiento->created_at->format('d/m/Y H:i') }} &nbsp;&nbsp;
                <strong>Concepto:</strong> {{ $movimiento->concepto }} &nbsp;&nbsp;
                <strong>Tipo:</strong> {{ ucfirst($movimiento->tipo) }}
            </div>

            <!-- Segunda línea: Detalles: Cliente | Forma de pago -->
<div class="mb-3">
    <strong>Detalles:</strong> {{ $cliente }}
    @if($operacion && !empty($operacion->observaciones))
        &nbsp;&nbsp; | &nbsp;&nbsp;
        <strong>Observaciones:</strong> {{ $operacion->observaciones }}
    @endif
    &nbsp;&nbsp; | &nbsp;&nbsp;
    <strong>Forma de pago:</strong> {{ $movimiento->formaPago->nombre ?? '-' }}
</div>


            <!-- Tabla de productos -->
            @if($productos && count($productos))
                <table class="table table-sm table-bordered align-middle" style="font-size: 13px;">
                    <thead class="table-light">
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalProductos = 0; @endphp
                        @foreach($productos as $detalle)
                            <tr>
                                <td>{{ $detalle->producto->nombre ?? '' }}</td>
                                <td>{{ $detalle->cantidad }}</td>
                                <td>${{ number_format($detalle->precio_unitario,2) }}</td>
                                <td>${{ number_format($detalle->subtotal ?? ($detalle->cantidad * $detalle->precio_unitario),2) }}</td>
                            </tr>
                            @php $totalProductos += $detalle->subtotal ?? ($detalle->cantidad * $detalle->precio_unitario); @endphp
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Total:</th>
                            <th>${{ number_format($totalProductos,2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            @endif
        </div>
    </div>

</div>
