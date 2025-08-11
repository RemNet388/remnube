<?php

namespace App\Http\Controllers;

use App\Models\CajaDiaria;
use App\Models\MovimientoCaja;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CajaDiariaController extends Controller
{
    public function index()
{
    // Caja de hoy
    $cajaDiaria = CajaDiaria::whereDate('fecha', now()->toDateString())->first();

    // Histórico de cajas (excluyendo la de hoy para que no se repita)
    $cajas = CajaDiaria::orderBy('fecha', 'desc')->get();

    return view('caja.index', compact('cajaDiaria', 'cajas'));
}

    public function create()
    {
        return view('caja.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'saldo_inicial' => 'required|numeric',
            'observaciones' => 'required|string|max:50'
        ]);

        CajaDiaria::create($request->only(['fecha', 'saldo_inicial', 'saldo_final', 'observaciones']));
        return redirect()->route('caja.index')->with('success', 'Caja creada correctamente.');
    }

    public function show($id)
{
    $caja = CajaDiaria::findOrFail($id);

    // Ventas de la fecha de la caja, con cliente y forma de pago
    $ventasPorFormaPago = \App\Models\Venta::with(['cliente', 'formaPago'])
        ->whereDate('fecha', $caja->fecha)
        ->orderBy('forma_pago_id')
        ->get()
        ->groupBy('formaPago.nombre'); // Agrupa por nombre de forma de pago

    // Total general
    $totalGeneral = $ventasPorFormaPago->flatten()->sum('total');

    return view('caja.show', compact('caja', 'ventasPorFormaPago', 'totalGeneral'));
}


    public function edit(CajaDiaria $caja)
    {
        return view('caja.edit', compact('caja'));
    }

    public function update(Request $request, CajaDiaria $caja)
    {
        $request->validate([
            'fecha' => 'required|date',
            'saldo_inicial' => 'required|numeric',
            'observaciones' => 'required|string|max:50'
        ]);

        $caja->update($request->only(['fecha', 'saldo_inicial', 'saldo_final', 'observaciones']));
        return redirect()->route('caja.index')->with('success', 'Caja actualizada correctamente.');
    }

    public function destroy(CajaDiaria $caja)
    {
        $caja->delete();
        return redirect()->route('caja.index')->with('success', 'Caja eliminada.');
    }

    public function abrir()
    {
        // Si ya existe una caja para hoy, redirigimos
        if (CajaDiaria::where('fecha', Carbon::today())->exists()) {
            return redirect()->route('caja.index')->with('error', 'La caja ya está abierta para hoy.');
        }

        // Creamos una nueva caja
        CajaDiaria::create([
            'fecha' => Carbon::today(),
            'saldo_inicial' => 0,
            'saldo_final' => 0,
            'observaciones' => null,
        ]);

        return redirect()->route('caja.index')->with('success', 'Caja abierta correctamente.');
    }
}
