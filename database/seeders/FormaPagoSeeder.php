<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FormaPago;

class FormaPagoSeeder extends Seeder
{
    public function run()
    {
        $formas = [
            'Contado efectivo',
            'Transferencia',
            'Tarjeta de crédito',
            'Tarjeta de débito'
        ];

        foreach ($formas as $forma) {
            FormaPago::create(['nombre' => $forma]);
        }
    }
}
