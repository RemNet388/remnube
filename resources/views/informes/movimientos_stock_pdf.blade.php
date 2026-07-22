<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Informe de Movimientos de Stock</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h2, h3 { margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table, th, td { border: 1px solid #000; }
        th, td { padding: 5px; text-align: left; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <h2>📦 Informe de Movimientos de Stock</h2>
    <p>Desde: {{ $desde }} | Hasta: {{ $hasta }}</p>

    {{-- Top 5 productos más vendidos --}}
    <h3>📊 Top 5 productos más vendidos</h3>
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Unidades vendidas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($masVendidos as $item)
                <tr>
                    <td>{{ $item->producto->nombre ?? '—' }}</td>
                    <td>{{ $item->total }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Evolución mensual --}}
    <h3>📈 Evolución mensual de ventas</h3>
    <table>
        <thead>
            <tr>
                <th>Mes</th>
                <th>Total unidades vendidas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ventasMensuales as $item)
                <tr>
                    <td>{{ $item->mes }}</td>
                    <td>{{ $item->total }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Distribución por categoría --}}
    <h3>🥧 Distribución por categoría</h3>
    <table>
        <thead>
            <tr>
                <th>Categoría</th>
                <th>Total unidades vendidas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ventasPorCategoria as $item)
                <tr>
                    <td>{{ $item['categoria'] }}</td>
                    <td>{{ $item['total'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
