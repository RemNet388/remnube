@extends('layouts.app')

@section('content')
<div class="container mt-3">

    {{-- Encabezado --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-uppercase fw-bold mb-0" style="font-family: 'Roboto', sans-serif; font-size: 16px;">
            Detalle de Venta #{{ $venta->id }}
        </h2>
    </div>

    {{-- Info general --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-2" style="font-size: 0.85rem; font-family: 'Roboto', sans-serif;">
            <div class="row">
                <div class="col-md-3"><strong>Cliente:</strong> {{ $venta->cliente->nombre ?? 'Consumidor Final' }}</div>
                <div class="col-md-3"><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i') }}</div>
                <div class="col-md-3"><strong>Forma de pago:</strong> {{ $venta->formaPago->nombre ?? '-' }}</div>
                <div class="col-md-3"><strong>Total:</strong> ${{ number_format($venta->total, 2, ',', '.') }}</div>
                <div class="col-md-3"><strong>Observaciones:</strong> {{ $venta->observaciones ?? '-' }}</div>                
            </div>
        </div>
    </div>

    {{-- Productos --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-2">
            <h6 class="text-muted mb-2">🛒 Productos</h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered align-middle mb-0" style="font-size: 0.8rem;">
                    <thead class="table-light text-muted">
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($venta->detalles as $detalle)
                            <tr>
                                <td>{{ $detalle->producto->nombre }}</td>
                                <td>{{ $detalle->cantidad }}</td>
                                <td>${{ number_format($detalle->precio_unitario, 2, ',', '.') }}</td>
                                <td>${{ number_format($detalle->subtotal, 2, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-muted">No hay productos en esta venta.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
@push('submenu')
<a href="{{ route('ventas.index') }}" class="btn btn-primary btn-sm">
    ⬅ Volver
</a>
<span style="display: inline-block; width: 120px;"></span>
<button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalImprimirVenta">
    🖨 Imprimir
</button>
@endpush
{{-- Modal imprimir --}}
<div class="modal fade" id="modalImprimirVenta" tabindex="-1" aria-labelledby="modalImprimirVentaLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-0 shadow-sm">
      <div class="modal-header bg-light">
        <h5 class="modal-title" id="modalImprimirVentaLabel">Vista previa de Boleta</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body" style="font-size: 0.85rem; font-family: 'Roboto', sans-serif;">
        {{-- Contenido de la boleta igual al tuyo --}}
<div class="text-center mb-3">
    <img src="{{ asset('/empresa.png') }}" alt="Logo" style="height:60px;">
    <h6 class="mt-2 mb-0 fw-bold">{{ config('empresa.nombre') }}</h6>
    <small class="text-muted">
        {{ config('empresa.direccion') }}<br>
        Tel: {{ config('empresa.telefono') }} | {{ config('empresa.email') }}
    </small>
</div>

        <hr>

        <div class="row mb-2">
            <div class="col"><strong>Venta N°:</strong> {{ $venta->id }}</div>
            <div class="col"><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i') }}</div>
        </div>
        <div class="row mb-2">
            <div class="col"><strong>Cliente:</strong> {{ $venta->cliente->nombre ?? 'Consumidor Final' }}</div>
            <div class="col"><strong>Forma de Pago:</strong> {{ $venta->formaPago->nombre ?? '-' }}</div>
        </div>
@if(!empty($venta->observaciones))
    <div class="row mt-2">
        <div class="col">
            <strong>Observaciones:</strong> {{ $venta->observaciones }}
        </div>
    </div>
@endif
        <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle mb-2" style="font-size: 0.8rem;">
                <thead class="table-light text-muted">
                    <tr>
                        <th>Producto</th>
                        <th class="text-end">Cant.</th>
                        <th class="text-end">P. Unit.</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($venta->detalles as $detalle)
                        <tr>
                            <td>{{ $detalle->producto->nombre }}</td>
                            <td class="text-end">{{ $detalle->cantidad }}</td>
                            <td class="text-end">${{ number_format($detalle->precio_unitario, 2, ',', '.') }}</td>
                            <td class="text-end">${{ number_format($detalle->subtotal, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="fw-bold">
                        <td colspan="3" class="text-end">TOTAL</td>
                        <td class="text-end">${{ number_format($venta->total, 2, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="imprimirVenta({{ $venta->id }})">
            🖨 Imprimir
        </button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script>
function imprimirVenta(ventaId) {
    const url = `/ventas/${ventaId}/print`; // Ajusta la ruta a tu PDF/boleta
    const w = window.open(url, '_blank');
    w.onload = function() {
        w.print();
        // Opcional: cerrar automáticamente después de imprimir
        // w.close();
    };
}
</script>
