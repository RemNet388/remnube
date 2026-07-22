<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'fecha',
        'observaciones',
        'total',
        'forma_pago_id',
	    'caja_id',
        'user_id',
    ];

    // Relaciones
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function formaPago()
    {
        return $this->belongsTo(FormaPago::class, 'forma_pago_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class);
    }
    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }
        public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
