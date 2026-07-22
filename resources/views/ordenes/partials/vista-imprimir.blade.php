<style>
.titulo-impresion {
    font-size: 28pt;
    font-weight: bold;
    text-align: center;
    margin-bottom: 20px;
}
.copia {
    height: 48%;
    padding: 15px;
    box-sizing: border-box;
    border-bottom: 2px dashed #000;
    page-break-inside: avoid;
}
.header {
    display: flex;
    align-items: center;
    padding: 8px 10px;
    margin-bottom: 10px;
    background: #222;   /* fondo oscuro */
    color: #fff;        /* texto blanco */
    border-radius: 3px;
}
.header img {
    height: 70px;
    margin-right: 12px;
    background: #fff;   /* fondo blanco detrás del logo */
    padding: 4px;
    border-radius: 3px;
}
.header .datos-empresa {
    font-size: 12px;
    line-height: 1.4;
}
h2 {
    margin: 0;
    font-size: 16px;
}
.contenido {
    font-size: 12px;
}
.footer {
    margin-top: 10px;
    font-size: 10px;
    border-top: 1px solid #000;
    padding-top: 5px;
}
@media print {
    body {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
}
</style>


<div class="titulo-impresion">
    Orden de Servicio {{ $orden->numero_orden }}
</div>

@for ($i = 0; $i < 2; $i++)
<div class="copia">
<div class="header .datos-empresa">
    <img src="{{ asset('logo.png') }}" alt="Logo" style="height: 32px;">
    <div class="datos-empresa">
        <strong>{{ config('empresa.nombre') }}</strong>
        {{ config('empresa.direccion') }}<br>
        Tel: {{ config('empresa.telefono') }} | Email: {{ config('empresa.email') }}
    </div>
</div>


    <h2>Orden de Servicio Nº {{ $orden->numero_orden }}</h2>
    <div class="contenido">
        <p><strong>Cliente:</strong> {{ $orden->cliente->nombre }}</p>
        <p><strong>Marca:</strong> {{ $orden->marca->nombre }}</p>
        <p><strong>Modelo:</strong> {{ $orden->modelo->nombre }}</p>
        <p><strong>N° Serie:</strong> {{ $orden->nro_serie }}</p>
        <p><strong>Detalle de reparación:</strong> {{ $orden->detalle_reparacion }}</p>
        <p><strong>Observaciones:</strong> {{ $orden->observaciones }}</p>
        <p><strong>Presupuesto:</strong> ${{ number_format($orden->presupuesto, 2, ',', '.') }}</p>
        <p><strong>Estado:</strong> {{ ucfirst($orden->estado) }}</p>
    </div>

    <div class="footer">
        <strong>Condiciones del servicio técnico:</strong><br>
        - La empresa no se responsabiliza por la pérdida de datos.<br>
        - Los equipos no retirados en 30 días se considerarán abandonados.<br>
        - Los plazos de entrega pueden variar según disponibilidad de repuestos.<br>
    </div>
</div>
@endfor
