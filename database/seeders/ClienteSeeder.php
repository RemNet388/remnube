<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;

class ClienteSeeder extends Seeder
{
    public function run()
    {
        Cliente::create([
            'nombre' => 'Consumidor Final',
            'dni' => null,
            'direccion' => null,
            'telefono' => null,
            'email' => null
        ]);
    }
}
