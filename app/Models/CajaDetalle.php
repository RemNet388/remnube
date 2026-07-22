<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CajaDetalle extends Model
{
    protected $fillable = ['caja_id','forma_pago_id','monto'];

    public function caja() {
        return $this->belongsTo(Caja::class);
    }

    public function formaPago() {
        return $this->belongsTo(FormaPago::class);
    }
}
