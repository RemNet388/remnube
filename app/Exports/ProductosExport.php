<?php

namespace App\Exports;

use App\Models\Producto;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductosExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Producto::select('nombre', 'stock', 'precio_compra', 'precio_venta', 'fecha_vencimiento')->get();
    }

    public function headings(): array
    {
        return [
            'Producto',
            'Stock',
            'Precio Compra',
            'Precio Venta',
            'Fecha de Vencimiento',
        ];
    }
}
