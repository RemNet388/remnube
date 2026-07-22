@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3>Facturación ARCA (AfipSDK - Modo Desarrollo)</h3>

    <form id="facturarForm">
        @csrf

        <div class="mb-3">
            <label>Descripción</label>
            <input type="text" name="descripcion" class="form-control" value="Venta de prueba">
        </div>

        <div class="mb-3">
            <label>Importe</label>
            <input type="number" name="importe" step="0.01" class="form-control" value="1500">
        </div>

        <button type="submit" class="btn btn-primary">Facturar</button>
    </form>

    <pre id="resultado" class="mt-4"></pre>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('facturarForm').addEventListener('submit', async e => {
    e.preventDefault();

    const form = new FormData(e.target);

    let res = await fetch('{{ route("facturar") }}', {
        method: 'POST',
        body: form
    });

    let json = await res.json();
    document.getElementById('resultado').textContent = JSON.stringify(json, null, 2);
});
</script>
@endsection
