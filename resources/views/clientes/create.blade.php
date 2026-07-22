@extends('layouts.app')

@section('content')
<div class="container mt-3">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="mb-3 text-muted">➕ Nuevo Cliente</h5>
            
            <form method="POST" action="{{ route('clientes.store') }}">
                @csrf
                <div class="mb-2">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control form-control-sm" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">DNI</label>
                    <input type="text" name="dni" class="form-control form-control-sm">
                </div>
                <div class="mb-2">
                    <label class="form-label">Dirección</label>
                    <input type="text" name="direccion" class="form-control form-control-sm">
                </div>
                <div class="mb-2">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control form-control-sm">
                </div>
                <div class="mb-2">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control form-control-sm">
                </div>

                <button type="submit" class="btn btn-primary btn-sm">💾 Guardar</button>
                <a href="{{ route('clientes.index') }}" class="btn btn-secondary btn-sm">↩ Volver</a>
            </form>
        </div>
    </div>
</div>
@endsection
