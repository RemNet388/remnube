<?php

class CajaDiaria extends Model
{
    protected $table = 'caja_diaria';

    protected $fillable = [
        'fecha',
        'saldo_inicial',
        'saldo_final',
        'observaciones'
    ];

    // Relación opcional con ventas
    public function ventas()
    {
        return $this->hasMany(Venta::class, 'fecha', 'fecha'); // si la tabla ventas tiene created_at
    }

    // Calcular total de ventas del día
    public function totalVentas()
    {
        return Venta::whereDate('created_at', $this->fecha)->sum('total');
    }

    // Calcular saldo final
    public function calcularSaldoFinal()
    {
        return $this->saldo_inicial + $this->totalVentas();
    }
}
