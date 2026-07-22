<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    protected $fillable = ['fecha_apertura','fecha_cierre','estado','monto_inicial','monto_final','fondo_proximo'];

    public function detalles() {
        return $this->hasMany(CajaDetalle::class);
    }

    protected $casts = [
       'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime',
    ];

    public function ventas() {
        return $this->hasMany(Venta::class);
    }

    public function movimientos() {
        return $this->hasMany(MovimientoCaja::class, 'caja_id');
    }
}
