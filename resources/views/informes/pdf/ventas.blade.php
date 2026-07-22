<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Informe de Ventas</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background: #f0f0f0; }
    </style>
</head>
<body>
    <h2>💰 Informe de Ventas</h2>
    <p><strong>Desde:</strong> {{ $desde }} - <strong>Hasta:</strong> {{ $hasta }}</p>
    <p><strong>Total:</strong> ${{ number_format($total, 2) }}</p>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Total</th>
                <th>Forma de Pago</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ventas as $venta)
            <tr>
                <td>{{ $venta->fecha }}</td>
                <td>${{ number_format($venta->total, 2) }}</td>
                <td>{{ $venta->formaPago->nombre ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
