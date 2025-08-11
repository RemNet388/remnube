@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between mb-3 align-items-center">
    <div class="d-flex align-items-center">
        <h2 class="me-3 mb-0">Productos</h2>

        <form id="filtroForm" action="{{ route('productos.index') }}" method="GET" class="d-flex">
            <input 
                type="text" 
                name="buscar" 
                class="form-control form-control-sm me-2" 
                placeholder="Buscar producto..."
                value="{{ request('buscar') }}"
            >

            <select name="categoria_id" class="form-control form-control-sm me-2" onchange="document.getElementById('filtroForm').submit();">
                <option value="todas">Todas las categorías</option>
                @foreach($categorias as $categoria)
                    <option value="{{ $categoria->id }}" {{ request('categoria_id') == $categoria->id ? 'selected' : '' }}>
                        {{ $categoria->nombre }}
                    </option>
                @endforeach
            </select>

            <button class="btn btn-primary btn-sm" type="submit">
                <i class="bi bi-search"></i>
            </button>
        </form>
    </div>

    <a href="{{ route('productos.create') }}" class="btn btn-primary">
        Nuevo Producto
    </a>
</div>



@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-sm table-bordered table-striped align-middle">
    <thead class="table-light">
        <tr>
            <th>Imagen</th>
            <th>Nombre</th>
            <th>Categoría</th>
            <th>Precio Compra</th>
            <th>Precio Venta</th>
            <th>Stock</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($productos as $producto)
        <tr>
            <td>
                @if($producto->imagen)
                    <button type="button" 
                        class="btn btn-primary btn-sm" 
                        data-bs-toggle="modal" 
                        data-bs-target="#modalImagen{{ $producto->id }}">
                        <i class="bi bi-eye"></i> Ver
                    </button>

                    <!-- Modal -->
                    <div class="modal fade" id="modalImagen{{ $producto->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">{{ $producto->nombre }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <img src="{{ asset('storage/'.$producto->imagen) }}" class="img-fluid rounded" alt="Imagen">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <span class="text-muted">Sin imagen</span>
                @endif
            </td>
            <td>{{ $producto->nombre }}</td>
            <td>{{ $producto->categoria->nombre ?? 'Sin categoría' }}</td>
            <td>{{ $producto->precio_compra }}</td>
            <td>{{ $producto->precio_venta }}</td>
            <td>{{ $producto->stock }}</td>
            <td>
                <a href="{{ route('productos.edit', $producto) }}" class="btn btn-warning btn-sm">Editar</a>
                <form action="{{ route('productos.destroy', $producto) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar producto?')">Eliminar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection
