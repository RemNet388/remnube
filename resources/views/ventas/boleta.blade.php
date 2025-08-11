<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Boleta #{{ $venta->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, th, td { border: 1px solid #000; }
        th, td { padding: 5px; text-align: left; }
        .center { text-align: center; }
    </style>
</head>
<body>
    <h2 class="center">Boleta de Venta</h2>
    <p><strong>Venta #:</strong> {{ $venta->id }}</p>
    <p><strong>Fecha:</strong> {{ $venta->fecha }}</p>
    <p><strong>Cliente:</strong> {{ $venta->cliente->nombre ?? 'Consumidor Final' }}</p>
    <p><strong>Forma de pago:</strong> {{ $venta->formaPago->nombre ?? '-' }}</p>

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
                    <td>{{ $detalle->cantidad }}</td>
                    <td>${{ number_format($detalle->precio_unitario, 2) }}</td>
                    <td>${{ number_format($detalle->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3 class="center">TOTAL: ${{ number_format($venta->total, 2) }}</h3>
</body>
</html>
