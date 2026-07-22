@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4 text-uppercase" style="font-family: 'Montserrat', 'Segoe UI', 'Helvetica Neue', sans-serif; font-weight: 600;">
        INFORME DE VENTAS
    </h3>

    {{-- Filtro de fechas --}}
    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-3">
            <input type="date" name="desde" class="form-control" value="{{ $desde }}">
        </div>
        <div class="col-md-3">
            <input type="date" name="hasta" class="form-control" value="{{ $hasta }}">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Filtrar</button>
        </div>
        <div class="col-auto">
            <a href="{{ route('informes.ventas.imprimir', ['desde'=>$desde,'hasta'=>$hasta]) }}" class="btn btn-danger">Imprimir PDF</a>
        </div>
    </form>

    {{-- Contenedor principal --}}
    <div class="row g-4 mb-5">
        {{-- Lado izquierdo: resumen + gráfico diario --}}
        <div class="col-md-6">
            <div class="card mb-3 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title">Ventas Totales del Mes</h5>
                    <p class="display-6 fw-bold">${{ number_format($total, 2) }}</p>
                    <small class="text-muted">Mes anterior: ${{ number_format($totalMesAnterior ?? 0, 2) }}</small>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="card-title mb-3">Ventas por Día</h6>
                    <canvas id="ventasPorDia"></canvas>
                </div>
            </div>
        </div>

        {{-- Lado derecho: forma de pago --}}
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="card-title mb-3">Ventas por Forma de Pago</h6>
                    <canvas id="ventasPorFormaPago"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- NUEVO BLOQUE: Movimientos por forma de pago --}}
    <div class="card shadow-sm">
        <div class="card-header bg-light fw-semibold">
            Movimientos de Caja por Forma de Pago
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('informes.ventas') }}" class="row g-2 mb-3">
                <div class="col-md-3">
                    <label class="form-label">Desde</label>
                    <input type="date" name="mov_desde" class="form-control" value="{{ request('mov_desde', $desde) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Hasta</label>
                    <input type="date" name="mov_hasta" class="form-control" value="{{ request('mov_hasta', $hasta) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Forma de Pago</label>
                    <select name="forma_pago_id" class="form-select">
                        @foreach($formasPago as $fp)
                            <option value="{{ $fp->id }}" 
                                {{ request('forma_pago_id', 1) == $fp->id ? 'selected' : '' }}>
                                {{ $fp->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 align-self-end">
                    <button class="btn btn-primary w-100">Filtrar</button>
                </div>
            </form>

            {{-- Resumen --}}
            <div class="row text-center mb-4">
                <div class="col-md-4">
                    <div class="border p-2 bg-light rounded">
                        <strong>Ingresos</strong>
                        <p class="mb-0 text-success fw-bold">${{ number_format($totalIngresos ?? 0, 2) }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border p-2 bg-light rounded">
                        <strong>Egresos</strong>
                        <p class="mb-0 text-danger fw-bold">${{ number_format($totalEgresos ?? 0, 2) }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border p-2 bg-light rounded">
                        <strong>Saldo Neto</strong>
                        <p class="mb-0 fw-bold">
                            ${{ number_format(($totalIngresos ?? 0) - ($totalEgresos ?? 0), 2) }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Listado de movimientos --}}
            <table class="table table-sm table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Concepto</th>
                        <th>Forma de Pago</th>
                        <th class="text-end">Monto</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movimientos as $mov)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($mov->created_at)->format('d/m/Y') }}</td>
                            <td class="text-uppercase">{{ $mov->tipo }}</td>
                            <td>{{ $mov->concepto }}</td>
                            <td>{{ $mov->formaPago->nombre ?? '—' }}</td>
                            <td class="text-end {{ $mov->tipo == 'ingreso' ? 'text-success' : 'text-danger' }}">
                                {{ $mov->tipo == 'ingreso' ? '+' : '-' }}${{ number_format($mov->monto, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No hay movimientos en el rango seleccionado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx1 = document.getElementById('ventasPorDia').getContext('2d');
new Chart(ctx1, {
    type: 'line',
    data: {
        labels: {!! json_encode($ventasPorDia->keys()) !!},
        datasets: [{
            label: 'Ventas por Día',
            data: {!! json_encode($ventasPorDia->values()) !!},
            borderColor: '#007bff',
            backgroundColor: 'rgba(0,123,255,0.1)',
            fill: true,
            tension: 0.3
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        }
    }
});

const ctx2 = document.getElementById('ventasPorFormaPago').getContext('2d');
new Chart(ctx2, {
    type: 'pie',
    data: {
        labels: {!! json_encode($ventasPorFormaPago->keys()) !!},
        datasets: [{
            data: {!! json_encode($ventasPorFormaPago->values()) !!},
            backgroundColor: ['#007bff','#28a745','#ffc107','#dc3545']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});
</script>
@endpush
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
</a><a href="{{ route('informes.ventas_por_vendedor') }}" class="btn btn-danger btn-sm rounded-0 fw-semibold small">
    <i class="bi bi-person-badge"></i> Ventas por Vendedor
</a>
@endpush