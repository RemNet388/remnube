@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-uppercase fw-bold mb-4" style="font-family: 'Roboto', sans-serif; font-size: 16px;">
        Editar Proveedor
    </h2>

    <form action="{{ route('proveedores.update', $proveedor) }}" method="POST" style="font-family: 'Roboto', sans-serif; font-size: 13px;">
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

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success btn-sm" style="border-radius: 0; font-family: 'Roboto', sans-serif;">
                <i class="fa fa-save me-1"></i> Actualizar
            </button>
            <a href="{{ route('proveedores.index') }}" class="btn btn-secondary btn-sm" style="border-radius: 0; font-family: 'Roboto', sans-serif;">
                <i class="fa fa-times me-1"></i> Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
