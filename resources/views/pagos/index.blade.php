@extends('layouts.app')

@section('content')
<div class="container">
    <h2>💵 Pagos Registrados</h2>

    <div class="mb-3">
        <strong>Total Pagos:</strong> ${{ number_format($totalPagos,2) }}
    </div>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Monto</th>
                <th>Motivo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pagos as $pago)
                <tr>
                    <td>{{ $pago->fecha }}</td>
                    <td>${{ number_format($pago->monto,2) }}</td>
                    <td>{{ $pago->motivo ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
