<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = [
        'categoria_id', 'nombre', 'descripcion', 'precio_compra', 'precio_venta', 'stock', 'codigo', 'imagen', 'fecha_vencimiento',
    ];

    protected $casts = [
    'fecha_vencimiento' => 'date',
];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function detalleVentas()
    {
        return $this->hasMany(DetalleVenta::class);
    }

    public function detalleCompras()
    {
        return $this->hasMany(DetalleCompra::class);
    }
    public function movimientos()
{
    return $this->hasMany(MovimientoStock::class, 'producto_id');
}

public function movimientosStock()
{
    return $this->hasMany(MovimientoStock::class, 'producto_id')->with('proveedor');
}

}
