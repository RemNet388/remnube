@extends('layouts.app')

@section('content')
<div class="mb-2">
    <h2 class="text-uppercase fw-bold" style="font-family: 'Roboto', sans-serif; font-size: 14px;">Formas de Pago</h2>
</div>

@if(session('success'))
<div class="alert alert-success py-1 px-2" style="border-radius: 0; font-size: 13px;">
    {{ session('success') }}
</div>
@endif

<table class="table table-sm table-bordered table-striped align-middle" style="font-family: 'Roboto', sans-serif; font-size: 13px; border-radius: 0;">
    <thead class="table-light">
        <tr>
            <th>Nombre</th>
            <th>Descripción</th>
            <th style="width: 180px;">Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($formas_pago as $forma)
        <tr>
            <td>{{ $forma->nombre }}</td>
            <td>{{ $forma->descripcion }}</td>
            <td>
                @if($forma->id != 1 && $forma->id != 2)
                    <a href="{{ route('formas_pago.edit', ['forma_pago' => $forma->id]) }}" 
                       class="btn btn-warning btn-sm" style="border-radius: 0; font-size: 12px;">Editar</a>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection

@push('submenu')
<a href="{{ route('formas_pago.create') }}" 
   class="btn btn-primary btn-sm" style="border-radius: 0; font-size: 13px;">
   <i class="bi bi-plus"></i> Nueva Forma de Pago
</a>
<a href="{{ route('formas_pago.transferencias') }}" 
   class="btn btn-primary btn-sm" style="border-radius: 0; font-size: 13px;">
   <i class="bi bi-plus"></i> Transferir Entre Cuentas
</a>
@endpush
