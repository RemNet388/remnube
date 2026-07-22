@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">📋 Detalle de Caja del {{ \Carbon\Carbon::parse($caja->fecha)->format('d/m/Y') }}</h2>

    <a href="{{ route('caja.index') }}" class="btn btn-secondary mb-3">⬅ Volver a cajas</a>

    @if($movimientos->count())
        <table class="table table-striped table-hover">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Concepto</th>
                    <th>Monto</th>
                </tr>
            </thead>
            <tbody>
                @foreach($movimientos as $index => $mov)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($mov->created_at)->format('d/m/Y H:i') }}</td>
                        <td>{{ ucfirst($mov->tipo) }}</td>
                        <td>{{ $mov->concepto }}</td>
                        <td>
                            @if($mov->tipo === 'egreso')
                                -{{ number_format($mov->monto, 2) }}
                            @else
                                {{ number_format($mov->monto, 2) }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-end">Total caja:</th>
                    <th>
                        {{ number_format($movimientos->sum(function($mov){
                            return $mov->tipo === 'ingreso' ? $mov->monto : -$mov->monto;
                        }), 2) }}
                    </th>
                </tr>
            </tfoot>
        </table>
    @else
        <div class="alert alert-info">No hay movimientos registrados en esta caja aún.</div>
    @endif
</div>
@endsection
