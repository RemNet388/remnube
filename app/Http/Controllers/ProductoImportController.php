<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Categoria;
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

        if (!file_exists(public_path('imports'))) {
            mkdir(public_path('imports'), 0775, true);
        }

        $filename = time() . '_' . $request->file('file')->getClientOriginalName();
        $request->file('file')->move(public_path('imports'), $filename);

        $fullPath = public_path('imports/' . $filename);
        $data = Excel::toArray([], $fullPath);
        $headers = $data[0][0] ?? [];

        return view('productos.import_map', [
            'headers' => $headers,
            'filePath' => $filename
        ]);
    }

    public function importMapped(Request $request)
    {
        $request->validate([
            'file_path' => 'required',
            'mapping' => 'required|array'
        ]);

        // Asegurar que se mapeó "nombre"
        if (!in_array('nombre', array_values($request->mapping))) {
            return back()->withErrors(['mapping' => 'Debes mapear la columna "nombre" antes de importar.']);
        }

        $fullPath = public_path('imports/' . $request->file_path);

        if (!file_exists($fullPath)) {
            return back()->withErrors(['file' => 'El archivo no se encontró. Súbelo nuevamente.']);
        }

        $data = Excel::toArray([], $fullPath);
        $headers = $data[0][0] ?? [];

        foreach ($data[0] as $index => $row) {
            if ($index === 0) continue; // Saltar encabezado

            $productoData = [];

            foreach ($request->mapping as $excelColumn => $dbField) {
                if (!empty($dbField)) {
                    $colIndex = array_search($excelColumn, $headers);

                    if ($dbField === 'categoria_id') {
                        $categoriaValor = trim($row[$colIndex] ?? '');

                        if ($categoriaValor) {
                            if (is_numeric($categoriaValor)) {
                                $productoData['categoria_id'] = (int) $categoriaValor;
                            } else {
                                $categoria = Categoria::firstOrCreate(['nombre' => $categoriaValor]);
                                $productoData['categoria_id'] = $categoria->id;
                            }
                        } else {
                            $productoData['categoria_id'] = 1; // por defecto
                        }
                    } else {
                        $productoData[$dbField] = $row[$colIndex] ?? null;
                    }
                }
            }

            // Nombre obligatorio
            if (empty($productoData['nombre'])) {
                continue; // saltar fila sin nombre
            }

            // Valores por defecto para precios
            if (empty($productoData['precio_compra'])) {
                $productoData['precio_compra'] = 0.00;
            }
            if (empty($productoData['precio_venta'])) {
                $productoData['precio_venta'] = 0.00;
            }

            // Categoría por defecto
            if (empty($productoData['categoria_id'])) {
                $productoData['categoria_id'] = 1;
            }
if (empty($productoData['stock'])) {
    $productoData['stock'] = 0;
}
            Producto::create($productoData);
        }

        unlink($fullPath);

        return redirect()->route('productos.import.form')
                         ->with('success', 'Productos importados correctamente.');
    }
}
