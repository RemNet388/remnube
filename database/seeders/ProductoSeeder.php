<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;

class ProductoSeeder extends Seeder
{
    public function run()
    {
        // Producto de la categoría "Insumos" (ID 1)
        Producto::create([
            'categoria_id' => 1,
            'nombre' => 'Cable HDMI 3mts',
            'precio_compra' => 3000,
            'precio_venta' => 5000,
            'stock' => 8
        ]);

        // Otro producto de "Periféricos" (ID 2)
        Producto::create([
            'categoria_id' => 2,
            'nombre' => 'Teclado Genius con cable USB',
            'precio_compra' => 7000,
            'precio_venta' => 10000,
            'stock' => 10
        ]);
    }
}
