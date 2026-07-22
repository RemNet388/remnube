<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoStock extends Model
{
    protected $table = 'movimientos_stock'; // o el nombre que tenga tu tabla

    protected $fillable = ['producto_id', 'tipo', 'cantidad', 'descripcion'];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

        public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }
    
}
