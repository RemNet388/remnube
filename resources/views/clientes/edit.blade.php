@extends('layouts.app')

@section('content')
<h2>Editar Cliente</h2>

@if($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
    </ul>
</div>
@endif

<form action="{{ route('clientes.update', $cliente) }}" method="POST">
    @csrf @method('PUT')
    <div class="mb-3">
        <label>Nombre</label>
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
    <button class="btn btn-success">Actualizar</button>
    <a href="{{ route('clientes.index') }}" class="btn btn-secondary">Cancelar</a>
</form>
@endsection
