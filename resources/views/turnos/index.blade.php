@extends('layouts.app')

@section('content')
<div class="container">

    <h3 class="mb-3">Turnos</h3>

<!-- Selector de fecha con botones de anterior y siguiente -->
<form id="form-fecha-turnos" method="get" action="{{ route('turnos.index') }}" class="mb-3 d-flex gap-2 align-items-center">
    <button type="button" id="btn-anterior" class="btn btn-outline-primary btn-sm">&laquo;</button>
    <input type="date" name="fecha" id="input-fecha" value="{{ $fecha }}" class="form-control w-auto rounded-0 form-control-sm">
    <button type="button" id="btn-siguiente" class="btn btn-outline-primary btn-sm">&raquo;</button>
</form>

    @if(session('success'))
        <div class="alert alert-success rounded-0 py-1 small">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger rounded-0 py-1 small">{{ session('error') }}</div>
    @endif

<!-- Turnos -->
<div class="list-group">
    @foreach($turnos as $item)
@php
    $t = $item['turno'];
    $orden = $t?->orden;
    $horaTurno = \Carbon\Carbon::parse($fecha . ' ' . $item['hora']);
    $turnoPasado = $horaTurno->isPast();
@endphp

<div class="list-group-item d-flex justify-content-between align-items-center border rounded-0 mb-1 py-1 px-2
            @if($orden) bg-danger text-white
            @elseif($t) bg-success bg-opacity-25
            @else bg-light
            @endif"
     style="font-size: 0.85rem;">

    <!-- Hora -->
    <div style="width: 60px; font-weight: 500;">
        {{ substr($item['hora'],0,5) }}
    </div>

    <!-- Cliente / Nota -->
    <div class="flex-grow-1 ms-2">
        @if($t)
            <span class="fw-semibold text-dark" role="button"
                  data-bs-toggle="modal" data-bs-target="#modalEditarTurno"
                  data-turno-id="{{ $t->id }}"
                  data-fecha="{{ $t->fecha }}"
                  data-hora="{{ $t->hora_inicio }}">
                {{ $t->cliente->nombre ?? 'Sin nombre' }}
            </span>
            @if($t->nota)
                <div class="small fst-italic text-muted">{{ $t->nota }}</div>
            @endif
        @else
            <span class="text-muted">Libre</span>
        @endif
    </div>

    <!-- Botones acción -->
    <div class="ms-2 d-flex gap-1 align-items-center">
        @if($t)
            @if($orden)
                <a href="{{ route('ordenes.edit', $orden->id) }}"
                   class="btn btn-light btn-sm rounded-0"
                   style="width: 90px; font-size: 0.75rem;">
                    Ver OT
                </a>
            @else
                <a href="{{ route('ordenes.create', ['turno_id' => $t->id, 'cliente_id' => $t->cliente_id]) }}"
                   class="btn btn-success btn-sm rounded-0"
                   style="width: 90px; font-size: 0.75rem;">
                    Generar OT
                </a>
            @endif

            <!-- Eliminar turno -->
            <form action="{{ route('turnos.destroy', $t->id) }}" method="POST" onsubmit="return confirm('¿Eliminar turno?')" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm rounded-0" title="Eliminar" style="padding: 2px 5px;">
                    🗑
                </button>
            </form>

            <!-- Notificar WhatsApp -->
            @if(!$turnoPasado)
                @php
                    $telefono = preg_replace('/[^0-9]/', '', $t->cliente->telefono ?? '');
                    $nombreCliente = $t->cliente->nombre ?? 'Cliente';
                    $negocio = 'Gomeria AG';
                    $horaTurnoStr = substr($item['hora'], 0, 5);
                    $mensaje = "Hola $nombreCliente, de $negocio te informamos que tu turno está programado para el $fecha a las $horaTurnoStr. No lo olvides. Te esperamos!";
                    $linkWhatsapp = 'https://wa.me/' . $telefono . '?text=' . urlencode($mensaje);
                @endphp
                <a href="{{ $linkWhatsapp }}" target="_blank"
                   class="btn btn-sm rounded-0"
                   style="width: 30px; background-color:#25D366; font-size: 0.75rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor"
                         class="bi bi-whatsapp" viewBox="0 0 16 16">
                        <path d="M13.601 2.326A7.92 7.92 0 0 0 8.033 0C3.6 0 .016 3.59.016 8.018c0 1.414.37 2.725 1.014 3.905L0 16l4.183-1.098A7.964 7.964 0 0 0 8.033 16c4.432 0 8.016-3.59 8.016-8.018 0-2.144-.828-4.152-2.448-5.656zM8.03 14.007a5.96 5.96 0 0 1-3.205-.936l-.228-.14-2.355.617.627-2.286-.155-.25a5.97 5.97 0 0 1-1.019-3.296c0-3.303 2.69-5.99 6.002-5.99a5.99 5.99 0 0 1 4.252 1.763A5.99 5.99 0 0 1 14.03 8.02c0 3.303-2.69 5.99-6.002 5.99z"/>
                    </svg>
                </a>
            @endif
        @else
            <button class="btn btn-primary btn-sm rounded-0" style="width: 120px; font-size:0.75rem;"
                    data-bs-toggle="modal" data-bs-target="#modalReservar"
                    data-hora="{{ $item['hora'] }}">
                Reservar
            </button>
        @endif
    </div>
</div>
@endforeach
@include('turnos.modal-editar')
</div>

</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-fecha-turnos');
    const inputFecha = document.getElementById('input-fecha');
    const btnAnterior = document.getElementById('btn-anterior');
    const btnSiguiente = document.getElementById('btn-siguiente');

    // Enviar el formulario automáticamente al cambiar la fecha
    inputFecha.addEventListener('change', () => {
        form.submit();
    });

    // Función para sumar/restar días
    function cambiarFecha(dias) {
        const fecha = new Date(inputFecha.value);
        fecha.setDate(fecha.getDate() + dias);
        inputFecha.value = fecha.toISOString().slice(0,10); // yyyy-mm-dd
        form.submit();
    }

    btnAnterior.addEventListener('click', () => cambiarFecha(-1));
    btnSiguiente.addEventListener('click', () => cambiarFecha(1));
});
</script>
<!-- Modal Reservar -->
@include('turnos.modal-reservar')

@endsection
