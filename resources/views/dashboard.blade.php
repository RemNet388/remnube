<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="container py-4">
        @if(session('error'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif        

<div class="row mb-4">
<!-- Card Ventas -->
<div class="col-md-6 mb-3">
    <div class="card shadow-sm h-60">
        <div class="card-body">
            <h6 class="card-title text-muted">Ventas del Mes <h3>$ {{ $ventasMes ?? 0 }}</h3></h6>
            <canvas id="chartVentas" height="60"></canvas>
        </div>
    </div>
</div>
    <!-- Card Clientes y Proveedores -->
    <div class="col-md-6 mb-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="row">
                    <!-- Top 10 Clientes -->
                    <div class="col-6">
                        <h6 class="small">Clientes Deudores</h6>
                        <canvas id="chartTopClientes" height="150"></canvas>
                    </div>
                    <!-- Top 10 Proveedores -->
                    <div class="col-6">
                        <h6 class="small">Proveedores</h6>
                        <canvas id="chartTopProveedores" height="150"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
        <!-- CARDS DE ACCESO -->
        <div class="row">
    <!-- Card: Ventas -->
    <div class="col-md-4 mb-4">
        <a href="{{ route('ventas.index') }}" class="card h-100 text-decoration-none text-dark shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Ventas</h5>
                <p class="card-text">Gestiona las ventas y consulta el historial.</p>
            </div>
        </a>
    </div>

    <!-- Card: Clientes -->
    <div class="col-md-4 mb-4">
        <a href="{{ route('clientes.index') }}" class="card h-100 text-decoration-none text-dark shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Clientes</h5>
                <p class="card-text">Administra tus clientes y sus datos.</p>
            </div>
        </a>
    </div>

    <!-- Card: Productos -->
    <div class="col-md-4 mb-4">
        <a href="{{ route('productos.index') }}" class="card h-100 text-decoration-none text-dark shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Productos</h5>
                <p class="card-text">Controla el inventario y los productos.</p>
            </div>
        </a>
    </div>

    <!-- Card: Caja -->
    <div class="col-md-4 mb-4">
        <a href="{{ route('cajas.index') }}" class="card h-100 text-decoration-none text-dark shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Caja</h5>
                <p class="card-text">Consulta y gestiona la caja diaria.</p>
            </div>
        </a>
    </div>

    @if(auth()->user()->role === 'admin')
        <!-- Card: Compras -->
        <div class="col-md-4 mb-4">
            <a href="{{ route('compras.index') }}" class="card h-100 text-decoration-none text-dark shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Compras</h5>
                    <p class="card-text">Gestiona las compras del sistema.</p>
                </div>
            </a>
        </div>

        <!-- Card: Proveedores -->
        <div class="col-md-4 mb-4">
            <a href="{{ route('proveedores.index') }}" class="card h-100 text-decoration-none text-dark shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Proveedores</h5>
                    <p class="card-text">Gestiona los proveedores del sistema.</p>
                </div>
            </a>
        </div>

        @if(config('custom.plan') >= 2)
            <!-- Cards adicionales solo para Plan Oro / Full -->
            <div class="col-md-4 mb-4">
                <a href="{{ route('marcas.index') }}" class="card h-100 text-decoration-none text-dark shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Marcas y Modelos</h5>
                        <p class="card-text">Gestiona las Marcas y Modelos del sistema.</p>
                    </div>
                </a>
            </div>

            <div class="col-md-4 mb-4">
                <a href="{{ route('categorias.index') }}" class="card h-100 text-decoration-none text-dark shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Categorías</h5>
                        <p class="card-text">Gestiona las Categorías del sistema.</p>
                    </div>
                </a>
            </div>

            <div class="col-md-4 mb-4">
                <a href="{{ route('ordenes.index') }}" class="card h-100 text-decoration-none text-dark shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Servicio Técnico</h5>
                        <p class="card-text">Gestiona los Servicio Técnico del sistema.</p>
                    </div>
                </a>
            </div>
        @endif
    @endif
</div>

    </div>

    <!-- Scripts Chart.js -->
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {

        // Ventas día a día del mes
        new Chart(document.getElementById('chartVentas').getContext('2d'), {
            type: 'line',
            data: {
                labels: @json($ventasLabels ?? []),
                datasets: [{
                    label: 'Ventas',
                    data: @json($ventasData ?? []),
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 3
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { title: { display: true, text: 'Día del mes' } },
                    y: { title: { display: true, text: 'Ventas' }, beginAtZero: true }
                }
            }
        });

        // Top 10 Clientes Deudores
        new Chart(document.getElementById('chartTopClientes').getContext('2d'), {
            type: 'bar',
            data: {
                labels: @json($topClientes->pluck('nombre') ?? []),
                datasets: [{
                    label: 'Saldo',
                    data: @json($topClientes->pluck('saldo') ?? []),
                    backgroundColor: 'rgba(255, 99, 132, 0.6)'
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });

        // Top 10 Proveedores
        new Chart(document.getElementById('chartTopProveedores').getContext('2d'), {
            type: 'bar',
            data: {
                labels: @json($topProveedores->pluck('nombre') ?? []),
                datasets: [{
                    label: 'Saldo',
                    data: @json($topProveedores->pluck('saldo') ?? []),
                    backgroundColor: 'rgba(54, 162, 235, 0.6)'
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });

    });
    </script>
    @endpush
</x-app-layout>
