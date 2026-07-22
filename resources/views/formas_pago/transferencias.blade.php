@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4 text-uppercase" style="font-family: 'Montserrat', sans-serif; font-weight: 600;">
        TRANSFERENCIAS ENTRE FORMAS DE PAGO
    </h3>

    {{-- Mensaje de éxito --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Formulario de transferencia --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form action="{{ route('formas_pago.transferir') }}" method="POST" class="row g-3">
                @csrf
                <div class="col-md-4">
                    <label class="form-label">Forma de Pago Origen</label>
                    <select name="forma_pago_origen_id" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        @foreach($formasPago as $fp)
                            <option value="{{ $fp->id }}">{{ $fp->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Forma de Pago Destino</label>
                    <select name="forma_pago_destino_id" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        @foreach($formasPago as $fp)
                            <option value="{{ $fp->id }}">{{ $fp->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Monto</label>
                    <input type="number" step="0.01" name="monto" class="form-control" required>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Observaciones (opcional)</label>
                    <input type="text" name="observaciones" class="form-control">
                </div>

                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-arrow-left-right"></i> Registrar Transferencia
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Últimas transferencias --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="mb-3">Últimas Transferencias</h5>
            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Forma de Pago</th>
                        <th>Monto</th>
                        <th>Concepto</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transferencias as $t)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($t->fecha)->format('d/m/Y') }}</td>
                            <td>{{ $t->tipo }}</td>
                            <td>{{ $t->forma_pago }}</td>
                            <td>${{ number_format($t->monto, 2) }}</td>
                            <td>{{ $t->concepto }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted">Sin transferencias registradas</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
@push('submenu')
<a href="{{ route('formas_pago.create') }}" 
   class="btn btn-primary btn-sm" style="border-radius: 0; font-size: 13px;">
   <i class="bi bi-plus"></i> Nueva Forma de Pago
</a>
<a href="{{ route('formas_pago.transferencias') }}" 
   class="btn btn-primary btn-sm" style="border-radius: 0; font-size: 13px;">
   <i class="bi bi-plus"></i> Transferir Entre Cuentas
</a>
@endpush