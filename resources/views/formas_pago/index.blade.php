@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h2>Formas de Pago</h2>
    <a href="{{ route('formas_pago.create') }}" class="btn btn-primary">Nueva Forma de Pago</a>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-sm table-bordered table-striped align-middle">
    <thead class="table-light">
        <tr>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($formas_pago as $forma)
        <tr>
            <td>{{ $forma->nombre }}</td>
            <td>{{ $forma->descripcion }}</td>
            <td>
                <a href="{{ route('formas_pago.edit', $forma) }}" class="btn btn-warning btn-sm">Editar</a>
                <form action="{{ route('formas_pago.destroy', $forma) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar forma de pago?')">Eliminar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
