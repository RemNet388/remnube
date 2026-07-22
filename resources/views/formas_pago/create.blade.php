@extends('layouts.app')

@section('content')
<div class="container" style="font-family: 'Roboto', sans-serif; font-size: 0.85rem;">
    <h2 class="mb-3 text-uppercase fw-bold">➕ Nueva Forma de Pago</h2>

    @if($errors->any())
        <div class="alert alert-danger rounded-0 small">
            <ul class="mb-0">
                @foreach($errors->all() as $error) 
                    <li>{{ $error }}</li> 
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm rounded-0">
        <div class="card-body">
            <form action="{{ route('formas_pago.store') }}" method="POST" class="row g-3">
                @csrf
                <div class="col-12">
                    <label class="form-label fw-bold text-uppercase small mb-1">Nombre</label>
                    <input type="text" name="nombre" 
                           class="form-control form-control-sm rounded-0" required>
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold text-uppercase small mb-1">Descripción</label>
                    <textarea name="descripcion" 
                              class="form-control form-control-sm rounded-0"></textarea>
                </div>

                <div class="col-12 d-flex gap-2">
                    <button class="btn btn-success btn-sm rounded-0">
                        💾 Guardar
                    </button>
                    <a href="{{ route('formas_pago.index') }}" 
                       class="btn btn-secondary btn-sm rounded-0">
                        ❌ Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
