<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriaSeeder extends Seeder
{
    public function run()
    {
        $categorias = ['Insumos', 'Periféricos'];

        foreach ($categorias as $cat) {
            Categoria::create(['nombre' => $cat]);
        }
    }
}
