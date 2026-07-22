<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use Maatwebsite\Excel\Facades\Excel;

class ProductoImportController extends Controller
{
    public function showUploadForm()
    {
        return view('productos.import_upload');
    }

    public function preview(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv,txt'
        ]);

        // Guardar temporalmente
        $path = $request->file('file')->store('temp');
        $fullPath = storage_path('app/' . $path);

        // Leer primera hoja
        $data = Excel::toArray([], $fullPath);

        // Encabezados (primera fila)
        $headers = $data[0][0] ?? [];

        return view('productos.import_map', [
            'headers' => $headers,
            'filePath' => $path
        ]);
    }

    public function importMapped(Request $request)
    {
        $request->validate([
            'file_path' => 'required',
            'mapping' => 'required|array'
        ]);

        $fullPath = storage_path('app/' . $request->file_path);
        $data = Excel::toArray([], $fullPath);

        $headers = $data[0][0] ?? [];

        foreach ($data[0] as $index => $row) {
            if ($index === 0) continue; // Saltar encabezado

            $productoData = [];
            foreach ($request->mapping as $excelColumn => $dbField) {
                if (!empty($dbField)) {
                    $colIndex = array_search($excelColumn, $headers);
                    $productoData[$dbField] = $row[$colIndex] ?? null;
                }
            }

            Producto::create($productoData);
        }

        return redirect()->route('productos.import.form')
                         ->with('success', 'Productos importados correctamente.');
    }
}
