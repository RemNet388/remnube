@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4 text-uppercase" style="font-family: 'Roboto', sans-serif; font-weight: 500;">
        ✏️ Editar Producto
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
            <form action="{{ route('productos.update', $producto) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Categoría arriba --}}
                <div class="mb-3">
                    <label class="form-label small">Categoría</label>
                    <select name="categoria_id" class="form-control form-control-sm rounded-0" required>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}" {{ $producto->categoria_id == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Nombre + Código en la misma fila --}}
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label small">Nombre</label>
                        <input type="text" 
                               name="nombre" 
                               value="{{ old('nombre', $producto->nombre) }}" 
                               class="form-control form-control-sm rounded-0" 
                               required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label small">Código de barras</label>
                        <input type="text" 
                               name="codigo" 
                               value="{{ old('codigo', $producto->codigo ?? '') }}" 
                               class="form-control form-control-sm rounded-0">
                    </div>
                </div>

                {{-- Precios + Stock dentro de un recuadro --}}
                <div class="border p-3 mb-3 rounded-0">
                    <h6 class="text-uppercase small text-muted mb-3">Precios y Stock</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label small">Precio Compra</label>
                            <input type="number" 
                                   name="precio_compra" 
                                   step="0.01" 
                                   value="{{ old('precio_compra', $producto->precio_compra) }}" 
                                   class="form-control form-control-sm rounded-0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label small">Precio Venta</label>
                            <input type="number" 
                                   name="precio_venta" 
                                   step="0.01" 
                                   value="{{ old('precio_venta', $producto->precio_venta) }}" 
                                   class="form-control form-control-sm rounded-0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label small">Stock</label>
                            <input type="number" 
                                   name="stock" 
                                   value="{{ old('stock', $producto->stock) }}" 
                                   class="form-control form-control-sm rounded-0">
                        </div>
                        <div class="col-md-4 mb-3">
    <label for="fecha_vencimiento" class="form-label small">Fecha de vencimiento (opcional)</label>
    <input type="date" 
           class="form-control form-control-sm rounded-0" 
           id="fecha_vencimiento" 
           name="fecha_vencimiento" 
           value="{{ old('fecha_vencimiento', $producto->fecha_vencimiento?->format('Y-m-d')) }}">
</div>

                    </div>
                </div>

                {{-- Imagen --}}
                <div class="mb-3">
                    <label class="form-label small">Imagen</label>
                    @if($producto->imagen)
                        <div class="mb-2">
                            <img src="{{ asset('storage/'.$producto->imagen) }}" 
                                 alt="Imagen" 
                                 class="border rounded-0" 
                                 style="max-width:100px;">
                        </div>
                    @endif
                    <input type="file" name="imagen" class="form-control form-control-sm rounded-0">
                </div>
        {{-- Descripción --}}
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea name="descripcion" id="descripcion" class="form-control" rows="3">
    {{ old('descripcion', $producto->descripcion) }}
</textarea>
        </div>
                {{-- Botones --}}
                <div class="d-flex justify-content-end gap-2">
                    <button class="btn btn-success btn-sm rounded-0">💾 Actualizar</button>
                    <a href="{{ route('productos.index') }}" class="btn btn-secondary btn-sm rounded-0">↩ Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
