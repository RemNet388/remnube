<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Stock</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 3px; text-align: center; }
        th { background-color: #eee; }
        .text-left { text-align: left; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <h3 style="text-align: center;">Informe de Stock</h3>

    @php $i = 0; @endphp
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Stock</th>
                <th>Precio Compra</th>
                <th>Precio Venta</th>
                <th>Fecha Vencimiento</th>
            </tr>
        </thead>
        <tbody>
        @foreach($productos as $p)
            <tr>
                <td class="text-left">{{ $p->nombre }}</td>
                <td>{{ $p->stock }}</td>
                <td>{{ number_format($p->precio_compra, 2) }}</td>
                <td>{{ number_format($p->precio_venta, 2) }}</td>
                <td>{{ $p->fecha_vencimiento ? $p->fecha_vencimiento->format('d/m/Y') : '' }}</td>
            </tr>
            @php $i++; @endphp
            @if($i % 40 == 0)
                </tbody>
            </table>
            <div class="page-break"></div>
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Stock</th>
                        <th>Precio Compra</th>
                        <th>Precio Venta</th>
                        <th>Fecha Vencimiento</th>
                    </tr>
                </thead>
                <tbody>
            @endif
        @endforeach
        </tbody>
    </table>
</body>
</html>
