<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\FormaPago;
use Illuminate\Http\Request;

class PagoController extends Controller
{

public function store(Request $request)
{
    $request->validate([
        'fecha' => 'required|date',
        'monto' => 'required|numeric|min:0.01',
        'motivo' => 'nullable|string|max:255',
        'forma_pago_id' => 'nullable|exists:formas_pago,id'
    ]);

    Pago::create([
        'fecha' => $request->fecha,
        'monto' => $request->monto,
        'motivo' => $request->motivo,
        'forma_pago_id' => $request->forma_pago_id ?? 1, // por defecto efectivo
    ]);

    return back()->with('success', 'Pago registrado correctamente.');
}

public function index()
{
    $pagos = Pago::with('formaPago')->orderBy('fecha', 'desc')->get();

    // Total de todos los pagos
    $totalPagos = $pagos->sum('monto');

    return view('pagos.index', compact('pagos', 'totalPagos'));
}
public function indexPorFormaPago()
{
    $formasPago = \App\Models\FormaPago::all();
    // Trae todos los pagos, con relación a forma de pago
    $pagos = \App\Models\Pago::with('formaPago')->orderBy('fecha', 'desc')->get();

    // Agrupar por forma de pago
    $pagosPorForma = $pagos->groupBy(fn($pago) => $pago->formaPago->nombre ?? 'Sin forma');

    // Total de todos los pagos
    $totalPagos = $pagos->sum('monto');

    return view('pagos.index_forma', compact('pagosPorForma', 'formasPago', 'pagos', 'totalPagos'));
}
}
