@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-3">🧾 Informe de Compras por Proveedor</h3>

    {{-- Filtros --}}
    <form class="row g-2 mb-4">
        <div class="col-md-3">
<input type="date" name="desde" class="form-control"
       value="{{ $desde->format('Y-m-d') }}">
        </div>
        <div class="col-md-3">
<input type="date" name="hasta" class="form-control"
       value="{{ $hasta->format('Y-m-d') }}">
        </div>
        <div class="col-md-3">
            <button class="btn btn-primary">Filtrar</button>
        </div>
    </form>

    <div class="card mb-3">
        <div class="card-body">
            <strong>Total General Comprado:</strong>
            ${{ number_format($totalGeneral,2) }}
        </div>
    </div>

    <table class="table table-bordered table-striped align-middle">
        <thead class="table-light">
            <tr>
                <th>Proveedor</th>
                <th>Cantidad de Compras</th>
                <th>Total Comprado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($comprasPorProveedor as $p)
                <tr>
                    <td>{{ $p['proveedor'] }}</td>
                    <td>{{ $p['cantidad'] }}</td>
                    <td>${{ number_format($p['total'],2) }}</td>
                </tr>
            @endforeach

            @if($comprasPorProveedor->isEmpty())
                <tr>
                    <td colspan="3" class="text-center">
                        No hay compras en el período
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
@endsection
 @push('submenu')
<a href="{{ route('compras.create') }}" class="btn btn-primary btn-sm" style="border-radius: 0; font-size: 13px;">
    <i class="bi bi-plus"></i> Nueva Compra
</a>
<a href="{{ route('informes.compras_proveedor') }}" class="btn btn-primary btn-sm" style="border-radius: 0; font-size: 13px;">
    <i class="bi bi-plus"></i> Informe de Compras
</a>
        <span style="display: inline-block; width: 20px;"></span>
@endpush 