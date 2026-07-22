<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gasto extends Model
{
    protected $fillable = ['descripcion', 'monto', 'forma_pago_id', 'retiro_id', 'fecha'];

    protected $casts = [
        'fecha' => 'date'
    ];

    public function retiro()
    {
        return $this->belongsTo(Retiro::class);
    }

    public function formaPago()
    {
        return $this->belongsTo(FormaPago::class, 'forma_pago_id');
    }
}
