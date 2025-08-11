@extends('layouts.app')

@section('content')
<h2>Agregar Movimiento a Caja</h2>
<form action="{{ route('caja.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label>Descripción</label>
        <input type="text" name="descripcion" class="form-control form-control-sm" required>
    </div>
    <div class="mb-3">
        <label>Monto</label>
        <input type="number" name="monto" class="form-control form-control-sm" step="0.01" required>
    </div>
    <button class="btn btn-success">Guardar</button>
</form>
@endsection
