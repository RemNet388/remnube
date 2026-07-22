<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrdenServicio extends Model
{
    use HasFactory;

    protected $table = 'ordenes_servicio';

    protected $fillable = [
        'cliente_id',
        'marca_id',
        'modelo_id',
        'numero',
        'identificador',
        'detalle_reparacion',
        'observaciones',
        'estado',
        'presupuesto',
        'presupuesto_aprobado',
        'presupuesto_aprobado_el',
        'fecha_prometida',
        'finalizada_el',
        'entregada_el',
        'turno_id',    
    ];

    protected $casts = [
        'presupuesto_aprobado' => 'boolean',
        'presupuesto_aprobado_el' => 'datetime',
        'fecha_prometida' => 'date',
        'finalizada_el' => 'datetime',
        'entregada_el' => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    public function modelo()
    {
        return $this->belongsTo(Modelo::class);
    }

    public function turno()
{
    return $this->belongsTo(TurnoServicio::class);
}
    // Genera el número automáticamente al crear la orden
    protected static function booted()
    {
        static::creating(function ($orden) {
            if (empty($orden->numero)) {
                $ultimoId = static::max('id') + 1;
                $orden->numero = 'OT-' . str_pad($ultimoId, 6, '0', STR_PAD_LEFT);
            }
        });
    }
}
