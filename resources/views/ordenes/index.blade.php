@extends('layouts.app')

@section('content')
<div class="container">
<div class="mb-3">
    <h2 class="text-uppercase fw-bold" style="font-family: 'Roboto', sans-serif; font-size: 16px;">Ordenes de Servicio</h2>
</div>
            {{-- Mensaje de éxito --}}
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif


            {{-- Tabla --}}
            <div class="table-responsive">
<table class="table table-sm table-bordered table-striped align-middle" style="font-family: 'Roboto', sans-serif; font-size: 13px;">
    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Marca</th>
                            <th>Modelo</th>
                            <th>Ident.</th>
                            <th>Estado</th>
                            <th>Presupuesto</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @forelse($ordenes as $orden)
                            <tr>
                                <td>{{ $orden->numero }}</td>
                                <td>{{ $orden->cliente->nombre ?? '-' }}</td>
                                <td>{{ $orden->marca->nombre ?? '-' }}</td>
                                <td>{{ $orden->modelo->nombre ?? '-' }}</td>
                                <td>{{ $orden->identificador ?? '-' }}</td>
                                <td>
                                    <span class="badge 
                                        @if($orden->estado === 'pendiente') bg-warning
                                        @elseif($orden->estado === 'en_progreso') bg-primary
                                        @elseif($orden->estado === 'finalizada') bg-success
                                        @else bg-secondary
                                        @endif">
                                        {{ ucfirst(str_replace('_',' ',$orden->estado)) }}
                                    </span>
                                </td>
                                <td>${{ number_format((float) $orden->presupuesto ?? 0, 2) }}</td>
                                <td class="d-flex justify-content-center gap-1">

                                    <a href="{{ route('ordenes.edit', $orden->id) }}" class="btn btn-warning btn-sm rounded-0">
                                        <i class="fa fa-edit"></i> Editar
                                    </a>

                                    <button class="btn btn-secondary btn-sm rounded-0" onclick="abrirModalImprimir({{ $orden->id }})">
    <i class="fa fa-print"></i> Imprimir
</button>


                                    <form action="{{ route('ordenes.destroy', $orden->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar esta orden?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm rounded-0">
                                            <i class="fa fa-trash"></i> Eliminar
                                        </button>
                                    </form>

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-muted">No hay órdenes registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>



        </div>
    </div>
</div>

<!-- Modal de impresión -->
<div class="modal fade" id="modalImprimir" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Vista de impresión</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body" id="contenidoOrden">
        <!-- Aquí se cargará la vista parcial vía AJAX -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" onclick="imprimirModal()">Imprimir</button>
      </div>
    </div>
  </div>
</div>

<script>
function imprimirModal() {
    var contenido = document.getElementById('contenidoOrden').innerHTML;
    var ventana = window.open('', '_blank', 'width=800,height=600');
    ventana.document.write(contenido);
    ventana.document.close();
    ventana.focus();
    ventana.print();
}
</script>


@endsection

@section('scripts')
<script>
function cargarOrden(id) {
    $.ajax({
        url: '/ordenes/' + id + '/vista-imprimir', // ruta que devuelve la orden en HTML
        method: 'GET',
        success: function(data) {
            $('#contenidoOrden').html(data);
        }
    });
}

function imprimirContenido(idDiv) {
    var contenido = document.getElementById(idDiv).innerHTML;
    var ventana = window.open('', '', 'width=900,height=600');
    ventana.document.write('<html><head><title>Imprimir</title></head><body>' + contenido + '</body></html>');
    ventana.document.close();
    ventana.print();
}
</script>
<script>
function abrirModalImprimir(id) {
    // Limpiar contenido
    document.getElementById('contenidoOrden').innerHTML = 'Cargando...';

    // Abrir modal
    var modal = new bootstrap.Modal(document.getElementById('modalImprimir'));
    modal.show();

    // AJAX para cargar la vista parcial
    fetch('/ordenes/' + id + '/vista-imprimir')
        .then(response => response.text())
        .then(html => {
            document.getElementById('contenidoOrden').innerHTML = html;
        })
        .catch(err => {
            document.getElementById('contenidoOrden').innerHTML = 'Error al cargar la vista';
            console.error(err);
        });
}
</script>

@endsection
@push('submenu')
            {{-- Buscador --}}
<form action="{{ route('ordenes.index') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2">
    <input type="text" name="search" value="{{ $search }}" 
           class="form-control form-control-sm" placeholder="Buscar por cliente o número" 
           style="border-radius: 0; width: 200px; height: calc(1.5em + .5rem + 2px);">
    <button type="submit" class="btn btn-secondary btn-sm" style="border-radius: 0;">
        <i class="bi bi-search"></i>
    </button>
</form>      

  
<a href="{{ route('ordenes.create') }}" class="btn btn-outline-secondary btn-sm" style="border-radius: 0;"><i class="bi bi-plus"></i>
@if(config('custom.plan') >= 3)
    Nueva Orden (Si es con turno no usar esto)
@endif
</a>
<a href="{{ route('marcas.index') }}" class="btn btn-outline-secondary btn-sm" style="border-radius: 0;"> Marcas</a>
<a href="{{ route('modelos.index') }}" class="btn btn-outline-secondary btn-sm" style="border-radius: 0;"> Modelos</a>            

{{-- Paginación --}}
            <span style="display: inline-block; width: 120px;"></span>
            <div class="d-flex justify-content-center mt-3">
                {{ $ordenes->links() }}
            </div>
@endpush