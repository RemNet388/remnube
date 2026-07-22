@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Caja del Día - {{ $caja->fecha->format('d/m/Y') }}</h1>

    <div class="card mb-4">
        <div class="card-body">
            <p><strong>Saldo Inicial:</strong> ${{ number_format($caja->saldo_inicial, 2) }}</p>
            <p><strong>Saldo Final:</strong> ${{ number_format($caja->saldo_final, 2) }}</p>
            <p><strong>Observaciones:</strong> {{ $caja->observaciones ?? 'Sin observaciones' }}</p>
        </div>
    </div>

    <h4>Movimientos</h4>
    <table class="table table-sm table-bordered table-striped align-middle">
    <thead class="table-light">
            <tr>
                <th>Hora</th>
                <th>Tipo</th>
                <th>Concepto</th>
                <th>Monto</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($caja->movimientos as $mov)
                <tr>
                    <td>{{ $mov->created_at->format('H:i') }}</td>
                    <td>
                        @if($mov->tipo === 'ingreso')
                            <span class="badge bg-success">Ingreso</span>
                        @else
                            <span class="badge bg-danger">Egreso</span>
                        @endif
                    </td>
                    <td>{{ $mov->concepto }}</td>
                    <td>${{ number_format($mov->monto, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">No hay movimientos registrados hoy.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
