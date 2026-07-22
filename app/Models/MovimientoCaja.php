<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoCaja extends Model
{
    protected $table = 'movimientos_caja';

    protected $fillable = [
        'caja_id',
        'tipo',
        'concepto',
        'monto',
        'forma_pago_id',
        'entidad_id',
        'entidad_tipo',
    ];

    public function caja()
    {
        return $this->belongsTo(Caja::class, 'caja_id'); // <- Cambiado a Caja
    }
public function venta()
{
    return $this->belongsTo(Venta::class, 'concepto_id'); // si guardás el id de la venta
}

public function compra()
{
    return $this->belongsTo(Compra::class, 'concepto_id'); // si guardás el id de la compra
}
public function formaPago()
{
    return $this->belongsTo(FormaPago::class, 'forma_pago_id');
}
}

