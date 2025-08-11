@extends('layouts.app')

@section('content')
<h2>Nueva Forma de Pago</h2>

@if($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
    </ul>
</div>
@endif

<form action="{{ route('formas_pago.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label>Nombre</label>
        <input type="text" name="nombre" class="form-control form-control-sm" required>
    </div>
    <div class="mb-3">
        <label>Descripción</label>
        <textarea name="descripcion" class="form-control form-control-sm"></textarea>
    </div>
    <button class="btn btn-success">Guardar</button>
    <a href="{{ route('formas_pago.index') }}" class="btn btn-secondary">Cancelar</a>
</form>
@endsection
