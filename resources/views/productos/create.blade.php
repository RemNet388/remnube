@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4 text-uppercase" style="font-family: 'Roboto', sans-serif; font-weight: 500;">
        Nuevo Producto
    </h3>

    {{-- Errores --}}
    @if($errors->any())
        <div class="alert alert-danger small">
            <ul class="mb-0">
                @foreach($errors->all() as $error) 
                    <li>{{ $error }}</li> 
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-0">
        <div class="card-body">
            <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Categoría --}}
                <div class="mb-3">
                    <label class="form-label small">Categoría</label>
                    <select name="categoria_id" class="form-control form-control-sm rounded-0" required>
                        <option value="">-- Seleccione --</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Nombre + Código en la misma fila --}}
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label small">Nombre</label>
                        <input type="text" name="nombre" 
                               class="form-control form-control-sm rounded-0" 
                               value="{{ old('nombre') }}" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label small">Código de barras</label>
                        <input type="text" name="codigo" id="codigo" 
                               class="form-control form-control-sm rounded-0" 
                               value="{{ old('codigo', $producto->codigo ?? '') }}">
                    </div>
                </div>

                {{-- Sección precios + imagen --}}
                <fieldset class="border p-3 mb-3 rounded-0" style="border: 1px solid #ccc;">
                    <legend class="small px-2" style="font-size: 0.85rem; font-weight: 500;">Precios</legend>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small">Precio Compra</label>
                            <input type="number" name="precio_compra" step="0.01" 
                                   class="form-control form-control-sm rounded-0" 
                                   value="{{ old('precio_compra') }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label small">Precio Venta</label>
                            <input type="number" name="precio_venta" step="0.01" 
                                   class="form-control form-control-sm rounded-0" 
                                   value="{{ old('precio_venta') }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label small">Stock</label>
                            <input type="number" name="stock" 
                                   class="form-control form-control-sm rounded-0" 
                                   value="{{ old('stock') }}">
                        </div>
<div class="col-md-6 mb-3">
    <label for="fecha_vencimiento" class="form-label small">Fecha de vencimiento (opcional)</label>
    <input type="date" 
           class="form-control form-control-sm rounded-0" 
           id="fecha_vencimiento" 
           name="fecha_vencimiento" 
           value="{{ old('fecha_vencimiento') }}">
</div>


                        <div class="col-md-6 mb-3">
                            <label class="form-label small">Imagen</label>
                            <input type="file" name="imagen" class="form-control form-control-sm rounded-0">
                        </div>
        {{-- Descripción --}}
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea name="descripcion" id="descripcion" class="form-control" rows="3">{{ old('descripcion') }}</textarea>
        </div>                        
                    </div>
                </fieldset>

                {{-- Botones --}}
                <div class="d-flex justify-content-end gap-2">
                    <button class="btn btn-success btn-sm rounded-0">💾 Guardar</button>
                    <a href="{{ route('productos.index') }}" class="btn btn-secondary btn-sm rounded-0">↩ Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
