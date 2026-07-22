<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        'nombre', 'dni', 'direccion', 'telefono', 'email'
    ];

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }

    public function ordenesServicio()
{
    return $this->hasMany(OrdenServicio::class);
}

public function cuentaCorriente()
{
    return $this->hasMany(\App\Models\CuentaCorriente::class, 'entidad_id')
        ->where('entidad_tipo', 'cliente');
}

public function saldo()
{
    return $this->cuentaCorriente()->latest('id')->value('saldo') ?? 0;
}

    public function getSaldoAttribute()
    {
        return \App\Models\CuentaCorriente::where('entidad_id', $this->id)
            ->where('entidad_tipo', 'cliente')
            ->latest('id')
            ->value('saldo') ?? 0;
    }

    public function ordenes()
{
    return $this->hasMany(OrdenServicio::class);
}
public function vehiculo()
{
    return $this->belongsTo(Vehiculo::class);
}


}
