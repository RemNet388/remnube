@extends('layouts.app')

@section('content')
<div class="mb-3">
    <h2 class="text-uppercase fw-bold" style="font-family: 'Roboto', sans-serif; font-size: 16px;">
        Cuentas Corrientes
    </h2>
</div>

<!-- 📋 Tabla de clientes -->
<table class="table table-sm table-bordered table-striped align-middle" style="font-family: 'Roboto', sans-serif; font-size: 13px;">
    <thead class="table-light">
        <tr>
            <th>Nombre</th>
            <th>Email</th>
            <th>Teléfono</th>
            <th>Saldo</th>
            <th>Cta Cte</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse($clientes as $cliente)
            <tr>
                <td>{{ $cliente->nombre }}</td>
                <td>{{ $cliente->email }}</td>
                <td>{{ $cliente->telefono }}</td>
                <td>{{ number_format($cliente->saldo_actual ?? 0, 2) }}</td>
                <td>
                    <!-- Botón Detalle Comprobantes -->
                    <button class="btn btn-primary btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#modalDetalle{{ $cliente->id }}"
                            style="border-radius: 0;">
                        <i class="bi bi-receipt"></i> Detalle
                    </button>

                        <button class="btn btn-sm btn-success ms-2"
                                data-bs-toggle="modal"
                                data-bs-target="#modalPagar{{ $cliente->id }}"
                                style="border-radius: 0;">
                            <i class="bi bi-cash-coin"></i> Cobrar
                        </button>
                </td>
                <td>
                    @if($cliente->id != 1)
                        <a href="{{ route('clientes.edit', $cliente->id) }}" class="btn btn-warning btn-sm" style="border-radius: 0;">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @if(($cliente->saldo_actual == 0) > 0)
                            <form action="{{ route('clientes.destroy', $cliente->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" style="border-radius: 0;"
                                        onclick="return confirm('¿Seguro que deseas eliminar este cliente?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        @endif
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center">No hay clientes con deuda registrados.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<!-- 🔽 Modales Fuera del Table -->
@foreach($clientes as $cliente)
    <!-- Modal DETALLE -->
    <div class="modal fade" id="modalDetalle{{ $cliente->id }}" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Detalle de comprobantes - {{ $cliente->nombre }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            @php
                $movimientos = \App\Models\CuentaCorriente::where('entidad_id', $cliente->id)
                    ->orderBy('fecha', 'desc')
                    ->orderBy('id', 'desc')
                    ->get();
            @endphp

            @if($movimientos->isEmpty())
                <p>No hay movimientos registrados para este cliente.</p>
            @else
                <table class="table table-sm table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Concepto</th>
                            <th class="text-end">Debe</th>
                            <th class="text-end">Haber</th>
                            <th class="text-end">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($movimientos as $mov)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($mov->fecha)->format('d/m/Y') }}</td>
                                <td>{{ $mov->concepto }}</td>
                                <td class="text-end">{{ $mov->debe > 0 ? number_format($mov->debe, 2) : '' }}</td>
                                <td class="text-end">{{ $mov->haber > 0 ? number_format($mov->haber, 2) : '' }}</td>
                                <td class="text-end">{{ number_format($mov->saldo, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 0;">Cerrar</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal PAGAR -->
    <div class="modal fade" id="modalPagar{{ $cliente->id }}" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <form action="{{ route('cuentas_corrientes.pagar', $cliente->id) }}" method="POST">
            @csrf
            <div class="modal-header">
              <h5 class="modal-title">Registrar pago - {{ $cliente->nombre }}</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <p><strong>Saldo actual:</strong> ${{ number_format($cliente->saldo_actual ?? 0, 2) }}</p>

              <div class="mb-3">
                <label class="form-label">Monto a abonar</label>
                <input type="number" step="0.01" name="monto" class="form-control" required>
              </div>

              <div class="mb-3">
                <label class="form-label">Forma de pago</label>
                <select name="forma_pago_id" class="form-select" required>
                  @foreach($formasPago as $forma)
                    @if(strtolower($forma->nombre) !== 'cuenta corriente')
                      <option value="{{ $forma->id }}">{{ $forma->nombre }}</option>
                    @endif
                  @endforeach
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 0;">Cancelar</button>
              <button type="submit" class="btn btn-success" style="border-radius: 0;">Registrar Pago</button>
            </div>
          </form>
        </div>
      </div>
    </div>
@endforeach
@endsection


@push('submenu')
<form action="{{ route('clientes.deudores') }}" method="GET" 
      class="d-flex me-2 align-items-center" style="width: 350px;">
    <input type="text" name="buscar" value="{{ request('buscar') }}" 
           class="form-control form-control-sm" placeholder="Buscar cliente...">
    <button type="submit" class="btn-search ms-1">
        <i class="bi bi-search"></i>
    </button>
</form>

<a href="{{ route('clientes.create') }}" class="submenu-link">
    <i class="bi bi-plus"></i> Nuevo Cliente
</a>
<a href="{{ route('clientes.deudores') }}" class="submenu-link">
    <i class="bi bi-plus"></i> Cuentas Corrientes
</a>
@endpush
