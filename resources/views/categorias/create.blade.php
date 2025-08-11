@extends('layouts.app')

@section('content')
<h2>Nueva Categoría</h2>

@if($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
    </ul>
</div>
@endif

<form action="{{ route('categorias.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label>Nombre</label>
        <input type="text" name="nombre" class="form-control form-control-sm" required>
    </div>
    <div class="mb-3">
        <label>Descripción</label>
        <textarea name="descripcion" class="form-control form-control-sm"></textarea>
    </div>
    <button class="btn btn-success">Guardar</button>
    <a href="{{ route('categorias.index') }}" class="btn btn-secondary">Cancelar</a>
</form>
@endsection
