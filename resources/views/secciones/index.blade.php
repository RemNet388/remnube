@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4 text-uppercase">Secciones</h3>

    <a href="{{ route('secciones.create') }}" class="btn btn-primary mb-3">➕ Crear Sección</a>

    @if(session('success'))
        <div class="alert alert-success small">{{ session('success') }}</div>
    @endif

    <table class="table table-sm table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Slug</th>
                <th>Título</th>
                <th>Activo</th>
                <th>Orden</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($secciones as $seccion)
                <tr>
                    <td>{{ $seccion->id }}</td>
                    <td>{{ $seccion->slug }}</td>
                    <td>{{ $seccion->titulo }}</td>
                    <td>{{ $seccion->activo ? 'Sí' : 'No' }}</td>
                    <td>{{ $seccion->orden }}</td>
                    <td>
                        <a href="{{ route('secciones.edit', $seccion) }}" class="btn btn-sm btn-warning">✏️ Editar</a>
                        <form action="{{ route('secciones.destroy', $seccion) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar esta sección?')">🗑️ Borrar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
