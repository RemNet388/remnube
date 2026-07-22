@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-uppercase fw-bold mb-4" style="font-family: 'Roboto', sans-serif; font-size: 16px;">
        Editar Cliente
    </h2>

    @if($errors->any())
    <div class="alert alert-danger" style="font-family: 'Roboto', sans-serif; font-size: 13px;">
        <ul class="mb-0">
            @foreach($errors->all() as $error) 
                <li>{{ $error }}</li> 
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('clientes.update', $cliente) }}" method="POST" style="font-family: 'Roboto', sans-serif; font-size: 13px;">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Nombre *</label>
            <input type="text" name="nombre" value="{{ old('nombre', $cliente->nombre) }}" class="form-control form-control-sm" required>
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email', $cliente->email) }}" class="form-control form-control-sm">
        </div>

        <div class="mb-3">
            <label>Teléfono</label>
            <input type="text" name="telefono" value="{{ old('telefono', $cliente->telefono) }}" class="form-control form-control-sm">
        </div>

        <div class="mb-3">
            <label>Dirección</label>
            <input type="text" name="direccion" value="{{ old('direccion', $cliente->direccion) }}" class="form-control form-control-sm">
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success btn-sm" style="border-radius: 0; font-family: 'Roboto', sans-serif;">
                <i class="fa fa-save me-1"></i> Actualizar
            </button>
            <a href="{{ route('clientes.index') }}" class="btn btn-secondary btn-sm" style="border-radius: 0; font-family: 'Roboto', sans-serif;">
                <i class="fa fa-times me-1"></i> Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
