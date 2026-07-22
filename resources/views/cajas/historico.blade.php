@extends('layouts.app')

@section('content')
<div class="container">
<div class="mb-3">
    <h2 class="text-uppercase fw-bold" style="font-family: 'Roboto', sans-serif; font-size: 16px;">Historial de Cajas</h2>
</div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-2">
            <div class="table-responsive">
                <table class="table table-sm table-bordered align-middle text-center mb-0">
                    <thead class="table-light text-muted">
                        <tr style="font-size: 0.85rem;">
                            <th>#</th>
                            <th>Fecha Apertura</th>
                            <th>Fecha Cierre</th>
                            <th>Estado</th>
                            <th>Inicial</th>
                            <th>Final</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 0.8rem;">
                        @forelse($cajas as $caja)
                            <tr>
                                <td>{{ $caja->id }}</td>
                                <td>{{ \Carbon\Carbon::parse($caja->fecha_apertura)->format('d/m/Y H:i') }}</td>
                                <td>{{ $caja->fecha_cierre ? \Carbon\Carbon::parse($caja->fecha_cierre)->format('d/m/Y H:i') : '-' }}</td>
                                <td>
                                    <span class="badge rounded-pill bg-{{ $caja->estado === 'abierta' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($caja->estado) }}
                                    </span>
                                </td>
                                <td>${{ number_format($caja->monto_inicial, 2, ',', '.') }}</td>
                                <td>${{ number_format($caja->monto_final ?? 0, 2, ',', '.') }}</td>
                                <td>
                                    <a href="{{ route('cajas.detalle', $caja) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        Ver
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-muted">No hay cajas registradas.</td>
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
<a href="{{ route('cajas.index') }}" class="btn btn-primary btn-sm">
    <i class="bi bi-plus"></i> Volver a Caja
</a>
            <span style="display: inline-block; width: 120px;"></span>
            <div class="d-flex justify-content-center mt-3">
                {{ $cajas->links('pagination::bootstrap-5') }}
            </div>
@endpush