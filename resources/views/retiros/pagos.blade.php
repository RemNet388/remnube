@extends('layouts.app')

@section('content')
<div class="container" style="font-family: 'Roboto', sans-serif; font-size: 0.85rem;">
    <h2 class="mb-3 text-uppercase fw-bold">💰 Pagos</h2>

    <div class="row mb-3">
        {{-- Formulario de nuevo pago --}}
        <div class="col-md-8">
            <form method="POST" action="{{ route('pagos.store') }}" class="row g-2 align-items-center">
                @csrf
                <div class="col-md-3">
                    <input type="date" name="fecha" 
                           class="form-control form-control-sm rounded-0" 
                           value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-3">
                    <input type="number" step="0.01" name="monto" 
                           class="form-control form-control-sm rounded-0" 
                           placeholder="Monto" required>
                </div>
                <div class="col-md-3">
                    <input type="text" name="concepto" 
                           class="form-control form-control-sm rounded-0" 
                           placeholder="Concepto">
                </div>
                <div class="col-md-2">
                    <select name="forma_pago_id" 
                            class="form-select form-select-sm rounded-0" 
                            required>
                        @foreach($formasPago as $fp)
                            <option value="{{ $fp->id }}">{{ $fp->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <button class="btn btn-primary btn-sm w-100 rounded-0">Registrar</button>
                </div>
            </form>
        </div>

        {{-- Total pagos --}}
        <div class="col-md-4">
            <div class="card p-3 rounded-0 shadow-sm">
                <h5 class="small fw-bold">Total Pagos</h5>
                <p class="fs-6 fw-bold">$ {{ number_format($totalPagos ?? 0,2) }}</p>
            </div>
        </div>
    </div>

    {{-- Lista de pagos --}}
    <div class="table-responsive">
        <table class="table table-sm table-bordered table-striped align-middle rounded-0">
            <thead class="table-light">
                <tr>
                    <th>Fecha</th>
                    <th>Monto</th>
                    <th>Concepto</th>
                    <th>Forma de Pago</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pagos as $pago)
                    <tr>
                        <td>{{ $pago->fecha }}</td>
                        <td>{{ number_format($pago->monto,2) }}</td>
                        <td>{{ $pago->concepto }}</td>
                        <td>{{ $pago->formaPago->nombre }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
