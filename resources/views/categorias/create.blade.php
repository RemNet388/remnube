@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4 text-uppercase" style="font-family: 'Roboto', sans-serif; font-weight: 500;">
        ➕ Nueva Categoría
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
            <form action="{{ route('categorias.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label small">Nombre</label>
                    <input type="text" 
                           name="nombre" 
                           class="form-control form-control-sm rounded-0" 
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label small">Descripción</label>
                    <textarea name="descripcion" 
                              class="form-control form-control-sm rounded-0" 
                              rows="3"></textarea>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button class="btn btn-success btn-sm rounded-0">💾 Guardar</button>
                    <a href="{{ route('categorias.index') }}" class="btn btn-secondary btn-sm rounded-0">↩ Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
