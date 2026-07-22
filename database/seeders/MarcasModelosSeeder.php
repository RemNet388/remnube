<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Marca;
use App\Models\Modelo;

class MarcasModelosSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'Samsung' => ['Galaxy S21', 'Galaxy Note 20', 'Galaxy A52'],
            'LG' => ['K42', 'Velvet', 'Wing'],
            'Sony' => ['Xperia 1 III', 'Xperia 5 II', 'Xperia 10 III'],
        ];

        foreach ($data as $marcaNombre => $modelos) {
            $marca = Marca::create(['nombre' => $marcaNombre]);

            foreach ($modelos as $modelo) {
                Modelo::create([
                    'marca_id' => $marca->id,
                    'nombre' => $modelo,
                ]);
            }
        }
    }
}
