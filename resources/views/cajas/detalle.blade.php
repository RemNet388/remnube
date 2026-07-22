@extends('layouts.app')

@section('content')
<div class="container mt-3">
    <div class="mb-3">
    <h2 class="text-uppercase fw-bold" style="font-family: 'Roboto', sans-serif; font-size: 16px;">Detalle de Caja – {{ \Carbon\Carbon::parse($caja->fecha_apertura)->format('d/m/Y') }}</h2>
</div>
    {{-- Ventas --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-2">
            <h6 class="text-muted mb-2">💰 Ventas</h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered align-middle mb-1" style="font-size: 0.8rem;">
                    <thead class="table-light text-muted">
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Forma de Pago</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ventas as $venta)
                            <tr>
                                <td>{{ $venta->id }}</td>
                                <td>                
                                    <a href="{{ route('ventas.show', $venta->id) }}" class="text-decoration-none">
                                    {{ $venta->cliente->nombre ?? 'Consumidor Final' }}
                                    </a>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i') }}</td>
                                <td>${{ number_format($venta->total, 2, ',', '.') }}</td>
                                <td>{{ $venta->formaPago->nombre ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-muted">No hay ventas registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <h6 class="mt-2">Totales por Forma de Pago</h6>
            <ul class="mb-0 small text-muted">
                @foreach($totalesVentasPorFP as $fpId => $total)
                    <li>{{ \App\Models\FormaPago::find($fpId)->nombre ?? '-' }}: 
                        ${{ number_format($total, 2, ',', '.') }}</li>
                @endforeach
            </ul>
        </div>
    </div>

    {{-- Compras --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-2">
            <h6 class="text-muted mb-2">🛒 Compras</h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered align-middle mb-1" style="font-size: 0.8rem;">
                    <thead class="table-light text-muted">
                        <tr>
                            <th>#</th>
                            <th>Proveedor</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Forma de Pago</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($compras as $compra)
                            <tr>
                                <td>{{ $compra->id }}</td>
                                <td>{{ $compra->proveedor->nombre ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('compras.show', $compra->id) }}" class="text-decoration-none">
                                        {{ $compra->proveedor->nombre ?? '-' }}
                                    </a>
                                </td>
                                <td>${{ number_format($compra->total, 2, ',', '.') }}</td>
                                <td>{{ $compra->formaPago->nombre ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-muted">No hay compras registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <h6 class="mt-2">Totales por Forma de Pago</h6>
            <ul class="mb-0 small text-muted">
                @foreach($totalesComprasPorFP as $fpId => $total)
                    <li>{{ \App\Models\FormaPago::find($fpId)->nombre ?? '-' }}: 
                        ${{ number_format($total, 2, ',', '.') }}</li>
                @endforeach
            </ul>
        </div>
    </div>

    {{-- Resumen --}}
    <div class="alert alert-light border p-2">
        <strong>Total Neto Caja:</strong> 
        ${{ number_format($totalCaja, 2, ',', '.') }}
    </div>
</div>
@endsection
@push('submenu')
<a href="{{ route('cajas.historico') }}" class="btn btn-primary btn-sm">
     ⬅ Volver al histórico
</a>
@endpush