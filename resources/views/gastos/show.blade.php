@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Detalle del Gasto</h2>

    <div class="card">
        <div class="card-body">
            <p><strong>Fecha:</strong> {{ $gasto->fecha->format('d/m/Y') }}</p>
            <p><strong>Descripción:</strong> {{ $gasto->descripcion }}</p>
            <p><strong>Monto:</strong> ${{ number_format($gasto->monto, 2, ',', '.') }}</p>

            @if($gasto->retiro)
                <p><strong>Pagado desde Retiro:</strong> Retiro #{{ $gasto->retiro->id }} ({{ $gasto->retiro->fecha->format('d/m/Y') }})</p>
            @else
                <p><strong>Forma de Pago:</strong> {{ $gasto->formaPago->nombre }}</p>
            @endif
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('gastos.index') }}" class="btn btn-secondary">Volver</a>
        <a href="{{ route('gastos.edit', $gasto->id) }}" class="btn btn-warning">Editar</a>
        <form action="{{ route('gastos.destroy', $gasto->id) }}" method="POST" class="d-inline"
              onsubmit="return confirm('¿Seguro que deseas eliminar este gasto?');">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger">Eliminar</button>
        </form>
    </div>
</div>
@endsection
