@extends('layouts.app')

@section('content')
<div class="container my-4">

    {{-- Título principal --}}
    <h2 class="text-uppercase fw-bold mb-4" style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
        Informe de Movimientos de Stock
    </h2>

    {{-- Formulario de filtros --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Desde</label>
                    <input type="date" name="desde" value="{{ $desde }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Hasta</label>
                    <input type="date" name="hasta" value="{{ $hasta }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <button class="btn btn-primary me-2">Filtrar</button>
                    <a href="{{ route('informes.movimientos_stock.pdf', ['desde' => $desde, 'hasta' => $hasta]) }}" target="_blank" class="btn btn-danger">Imprimir PDF</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Top 5 productos más vendidos --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-light fw-bold text-uppercase">
            📊 Top 5 productos más vendidos
        </div>
        <div class="card-body row">
            <div class="col-md-6">
                <ul class="list-group list-group-flush">
                    @foreach($masVendidos->take(5) as $item)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $item->producto->nombre ?? '—' }}
                            <span class="badge bg-primary rounded-pill">{{ $item->total }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="col-md-6">
                <canvas id="topVendidosChart" style="height:250px;"></canvas>
            </div>
        </div>
    </div>

    {{-- Evolución mensual y distribución por categoría lado a lado --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light fw-bold text-uppercase">
                    📈 Evolución mensual de ventas
                </div>
                <div class="card-body">
                    <canvas id="ventasMensualesChart" style="height:250px;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light fw-bold text-uppercase">
                    🥧 Distribución por categoría
                </div>
                <div class="card-body">
                    <canvas id="categoriasChart" style="height:250px;"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // === Top 5 productos más vendidos ===
    new Chart(document.getElementById('topVendidosChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($masVendidos->take(5)->pluck('producto.nombre')) !!},
            datasets: [{
                label: 'Unidades vendidas',
                data: {!! json_encode($masVendidos->take(5)->pluck('total')) !!},
                borderWidth: 1,
                backgroundColor: 'rgba(54, 162, 235, 0.6)'
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // === Evolución mensual de ventas ===
    new Chart(document.getElementById('ventasMensualesChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($ventasMensuales->pluck('mes')) !!},
            datasets: [{
                label: 'Ventas por mes',
                data: {!! json_encode($ventasMensuales->pluck('total')) !!},
                borderColor: 'rgba(75, 192, 192, 1)',
                tension: 0.2,
                fill: true,
                backgroundColor: 'rgba(75, 192, 192, 0.2)'
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } } }
    });

    // === Distribución por categoría ===
    const categoriasCtx = document.getElementById('categoriasChart');
    new Chart(categoriasCtx, {
        type: 'pie',
        data: {
labels: {!! json_encode($ventasPorCategoria->pluck('categoria')) !!},
datasets: [{
    data: {!! json_encode($ventasPorCategoria->pluck('total')) !!},

                backgroundColor: [
                    'rgba(255, 99, 132, 0.6)',
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(255, 206, 86, 0.6)',
                    'rgba(75, 192, 192, 0.6)',
                    'rgba(153, 102, 255, 0.6)',
                    'rgba(255, 159, 64, 0.6)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
</script>
@endsection
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