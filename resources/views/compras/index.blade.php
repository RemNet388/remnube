@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Listado de Compras</h2>
    <a href="{{ route('compras.create') }}" class="btn btn-primary mb-3">Nueva Compra</a>
</div>

<div class="container">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Fecha</th>
                <th>Proveedor</th>
                <th>Forma de Pago</th>
                <th>Total</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($compras as $compra)
                <tr>
                    <td>{{ $compra->id }}</td>
                    <td>{{ \Carbon\Carbon::parse($compra->fecha)->format('d/m/Y') }}</td>
                    <td>{{ $compra->proveedor->nombre ?? '' }}</td>
                    <td>{{ $compra->formaPago->nombre ?? '' }}</td>
                    <td>${{ number_format($compra->total, 2) }}</td>
                    <td>
                        <a href="{{ route('compras.show', $compra->id) }}" class="btn btn-info btn-sm">Ver</a>
                        <a href="{{ route('compras.print', $compra->id) }}" class="btn btn-secondary btn-sm">Imprimir</a>
                        <form action="{{ route('compras.destroy', $compra->id) }}" method="POST" style="display:inline-block" onsubmit="return confirm('¿Eliminar esta compra?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No hay compras registradas</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
