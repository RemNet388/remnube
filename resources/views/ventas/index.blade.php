@extends('layouts.app')

@section('content')
<div class="mb-3">
    <h2 class="text-uppercase fw-bold" style="font-family: 'Roboto', sans-serif; font-size: 16px;">Ventas</h2>
</div>
<!-- 📋 Tabla de ventas -->
<table class="table table-sm table-bordered table-striped align-middle" style="font-family: 'Roboto', sans-serif; font-size: 13px;">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>Fecha</th>
            <th>Cliente</th>
            <th>Total</th>
            <th>Forma de Pago</th>
            <th>Vendedor</th> <!-- Nueva columna -->
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse($ventas as $venta)
            <tr>
                <td>{{ $venta->id }}</td>
                <td>{{ $venta->fecha }}</td>
                <td>{{ $venta->cliente->nombre ?? 'Consumidor Final' }}</td>
                <td>${{ number_format($venta->total, 2) }}</td>
                <td>{{ $venta->formaPago->nombre ?? '-' }}</td>
                <td>{{ $venta->usuario->name ?? '-' }}</td> <!-- Vendedor -->
                <td>
                    <a href="{{ route('ventas.show', $venta->id) }}" class="btn btn-info btn-sm" style="border-radius: 0;" title="Ver">
                        <i class="bi bi-eye"></i>
                    </a>
                    <form action="{{ route('ventas.destroy', $venta->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" style="border-radius: 0;" 
                                onclick="return confirm('¿Seguro que deseas eliminar esta venta?')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center">No hay ventas registradas.</td>
            </tr>
        @endforelse
    </tbody>
</table>

@endsection

@push('styles')
<style>
    .ventas-submenu-actions {
        gap: 0.5rem;
    }

    .ventas-submenu {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .ventas-submenu-search {
        width: 350px;
    }

    .ventas-submenu-pagination {
        margin-top: 2px;
    }

    .ventas-submenu-pagination .pagination {
        margin-bottom: 0;
    }

    @media (max-width: 768px) {
        body > .container.mb-5 {
            margin-bottom: 0.5rem !important;
            padding-bottom: 86px !important;
        }

        #barra-submenu {
            padding-bottom: env(safe-area-inset-bottom);
        }

        #barra-submenu > .container {
            gap: 0.4rem !important;
            padding: 0.35rem 0.5rem !important;
        }

        #barra-submenu > .container > .me-3 {
            margin-right: 0 !important;
            padding: 3px 5px !important;
        }

        #barra-submenu > .container > .me-3 img {
            height: 24px !important;
        }

        #barra-submenu > .container > .d-flex.align-items-center:last-child {
            flex: 1 1 auto !important;
            min-width: 0;
            overflow: visible;
        }

        .ventas-submenu {
            width: 100%;
            min-width: 0;
            flex-direction: column;
            align-items: stretch;
            gap: 0.25rem;
        }

        .ventas-submenu-actions {
            width: 100%;
            min-width: 0;
            gap: 0.35rem;
        }

        .ventas-submenu-search {
            flex: 1 1 auto;
            width: auto;
            min-width: 0;
        }

        .ventas-submenu-search input {
            min-width: 0;
        }

        .ventas-submenu-pagination {
            margin-top: 0;
            overflow-x: auto;
        }

        .ventas-submenu-pagination nav,
        .ventas-submenu-pagination .pagination {
            width: 100%;
        }

        .ventas-submenu-pagination .pagination {
            justify-content: center;
            flex-wrap: nowrap;
        }

        .ventas-submenu-pagination .page-link {
            font-size: 0.75rem;
            padding: 0.2rem 0.4rem;
        }
    }
</style>
@endpush

@push('submenu')
<div class="ventas-submenu">
<div class="ventas-submenu-actions d-flex align-items-center">
    <!-- Buscador -->
    <form action="{{ route('ventas.index') }}" method="GET" class="ventas-submenu-search d-flex">
        <input type="text" name="buscar" value="{{ request('buscar') }}" 
               class="form-control form-control-sm" placeholder="Buscar cliente..." 
               style="border-radius: 0; flex: 1;">
        <button type="submit" class="btn btn-outline-secondary btn-sm ms-1" style="border-radius: 0;">
            <i class="bi bi-search"></i>
        </button>
    </form>

    <!-- Botón Nueva Venta -->
    <a href="{{ route('ventas.create') }}" class="btn btn-primary btn-sm" 
       style="border-radius: 0; font-size: 13px; flex-shrink: 0;">
        <i class="bi bi-plus"></i> Nueva Venta
    </a>
</div>

<!-- Paginación justo debajo, compacta -->
<div class="ventas-submenu-pagination d-flex justify-content-center">
    {{ $ventas->links() }}
</div>
</div>
@endpush
