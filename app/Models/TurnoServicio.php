<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TurnoServicio extends Model
{
    use HasFactory;
    protected $table = 'turnos_servicio';
    
    protected $fillable = [
        'fecha',
        'hora_inicio',
        'hora_fin',
        'cliente_id',
        'estado',
        'orden_servicio_id',
        'nota',
    ];

    protected $casts = [
        'fecha' => 'date',
        'hora_inicio' => 'datetime:H:i',
        'hora_fin' => 'datetime:H:i',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function scopeDelDia($query, $fecha)
    {
        return $query->whereDate('fecha', $fecha)->orderBy('hora_inicio');
    }

    public function orden()
    {
        return $this->hasOne(OrdenServicio::class, 'turno_id'); // 🔹 clave foránea correcta
    }

}
