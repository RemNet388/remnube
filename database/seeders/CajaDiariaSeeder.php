<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CajaDiaria;
use Carbon\Carbon;

class CajaDiariaSeeder extends Seeder
{
    public function run()
    {
        CajaDiaria::create([
            'fecha' => Carbon::now()->toDateString(),
            'saldo_inicial' => 0,
            'saldo_final' => 0,
            'observaciones' => 'Caja inicial creada por seeder'
        ]);
    }
}
