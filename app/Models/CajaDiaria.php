<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CajaDiaria extends Model
{
    protected $table = 'caja_diaria';

    protected $fillable = [
        'fecha', 'saldo_inicial', 'saldo_final', 'observaciones'
    ];

    public function movimientos()
    {
        return $this->hasMany(MovimientoCaja::class, 'caja_id');
    }
}
