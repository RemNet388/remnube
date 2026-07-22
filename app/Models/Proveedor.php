<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $fillable = [
    'nombre',
    'cuit',
    'direccion',
    'telefono',
    'email',
    'saldo', // agregar acá
];
    
    protected $table = 'proveedores'; // 👈 fuerza el nombre correcto
    public function compras()
    {
        return $this->hasMany(Compra::class);
    }
    public function cuentaCorriente()
{
    return $this->morphMany(CuentaCorriente::class, 'entidad', 'entidad_tipo', 'entidad_id');
}
}
