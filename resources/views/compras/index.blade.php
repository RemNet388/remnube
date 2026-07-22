@extends('layouts.app')

@section('content')
    <!-- Mensajes -->
    @if(session('success'))
        <div class="alert alert-success py-2 px-3 mb-3" style="font-size: 13px;">
            {{ session('success') }}
        </div>
    @endif
<div class="mb-3">
    <h2 class="text-uppercase fw-bold" style="font-family: 'Roboto', sans-serif; font-size: 16px;">Compras</h2>
</div>
    <!-- Tabla -->
    <div class="card border-light shadow-sm">
        <div class="card-body p-2">
            <table class="table table-sm table-bordered table-striped align-middle mb-0" style="font-size: 13px;">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Registro</th>
                        <th>Fecha</th>
                        <th>Comprobante</th>
                        <th>Proveedor</th>
                        <th>Forma de Pago</th>
                        <th>Total</th>
                        <th class="text-center" style="width: 200px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($compras as $compra)
                        <tr>
                            <td>{{ $compra->id }}</td>
                            <td>{{ \Carbon\Carbon::parse($compra->created_at)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($compra->fecha)->format('d/m/Y') }}</td>
                            <td>{{ $compra->numero_comprobante ?? '-' }}</td>
                            <td>{{ $compra->proveedor->nombre ?? '-' }}</td>
                            <td>{{ $compra->formaPago->nombre ?? '-' }}</td>
                            <td>${{ number_format($compra->total, 2) }}</td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('compras.show', $compra->id) }}" 
                                       class="btn btn-info rounded-0 shadow-sm">
                                        Ver
                                    </a>
                                    <form action="{{ route('compras.destroy', $compra->id) }}" method="POST" 
                                          onsubmit="return confirm('¿Eliminar esta compra?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger rounded-0 shadow-sm">
                                            🗑
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No hay compras registradas</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
 @push('submenu')
<a href="{{ route('compras.create') }}" class="btn btn-primary btn-sm" style="border-radius: 0; font-size: 13px;">
    <i class="bi bi-plus"></i> Nueva Compra
</a>
<a href="{{ route('informes.compras_proveedor') }}" class="btn btn-primary btn-sm" style="border-radius: 0; font-size: 13px;">
    <i class="bi bi-plus"></i> Informe de Compras
</a>
        <span style="display: inline-block; width: 20px;"></span>
                {{-- Paginación --}}
            <div class="d-flex justify-content-center mt-3">
                {{ $compras->links() }}
            </div>
@endpush 