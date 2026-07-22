@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Listado de Gastos</h2>

    {{-- Mensajes flash --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $err)
                <div>{{ $err }}</div>
            @endforeach
        </div>
    @endif

    <div class="mb-3">
        <a href="{{ route('gastos.create') }}" class="btn btn-primary">Nuevo Gasto</a>
    </div>

    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Fecha</th>
                <th>Descripción</th>
                <th>Monto</th>
                <th>Origen</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        @forelse($gastos as $gasto)
            <tr>
                <td>{{ \Carbon\Carbon::parse($gasto->fecha)->format('d/m/Y') }}</td>
                <td>{{ $gasto->descripcion }}</td>
                <td>${{ number_format($gasto->monto, 2, ',', '.') }}</td>
                <td>
                    @if($gasto->retiro)
                        <span class="badge bg-warning text-dark">Retiro #{{ $gasto->retiro->id }}</span>
                        <small>({{ $gasto->retiro->fecha->format('d/m/Y') }})</small>
                    @elseif($gasto->formaPago)
                        <span class="badge bg-info">{{ $gasto->formaPago->nombre }}</span>
                    @else
                        <span class="badge bg-secondary">No especificado</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('gastos.edit', $gasto->id) }}" class="btn btn-sm btn-warning">Editar</a>
                    <form action="{{ route('gastos.destroy', $gasto->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro de eliminar este gasto?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center">No hay gastos registrados.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
