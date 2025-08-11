<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoCaja extends Model
{
    protected $table = 'movimientos_caja';

    protected $fillable = [
        'caja_id', 'tipo', 'concepto', 'monto'
    ];

    public function caja()
    {
        return $this->belongsTo(CajaDiaria::class, 'caja_id');
    }
}
