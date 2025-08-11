@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Proveedor</h1>

    <form action="{{ route('proveedores.update', $proveedor) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre *</label>
            <input type="text" class="form-control form-control-sm" id="nombre" name="nombre" value="{{ old('nombre', $proveedor->nombre) }}" required>
            @error('nombre') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label for="direccion" class="form-label">Dirección</label>
            <input type="text" class="form-control form-control-sm" id="direccion" name="direccion" value="{{ old('direccion', $proveedor->direccion) }}">
        </div>

        <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="text" class="form-control form-control-sm" id="telefono" name="telefono" value="{{ old('telefono', $proveedor->telefono) }}">
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control form-control-sm" id="email" name="email" value="{{ old('email', $proveedor->email) }}">
            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <button type="submit" class="btn btn-success">Actualizar</button>
        <a href="{{ route('proveedores.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
