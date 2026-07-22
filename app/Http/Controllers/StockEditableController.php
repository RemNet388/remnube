<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;

// Si usas DomPDF
use Barryvdh\DomPDF\Facade\Pdf;

// Para Excel
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductosExport;

class StockEditableController extends Controller
{
    public function imprimir()
    {
        $pdf = Pdf::loadView('informes.stock_imprimir', [
            // traer todos en chunks para no saturar memoria
            'productos' => Producto::orderBy('nombre')->get()
        ])->setPaper('a4', 'landscape'); // orientación horizontal para más columnas

        return $pdf->stream('stock.pdf');
    }

    public function exportarExcel()
    {
        return Excel::download(new ProductosExport, 'stock.xlsx');
    }
}
