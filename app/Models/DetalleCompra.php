<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleCompra extends Model
{
    protected $fillable = [
        'compra_id', 'producto_id', 'cantidad', 'precio_unitario', 'subtotal'
    ];

    public function compra()
    {
        return $this->belongsTo(Compra::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
        /**
     * Acceso rápido al proveedor a través de la compra
     */
    public function proveedor()
    {
        return $this->compra->proveedor();
    }
}
