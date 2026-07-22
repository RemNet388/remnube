@extends('layouts.app')

@section('content')

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="mb-3">
    <h2 class="text-uppercase fw-bold" style="font-family: 'Roboto', sans-serif; font-size: 16px;">
        Categorías
    </h2>
</div>

<table class="table table-sm table-bordered table-striped align-middle" 
       style="font-family: 'Roboto', sans-serif; font-size: 13px;">
    <thead class="table-light">
        <tr>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($categorias as $categoria)
        <tr>
            <td>{{ $categoria->nombre }}</td>
            <td>{{ $categoria->descripcion }}</td>
            <td class="d-flex gap-1">
                <a href="{{ route('categorias.edit', $categoria) }}" 
                   class="btn btn-warning btn-sm rounded-0">
                   Editar
                </a>
                <form action="{{ route('categorias.destroy', $categoria) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-sm rounded-0" 
                            onclick="return confirm('¿Eliminar categoría?')">
                        Eliminar
                    </button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection

@push('submenu')
<a href="{{ route('categorias.create') }}" 
   class="btn btn-primary btn-sm rounded-0" 
   style="min-width: 130px; padding: 0.35rem 0.75rem;">
    <i class="bi bi-plus"></i> Nueva Categoría
</a>

<a href="{{ route('productos.index') }}" 
   class="btn btn-primary btn-sm rounded-0 ms-3" 
   style="min-width: 110px; padding: 0.35rem 0.75rem;">
    <i class="bi bi-plus-circle"></i> Productos
</a>
@endpush
