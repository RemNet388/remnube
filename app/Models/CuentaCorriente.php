<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuentaCorriente extends Model
{
    protected $table = 'cuentas_corrientes'; // 👈 importante
    protected $fillable = [
        'entidad_id',
        'entidad_tipo',
        'compra_id', // ✅ nuevo
        'fecha',
        'concepto',
        'debe',
        'haber',
        'saldo',
    ];

    // Relación polimórfica con Cliente o Proveedor
    public function entidad()
    {
        return $this->morphTo(__FUNCTION__, 'entidad_tipo', 'entidad_id');
    }
}
