<?php

namespace App\Imports;

use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductoImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    protected $mapping;

    public function __construct(array $mapping)
    {
        $this->mapping = $mapping;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            Log::info("Fila importada", ['index' => $index, 'row' => $row->toArray()]);

            $productoData = [];

            foreach ($this->mapping as $excelColumn => $dbField) {
                if (!empty($dbField)) {
                    $value = $row[strtolower($excelColumn)] ?? null;

                    Log::info("Mapping columna", [
                        'excelColumn' => $excelColumn,
                        'dbField' => $dbField,
                        'value' => $value
                    ]);

                    if ($dbField === 'categoria_id') {
                        $categoriaValor = trim($value ?? '');

                        if ($categoriaValor) {
                            if (is_numeric($categoriaValor)) {
                                $productoData['categoria_id'] = (int) $categoriaValor;
                            } else {
                                $categoria = Categoria::firstOrCreate(['nombre' => $categoriaValor]);
                                $productoData['categoria_id'] = $categoria->id;
                            }
                        } else {
                            $productoData['categoria_id'] = 1;
                        }
                    } else {
                        $productoData[$dbField] = $value;
                    }
                }
            }

            if (empty($productoData['nombre'])) {
                Log::warning("Fila sin nombre, se salta", ['row' => $row->toArray()]);
                continue;
            }

            // Defaults
            $productoData['precio_compra'] = $productoData['precio_compra'] ?? 0.00;
            $productoData['precio_venta']  = $productoData['precio_venta'] ?? 0.00;
            $productoData['stock']         = $productoData['stock'] ?? 0;
            $productoData['categoria_id']  = $productoData['categoria_id'] ?? 1;

            Log::info("Creando producto", $productoData);

            Producto::create($productoData);
        }
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
