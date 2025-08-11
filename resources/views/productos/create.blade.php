@extends('layouts.app')

@section('content')
<h2>Nuevo Producto</h2>

@if($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
    </ul>
</div>
@endif

<form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="mb-3">
        <label>Nombre</label>
        <input type="text" name="nombre" class="form-control form-control-sm" value="{{ old('nombre') }}" required>
    </div>

    <div class="mb-3">
        <label>Categoría</label>
        <select name="categoria_id" class="form-control form-control-sm" required>
            <option value="">-- Seleccione --</option>
            @foreach($categorias as $cat)
                <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
            @endforeach
        </select>
    </div>
<div class="form-group">
    <label for="codigo">Código de barras</label>
    <input type="text" name="codigo" id="codigo" class="form-control form-control-sm" value="{{ old('codigo', $producto->codigo ?? '') }}">
</div>

    <div class="mb-3">
        <label>Precio Compra</label>
        <input type="number" name="precio_compra" step="0.01" class="form-control form-control-sm" value="{{ old('precio_compra') }}">
    </div>

    <div class="mb-3">
        <label>Precio Venta</label>
        <input type="number" name="precio_venta" step="0.01" class="form-control form-control-sm" value="{{ old('precio_venta') }}">
    </div>

    <div class="mb-3">
        <label>Stock</label>
        <input type="number" name="stock" class="form-control form-control-sm" value="{{ old('stock') }}">
    </div>

    <div class="mb-3">
        <label>Imagen</label>
        <input type="file" name="imagen" class="form-control form-control-sm">
    </div>

    <button class="btn btn-success">Guardar</button>
    <a href="{{ route('productos.index') }}" class="btn btn-secondary">Cancelar</a>
</form>
@endsection
