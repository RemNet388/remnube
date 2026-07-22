<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriaSeeder extends Seeder
{
    public function run()
    {
        $categorias = ['General', 'categoria 1'];

        foreach ($categorias as $cat) {
            Categoria::create(['nombre' => $cat]);
        }
    }
}
