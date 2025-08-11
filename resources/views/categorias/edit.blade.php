@extends('layouts.app')

@section('content')
<h2>Editar Categoría</h2>

@if($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
    </ul>
</div>
@endif

<form action="{{ route('categorias.update', $categoria) }}" method="POST">
    @csrf @method('PUT')
    <div class="mb-3">
        <label>Nombre</label>
        <input type="text" name="nombre" value="{{ old('nombre', $categoria->nombre) }}" class="form-control form-control-sm" required>
    </div>
    <div class="mb-3">
        <label>Descripción</label>
        <textarea name="descripcion" class="form-control form-control-sm">{{ old('descripcion', $categoria->descripcion) }}</textarea>
    </div>
    <button class="btn btn-success">Actualizar</button>
    <a href="{{ route('categorias.index') }}" class="btn btn-secondary">Cancelar</a>
</form>
@endsection
