@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4 text-uppercase">Editar Sección</h3>

    @if($errors->any())
        <div class="alert alert-danger small">
            <ul class="mb-0">
                @foreach($errors->all() as $error) 
                    <li>{{ $error }}</li> 
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('secciones.update', $seccion) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label small">Slug</label>
            <input type="text" name="slug" value="{{ old('slug', $seccion->slug) }}" class="form-control form-control-sm" required>
        </div>

        <div class="mb-3">
            <label class="form-label small">Título</label>
            <input type="text" name="titulo" value="{{ old('titulo', $seccion->titulo) }}" class="form-control form-control-sm" required>
        </div>

        <div class="mb-3">
            <label class="form-label small">Contenido</label>
            <textarea name="contenido" class="form-control" rows="5">{{ old('contenido', $seccion->contenido) }}</textarea>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" name="activo" value="1" class="form-check-input" {{ old('activo', $seccion->activo) ? 'checked' : '' }}>
            <label class="form-check-label">Activo</label>
        </div>

        <div class="mb-3">
            <label class="form-label small">Orden</label>
            <input type="number" name="orden" value="{{ old('orden', $seccion->orden) }}" class="form-control form-control-sm">
        </div>

        <button class="btn btn-success btn-sm">💾 Actualizar</button>
        <a href="{{ route('secciones.index') }}" class="btn btn-secondary btn-sm">↩ Cancelar</a>
    </form>
</div>
@endsection
