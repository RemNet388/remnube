<x-app-layout>
    <div class="container py-2" style="font-family: 'Roboto', sans-serif; font-size: 0.75rem;">
        <h2 class="fw-bold mb-3">Productos a Comprar</h2>

        <div class="table-responsive border rounded mb-2">
            <table class="table table-sm table-bordered mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Producto</th>
                        <th>Stock</th>
                        <th>Total Vendido</th>
                        <th>Criticidad</th>
                        <th>Precio Compra</th>
                        <th>Proveedores</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($productos as $prod)
                        <tr>
                            <td>{{ $prod->nombre }}</td>
                            <td class="text-center">{{ $prod->stock }}</td>
                            <td class="text-center">{{ $prod->total_vendido }}</td>
                            <td class="text-center">{{ number_format($prod->criticidad * 100, 2) }}%</td>
                            <td class="text-end">${{ number_format($prod->precio_compra, 2) }}</td>
                            <td>
                                @foreach($prod->detalleCompras as $detalle)
                                    @if($detalle->compra && $detalle->compra->proveedor)
                                        {{ $detalle->compra->proveedor->nombre }}@if(!$loop->last), @endif
                                    @endif
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

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
</x-app-layout>