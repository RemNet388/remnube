<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Retiro extends Model
{
    protected $fillable = [
        'fecha', 'monto', 'dejar_para_siguiente_caja'
    ];

    protected $casts = [
        'fecha' => 'date'
    ];

    public function gastos()
    {
        return $this->hasMany(Gasto::class);
    }

    // atributo calculado: monto disponible (monto inicial - gastos aplicados)
    public function getDisponibleAttribute()
    {
        $gastado = $this->gastos()->sum('monto');
        return round($this->monto - $gastado, 2);
    }
}
