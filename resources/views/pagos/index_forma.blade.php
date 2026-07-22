@extends('layouts.app')

@section('content')
<div class="container">
    <h2>💰 Pagos</h2>

    <div class="row mb-3">
        {{-- Formulario de nuevo pago --}}
        <div class="col-md-8">
            <form method="POST" action="{{ route('pagos.store') }}" class="row g-2 align-items-center">
                @csrf
                <div class="col-md-3">
                    <input type="date" name="fecha" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-3">
                    <input type="number" step="0.01" name="monto" class="form-control" placeholder="Monto" required>
                </div>
                <div class="col-md-3">
                    <input type="text" name="motivo" class="form-control" placeholder="Concepto">
                </div>
                <div class="col-md-2">
                    <select name="forma_pago_id" class="form-control" required>
                        @foreach($formasPago as $fp)
                            <option value="{{ $fp->id }}">{{ $fp->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <button class="btn btn-primary w-100">Registrar</button>
                </div>
            </form>
        </div>

        {{-- Total pagos --}}
        <div class="col-md-4">
            <div class="card p-3">
                <h4>Total Pagos: ${{ number_format($totalPagos ?? 0,2) }}</h4>
            </div>
        </div>
    </div>

    {{-- Lista de pagos --}}
    <table class="table table-bordered">
        <thead>
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
                    <td>{{ $pago->motivo }}</td>
                    <td>{{ $pago->formaPago->nombre }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
