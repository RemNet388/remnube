@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">📋 Ventas de la caja OK- {{ \Carbon\Carbon::parse($caja->fecha_apertura)->format('d/m/Y') }}</h2>

    <a href="{{ route('cajas.index') }}" class="btn btn-secondary mb-3">⬅ Volver a Cajas</a>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Cliente</th>
                        <th>Forma de pago</th>
                        <th>Total</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ventas as $index => $venta)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $venta->cliente->nombre ?? '—' }}</td>
                            <td>{{ $venta->formaPago->nombre ?? '—' }}</td>
                            <td>{{ number_format($venta->total,2) }}</td>
                            <td>{{ \Carbon\Carbon::parse($venta->created_at)->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No hay ventas registradas para esta caja.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
