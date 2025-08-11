<?php

namespace App\Http\Controllers;

use App\Models\CajaDiaria;
use App\Models\MovimientoCaja;
use Illuminate\Http\Request;

class CajaController extends Controller
{
    public function index()
    {
        $cajas = CajaDiaria::latest()->get();
        return view('caja.index', compact('cajas'));
    }

    public function abrir(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date|unique:caja_diaria,fecha',
            'saldo_inicial' => 'required|numeric|min:0',
        ]);

        CajaDiaria::create([
            'fecha' => $request->fecha,
            'saldo_inicial' => $request->saldo_inicial,
            'estado' => 'abierta'
        ]);

        return redirect()->route('caja.index')->with('success', 'Caja abierta correctamente');
    }

    public function cerrar($id)
    {
        $caja = CajaDiaria::findOrFail($id);

        $totalIngresos = MovimientoCaja::where('fecha', $caja->fecha)->where('tipo', 'ingreso')->sum('monto');
        $totalEgresos = MovimientoCaja::where('fecha', $caja->fecha)->where('tipo', 'egreso')->sum('monto');

        $caja->update([
            'total_ingresos' => $totalIngresos,
            'total_egresos' => $totalEgresos,
            'saldo_final' => $caja->saldo_inicial + $totalIngresos - $totalEgresos,
            'estado' => 'cerrada'
        ]);

        return redirect()->route('caja.index')->with('success', 'Caja cerrada correctamente');
    }
}
