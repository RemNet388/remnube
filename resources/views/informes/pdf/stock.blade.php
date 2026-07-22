<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Informe de Stock</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; margin: 5px; }
        h2 { font-size: 12px; font-weight: bold; margin-bottom: 5px; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        th, td { border: 1px solid #000; padding: 2px 4px; text-align: left; font-size: 9px; }
        th { background-color: #f0f0f0; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>

<h2>Informe de Stock</h2>

@foreach($productos as $bloque)
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Stock</th>
                <th>Precio Compra</th>
                <th>Precio Venta</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bloque as $prod)
                <tr>
                    <td>{{ $prod->nombre }}</td>
                    <td>{{ $prod->stock }}</td>
                    <td>${{ number_format($prod->precio_compra, 2) }}</td>
                    <td>${{ number_format($prod->precio_venta, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="page-break"></div>
@endforeach

</body>
</html>
