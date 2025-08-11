@extends('layouts.app')

@section('content')
<h2>Editar Producto</h2>

@if($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
    </ul>
</div>
@endif

<form action="{{ route('productos.update', $producto) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="mb-3">
        <label>Nombre</label>
        <input type="text" name="nombre" class="form-control form-control-sm" value="{{ old('nombre', $producto->nombre) }}" required>
    </div>

    <div class="mb-3">
        <label>Categoría</label>
        <select name="categoria_id" class="form-control form-control-sm" required>
            @foreach($categorias as $cat)
                <option value="{{ $cat->id }}" {{ $producto->categoria_id == $cat->id ? 'selected' : '' }}>
                    {{ $cat->nombre }}
                </option>
            @endforeach
        </select>
    </div>
<div class="form-group">
    <label for="codigo">Código de barras</label>
    <input type="text" name="codigo" id="codigo" class="form-control form-control-sm" value="{{ old('codigo', $producto->codigo ?? '') }}">
</div>

    <div class="mb-3">
        <label>Precio Compra</label>
        <input type="number" name="precio_compra" step="0.01" class="form-control form-control-sm" value="{{ old('precio_compra', $producto->precio_compra) }}">
    </div>

    <div class="mb-3">
        <label>Precio Venta</label>
        <input type="number" name="precio_venta" step="0.01" class="form-control form-control-sm" value="{{ old('precio_venta', $producto->precio_venta) }}">
    </div>

    <div class="mb-3">
        <label>Stock</label>
        <input type="number" name="stock" class="form-control form-control-sm" value="{{ old('stock', $producto->stock) }}">
    </div>

    <div class="mb-3">
        <label>Imagen</label>
        @if($producto->imagen)
            <div class="mb-2">
                <img src="{{ asset('storage/'.$producto->imagen) }}" alt="Imagen" width="80">
            </div>
        @endif
        <input type="file" name="imagen" class="form-control form-control-sm">
    </div>

    <button class="btn btn-success">Actualizar</button>
    <a href="{{ route('productos.index') }}" class="btn btn-secondary">Cancelar</a>
</form>
@endsection
