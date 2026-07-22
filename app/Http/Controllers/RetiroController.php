<?php

namespace App\Http\Controllers;

use App\Models\Gasto;
use App\Models\Retiro;
use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class RetiroController extends Controller
{
    public function index()
{
    // Traer todos los retiros
    $retiros = Retiro::orderBy('fecha', 'desc')->get();

    // Traer todos los pagos
    $pagos = Pago::orderBy('fecha', 'desc')->get();

    // Total acumulado de retiros
    $totalRetiros = $retiros->sum('monto');

    // Total acumulado de pagos
    $totalPagos = $pagos->sum('monto');

    // Total disponible (retiros menos pagos)
    $totalDisponible = $totalRetiros - $totalPagos;

    return view('retiros.index', compact('retiros', 'pagos', 'totalRetiros', 'totalPagos', 'totalDisponible'));
}


    public function store(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'monto' => 'required|numeric|min:0',
            'dejar_para_siguiente_caja' => 'nullable|numeric|min:0'
        ]);

        Retiro::create($request->all());

        return redirect()->back()->with('success', 'Retiro registrado correctamente');
    }
}
