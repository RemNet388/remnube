@extends('layouts.app')

@section('content')
<div class="container my-4">

    {{-- Caja abierta --}}
    <div class="row mb-4">

{{-- SALDO --}}
<div class="col-md-9">
    <div class="card shadow-sm">
        <div class="card-body text-center">
            <h5 class="text-uppercase text-muted">Saldo</h5>
            
            {{-- Saldo total de la caja actual --}}
            <h1 class="display-4 text-success">
                ${{ number_format($totalCajaActual, 2) }}
            </h1>

            {{-- Cuadritos por forma de pago --}}
{{-- Cuadritos por forma de pago --}}
@php
    $cuentaCorrienteId = 2; // ID de Cuenta Corriente
    $fpSaldos = collect($saldosPorFormaPago)->except($cuentaCorrienteId); // todas menos CC
    $saldoCC = $saldosPorFormaPago[$cuentaCorrienteId] ?? 0; // saldo de CC
@endphp

<div class="row mt-3 g-2 justify-content-center">
    {{-- Saldos normales --}}
    @foreach($fpSaldos as $fpId => $monto)
        <div class="col-auto">
            <div class="card text-center shadow-sm" 
                 style="min-width: 100px; {{ $fpId == 1 ? 'background-color: #e6f7ff;' : '' }}">
                <div class="card-body p-2">
                    <small class="text-muted">{{ $formasPago[$fpId] ?? 'Desconocido' }}</small>
                    <div class="fw-bold">${{ number_format($monto, 2) }}</div>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Cuadro de Cuenta Corriente al final --}}
    @if($saldoCC > 0)
        <div class="col-auto">
            <div class="card text-center shadow-sm bg-danger text-white" style="min-width: 100px;">
                <div class="card-body p-2">
                    <small class="text-muted">{{ $formasPago[$cuentaCorrienteId] ?? 'Cuenta Corriente' }}</small>
                    <div class="fw-bold">${{ number_format($saldoCC, 2) }}</div>
                </div>
            </div>
        </div>
    @endif
</div>

            {{-- Saldo inicial y botón historial --}}
            <p class="mt-2 mb-0">Saldo inicial: ${{ number_format($cajaAbierta->monto_inicial, 2) }}</p>
            <a href="{{ route('cajas.historico', $cajaAbierta) }}" class="btn btn-info btn-sm mt-1">Ver historial de cajas</a>
        </div>
    </div>
</div>



        {{-- Caja chica / Cerrar caja --}}
        <div class="col-md-3">
            <div class="card shadow-sm">
            </div>

            {{-- Total Retiros --}}
            <div class="card border-warning shadow-sm mt-3">
                <div class="card-header bg-warning d-flex justify-content-between align-items-center">
                    💸 Retiros
                    <a href="{{ route('retiros.store') }}" class="btn btn-sm btn-info">Ver detalles</a>
                </div>
            </div>
        </div>
    </div>

{{-- Movimientos de la caja actual --}}
<div class="card shadow-sm mt-4">
    <div class="card-header bg-secondary text-white">
        📋 Movimientos de la caja actual
    </div>
    <div class="card-body p-0">
        <table class="table table-sm table-bordered table-striped align-middle" style="font-family: 'Roboto', sans-serif; font-size: 13px;">
            <thead class="table-light">
                <tr>
                    <th>Fecha / Hora</th>
                    <th>Tipo</th>
                    <th>Concepto</th>
                    <th>Monto</th>
                    <th>Forma de Pago</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movimientosCaja as $mov)
                    <tr>
                        <td>{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ ucfirst($mov->tipo) }}</td>
                        <td>{{ $mov->concepto }}</td>
                        <td>
                        @if($mov->tipo === 'egreso')
                            <span class="text-danger">- ${{ number_format($mov->monto, 2) }}</span>
                        @else
                            <span class="text-success">$ {{ number_format($mov->monto, 2) }}</span>
                        @endif
                        </td>
                        <td>{{ $mov->formaPago->nombre ?? '-' }}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2 detalle-btn"
                                data-id="{{ $mov->id }}">
                                <i class="bi bi-eye"></i> Ver
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No hay movimientos</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-end mt-2">
    {{ $movimientosCaja->links('pagination::bootstrap-5') }}
</div>


</div>

<div class="modal fade" id="detalleMovimientoModal" tabindex="-1" aria-labelledby="detalleMovimientoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle del Movimiento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                {{-- Aquí se inyecta el contenido dinámico vía JS --}}
            </div>
        </div>
    </div>
</div>


@push('scripts')
<script>
document.querySelectorAll('.detalle-btn').forEach(button => {
    button.addEventListener('click', async () => {
        const id = button.dataset.id;
        const modalBody = document.querySelector('#detalleMovimientoModal .modal-body');

        try {
            const res = await fetch(`/movimientos/${id}/detalle`);
            const html = await res.text();
            modalBody.innerHTML = html;
            new bootstrap.Modal(document.getElementById('detalleMovimientoModal')).show();
        } catch (error) {
            console.error(error);
            modalBody.innerHTML = '<p class="text-danger">No se pudo cargar el detalle.</p>';
        }
    });
});
</script>

@endpush
@endsection
@push('submenu')
<form method="POST" action="{{ route('cajas.cerrar', $cajaAbierta) }}" class="d-flex align-items-center me-2">
    @csrf
    <input type="text" name="fondo_proximo" value="{{ $cajaAbierta->fondo_proximo }}" placeholder="Monto a dejar"
           style="border-radius: 0; flex: 1; min-width: 180px; max-width: 250px;">

    <!-- Botón cerrar caja -->
    <button type="submit" class="btn btn-secondary btn-sm ms-2" 
            style="border-radius: 0; min-width: 40px; padding: 0.35rem 0.75rem;">
        <i class="btn btn-success"></i> 🔒 Cerrar caja
    </button>
</form>
@endpush
