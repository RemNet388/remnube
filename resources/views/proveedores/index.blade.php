@extends('layouts.app')

@section('content')
<div class="mb-3">
    <h2 class="text-uppercase fw-bold" style="font-family: 'Roboto', sans-serif; font-size: 16px;">Proveedores</h2>
</div>

    {{-- Mensaje de éxito --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Tabla de proveedores --}}
    <div class="table-responsive">
        <table class="table table-sm table-bordered table-striped align-middle" style="border-radius: 0; font-family: 'Roboto', sans-serif; font-size: 13px;">
            <thead class="table-light text-center">
                <tr>
                    <th>Nombre</th>
                    <th>Dirección</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>Saldo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody class="text-center">
                @forelse($proveedores as $proveedor)
                    <tr>
                        <td>{{ $proveedor->nombre }}</td>
                        <td>{{ $proveedor->direccion }}</td>
                        <td>{{ $proveedor->telefono }}</td>
                        <td>{{ $proveedor->email }}</td>
                        <td>${{ number_format($proveedor->saldo ?? 0, 2) }}
                        <button class="btn btn-sm btn-success ms-2" 
                                data-bs-toggle="modal" 
                                data-bs-target="#modalPagar{{ $proveedor->id }}"
                                style="border-radius: 0;">
                            <i class="bi bi-cash-coin"></i> Pagar
                        </button>

                        </td>
                        <td>
{{-- Ver detalles / Cuenta corriente --}}
<a href="{{ route('proveedores.cta_corriente', $proveedor->id) }}" 
   class="btn btn-info btn-sm" 
   style="border-radius: 0; font-family: 'Roboto', sans-serif;">
    <i class="fa fa-eye"></i> Cta. Cte
</a>                            
                            {{-- Editar --}}
                            <a href="{{ route('proveedores.edit', $proveedor) }}" class="btn btn-warning btn-sm" style="border-radius: 0; font-family: 'Roboto', sans-serif;">
                                <i class="fa fa-edit"></i> Editar
                            </a>

                            {{-- Eliminar --}}
                            <form action="{{ route('proveedores.destroy', $proveedor) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que desea eliminar este proveedor?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" style="border-radius: 0; font-family: 'Roboto', sans-serif;">
                                    <i class="fa fa-trash"></i> Eliminar
                                </button>
                            </form>

                            {{-- Modal de pago --}}
<div class="modal fade" id="modalPagar{{ $proveedor->id }}" tabindex="-1" aria-labelledby="modalPagarLabel{{ $proveedor->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-sm rounded-3">
            <form action="{{ route('proveedores.pagar.store', $proveedor) }}" method="POST">
                @csrf
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold" id="modalPagarLabel{{ $proveedor->id }}">
                        Pagar a {{ $proveedor->nombre }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <label class="form-label fw-semibold mb-0">Saldo actual:</label>
                        <span class="text-end fw-bold">${{ number_format($proveedor->saldo ?? 0, 2) }}</span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Monto a pagar:</label>
                        <input type="number" name="monto" class="form-control text-end" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Forma de pago:</label>
<select name="forma_pago_id" class="form-select" required>
    @foreach($formasPago as $forma)
        @if($forma->id != 2)
            <option value="{{ $forma->id }}">{{ $forma->nombre }}</option>
        @endif
    @endforeach
</select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Comentario:</label>
                        <textarea name="comentario" class="form-control" rows="2" placeholder="Opcional"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-secondary rounded-2">Cancelar</button>
                    <button type="submit" class="btn btn-success rounded-2 fw-bold">Pagar</button>
                </div>
            </form>
        </div>
    </div>
</div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-muted">No hay proveedores registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
@push('submenu')
<form action="{{ route('proveedores.index') }}" method="GET" 
      class="d-flex me-2 align-items-center" style="width: 350px;">
    <input type="text" name="buscar" value="{{ request('buscar') }}" 
           class="form-control form-control-sm" placeholder="Buscar proveedor...">
    <button type="submit" class="btn-search ms-1">
        <i class="bi bi-search"></i>
    </button>
</form>

<a href="{{ route('proveedores.create') }}" class="submenu-link">
    <i class="bi bi-plus"></i> Nuevo Proveedor
</a>
    <div class="ms-auto">
        {{ $proveedores->links() }}
    </div>
@endpush
