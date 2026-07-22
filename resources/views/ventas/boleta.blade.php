<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Boleta #{{ $venta->id }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .boleta {
            width: 100%;
            margin: 0 auto;
        }
        .header {
            display: flex;
            align-items: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header img {
            height: 80px;
            margin-right: 15px;
        }
        .header .datos-empresa {
            font-size: 14px;
        }
        .header .datos-empresa strong {
            font-size: 18px;
            display: block;
        }
        h2 {
            text-align: center;
            margin: 20px 0 10px 0;
            font-size: 18px;
        }
        .info {
            margin-bottom: 15px;
        }
        .info p {
            margin: 3px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .text-right {
            text-align: right;
        }
        .total {
            font-weight: bold;
            font-size: 16px;
            text-align: right;
            margin-top: 5px;
        }
        .footer {
            margin-top: 20px;
            font-size: 10px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="boleta">
<div class="header .datos-empresa">
    <img src="{{ asset('logo.png') }}" alt="Logo" style="height: 32px;">
    <div class="datos-empresa">
        <strong>{{ config('empresa.nombre') }}</strong>
        {{ config('empresa.direccion') }}<br>
        Tel: {{ config('empresa.telefono') }} | Email: {{ config('empresa.email') }}
    </div>
</div>

        <h2>Boleta de Venta #{{ $venta->id }}</h2>

        <div class="info">
            <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i') }}</p>
            <p><strong>Cliente:</strong> {{ $venta->cliente->nombre ?? 'Consumidor Final' }}</p>
            <p><strong>Forma de pago:</strong> {{ $venta->formaPago->nombre ?? '-' }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($venta->detalles as $detalle)
                <tr>
                    <td>{{ $detalle->producto->nombre }}</td>
                    <td class="text-right">{{ $detalle->cantidad }}</td>
                    <td class="text-right">${{ number_format($detalle->precio_unitario, 2, ',', '.') }}</td>
                    <td class="text-right">${{ number_format($detalle->subtotal, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <p class="total">TOTAL: ${{ number_format($venta->total, 2, ',', '.') }}</p>

        <div class="footer">
            Gracias por su compra.<br>
            Todos los precios incluyen IVA. Condiciones de venta sujetas a nuestra política.
        </div>
    </div>
</body>
</html>
