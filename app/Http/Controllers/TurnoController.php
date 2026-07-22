<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TurnoServicio;
use App\Models\Cliente;
use Illuminate\Support\Facades\DB;

class TurnoController extends Controller
{
    // Mostrar la agenda de turnos
    public function index(Request $request)
    {
        $fecha = $request->get('fecha', now()->toDateString());

        // Cargar todos los turnos de la fecha
        $turnosDB = TurnoServicio::where('fecha', $fecha)
                    ->get()
                    ->keyBy(function($t){
                        return (int) date('H', strtotime($t->hora_inicio));
                    });

        // Generar horas de 8 a 20
        $turnos = [];
        for ($h = 8; $h <= 20; $h++) {
            $turnos[] = [
                'hora' => sprintf('%02d:00', $h),
                'turno' => $turnosDB->get($h) // null si libre
            ];
        }

        $clientes = Cliente::orderBy('nombre')->get();

        return view('turnos.index', compact('fecha', 'turnos', 'clientes'));
    }

    // Reservar un turno
    public function reservar(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'hora' => 'required',
        ]);

        $fecha = $request->fecha;
        $horaInt = (int) date('H', strtotime($request->hora));

        // Verificar si ya existe turno en esa hora
$horaStr = date('H:00:00', strtotime($request->hora));

$existe = TurnoServicio::where('fecha', $fecha)
            ->where('hora_inicio', $horaStr)
            ->exists();

if ($existe) {
    return redirect()->back()->with('error', 'Este turno ya está reservado');
}

        // Cliente existente o nuevo
        $cliente_id = $request->cliente_id;
        if (!$cliente_id) {
            $cliente = Cliente::firstOrCreate(
                ['telefono' => $request->telefono_cliente],
                ['nombre' => $request->nombre_cliente ?? 'Sin nombre']
            );
            $cliente_id = $cliente->id;
        }

        // Crear turno
        DB::transaction(function () use ($fecha, $horaInt, $cliente_id, $request) {
            TurnoServicio::create([
                'fecha' => $fecha,
                'hora_inicio' => sprintf('%02d:00:00', $horaInt),
                'hora_fin' => sprintf('%02d:00:00', $horaInt + 1),
                'cliente_id' => $cliente_id,
                'nota' => $request->nota ?? null,
                'estado' => 'reservado',
            ]);
        });

        return redirect()->back()->with('success', 'Turno reservado correctamente');
    }

public function updateFechaHora(Request $request)
{
    $request->validate([
        'turno_id' => 'required|integer|exists:turnos_servicio,id',
        'fecha'    => 'required|date',
        'hora'     => 'required', // aceptamos "08:00" o "08:00:00"
    ]);

    $turnoId = (int) $request->turno_id;
    $fecha   = $request->fecha;
    // normalizamos la hora a HH:00:00 (si querés permitir minutos, adaptá)
    $horaInt = (int) date('H', strtotime($request->hora));
    $horaStr = sprintf('%02d:00:00', $horaInt);
    $horaFin = sprintf('%02d:00:00', $horaInt + 1);

    // Verificamos si existe otro turno con la misma fecha+hora
    $conflicto = \App\Models\TurnoServicio::where('fecha', $fecha)
                    ->where('hora_inicio', $horaStr)
                    ->where('id', '!=', $turnoId)
                    ->exists();

    if ($conflicto) {
        return back()->with('error', "Ya existe un turno para {$fecha} a las " . substr($horaStr,0,5));
    }

    // Todo ok -> actualizamos
    try {
        \DB::transaction(function() use ($turnoId, $fecha, $horaStr, $horaFin) {
            $turno = \App\Models\TurnoServicio::findOrFail($turnoId);
            $turno->fecha = $fecha;
            $turno->hora_inicio = $horaStr;
            $turno->hora_fin = $horaFin;
            $turno->save();
        });

        return back()->with('success', 'Turno actualizado correctamente.');
    } catch (\Throwable $e) {
        \Log::error('Error actualizando turno: '.$e->getMessage());
        return back()->with('error', 'Ocurrió un error al actualizar el turno.');
    }
}


public function destroy($id)
{
    try {
        $turno = \App\Models\TurnoServicio::findOrFail($id);

        if ($turno->orden_servicio_id) {
            return back()->with('error', 'No se puede eliminar un turno que tiene una orden asociada.');
        }

        $turno->delete();
        return back()->with('success', 'Turno eliminado correctamente.');
    } catch (\Throwable $e) {
        \Log::error('Error eliminando turno: '.$e->getMessage());
        return back()->with('error', 'Ocurrió un error al eliminar el turno.');
    }
}


}
