<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $fillable = [
        'nombre', 'cuit', 'direccion', 'telefono', 'email'
    ];
    protected $table = 'proveedores'; // 👈 fuerza el nombre correcto
    public function compras()
    {
        return $this->hasMany(Compra::class);
    }
}
