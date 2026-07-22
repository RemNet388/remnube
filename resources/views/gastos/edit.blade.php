@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Editar Gasto</h2>

    {{-- Errores de validación --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('gastos.update', $gasto->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="fecha" class="form-label">Fecha</label>
            <input type="date" id="fecha" name="fecha" class="form-control"
                   value="{{ old('fecha', $gasto->fecha) }}" required>
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <input type="text" id="descripcion" name="descripcion" class="form-control"
                   value="{{ old('descripcion', $gasto->descripcion) }}" required>
        </div>

        <div class="mb-3">
            <label for="monto" class="form-label">Monto</label>
            <input type="number" step="0.01" id="monto" name="monto" class="form-control"
                   value="{{ old('monto', $gasto->monto) }}" required>
        </div>

        {{-- Toggle Retiro --}}
        <div class="form-check mb-3">
            <input type="checkbox" id="usar_retiro" name="usar_retiro" class="form-check-input"
                   {{ old('retiro_id', $gasto->retiro_id) ? 'checked' : '' }}>
            <label class="form-check-label" for="usar_retiro">
                Pagar desde Retiros
            </label>
        </div>

        {{-- Selector de Retiro --}}
        <div class="mb-3" id="retiro_group" style="display: {{ old('retiro_id', $gasto->retiro_id) ? 'block' : 'none' }}">
            <label for="retiro_id" class="form-label">Seleccione Retiro</label>
            <select id="retiro_id" name="retiro_id" class="form-select">
                <option value="">-- Seleccione --</option>
                @foreach($retiros as $retiro)
                    <option value="{{ $retiro->id }}"
                        {{ old('retiro_id', $gasto->retiro_id) == $retiro->id ? 'selected' : '' }}>
                        Retiro #{{ $retiro->id }} - {{ $retiro->fecha->format('d/m/Y') }} (Saldo: ${{ number_format($retiro->disponible, 2, ',', '.') }})
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Selector Forma de Pago --}}
        <div class="mb-3" id="forma_pago_group" style="display: {{ old('retiro_id', $gasto->retiro_id) ? 'none' : 'block' }}">
            <label for="forma_pago_id" class="form-label">Forma de Pago</label>
            <select id="forma_pago_id" name="forma_pago_id" class="form-select">
                @foreach($formasPago as $fp)
                    <option value="{{ $fp->id }}"
                        {{ old('forma_pago_id', $gasto->forma_pago_id) == $fp->id ? 'selected' : '' }}>
                        {{ $fp->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-success">Guardar Cambios</button>
        <a href="{{ route('gastos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const check = document.getElementById('usar_retiro');
        const retiroGroup = document.getElementById('retiro_group');
        const formaPagoGroup = document.getElementById('forma_pago_group');
        const retiroSelect = document.getElementById('retiro_id');
        const formaPagoSelect = document.getElementById('forma_pago_id');

        function toggleInputs() {
            if (check.checked) {
                retiroGroup.style.display = 'block';
                formaPagoGroup.style.display = 'none';
                formaPagoSelect.value = "";
            } else {
                retiroGroup.style.display = 'none';
                formaPagoGroup.style.display = 'block';
                retiroSelect.value = "";
            }
        }

        check.addEventListener('change', toggleInputs);
        toggleInputs();
    });
</script>
@endsection
