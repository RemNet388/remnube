@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>➕ Nuevo Gasto</h2>

    <form action="{{ route('gastos.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Descripción</label>
            <input type="text" name="descripcion" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Monto</label>
            <input type="number" step="0.01" name="monto" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Fecha</label>
            <input type="date" name="fecha" class="form-control" value="{{ now()->toDateString() }}" required>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" id="desdeRetiros" class="form-check-input" name="desde_retiros" value="1">
            <label for="desdeRetiros" class="form-check-label">💵 Pagar desde Retiros</label>
        </div>

        <div class="mb-3" id="formaPagoGroup">
            <label>Forma de Pago</label>
            <select name="forma_pago_id" class="form-control">
                <option value="">-- Seleccionar --</option>
                @foreach($formasPago as $fp)
                    <option value="{{ $fp->id }}">{{ $fp->nombre }}</option>
                @endforeach
            </select>
        </div>

        <input type="hidden" name="origen" id="origenInput" value="caja">

        <button type="submit" class="btn btn-success">💾 Guardar</button>
        <a href="{{ route('gastos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const checkRetiros = document.getElementById("desdeRetiros");
    const formaPagoGroup = document.getElementById("formaPagoGroup");
    const formaPagoSelect = formaPagoGroup.querySelector("select");
    const origenInput = document.getElementById("origenInput");

    function toggleFormaPago() {
        if (checkRetiros.checked) {
            formaPagoSelect.disabled = true;
            origenInput.value = "retiros";
        } else {
            formaPagoSelect.disabled = false;
            origenInput.value = "caja";
        }
    }

    checkRetiros.addEventListener("change", toggleFormaPago);
});
</script>
@endsection
