<?php

namespace App\Http\Controllers;

use App\Models\MovimientoCaja;
use App\Models\FormaPago;
use App\Models\Caja;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TransferenciaController extends Controller
{
    // 📋 Muestra formulario + últimas transferencias
    public function transferencias()
    {
        $formasPago = FormaPago::orderBy('nombre')->get();

        $transferencias = MovimientoCaja::where('concepto', 'like', 'Transferencia%')
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get()
            ->map(function ($mov) {
                return (object)[
                    'tipo' => ucfirst($mov->tipo),
                    'forma_pago' => $mov->formaPago->nombre ?? '-',
                    'monto' => $mov->monto,
                    'concepto' => $mov->concepto,
                    'fecha' => $mov->created_at->format('d/m/Y H:i'),
                ];
            });

        return view('formas_pago.transferencias', compact('formasPago', 'transferencias'));
    }

    // 💸 Ejecuta la transferencia entre formas de pago
    public function transferirFormaPago(Request $request)
    {
        $request->validate([
            'forma_pago_origen_id' => 'required|exists:formas_pago,id|different:forma_pago_destino_id',
            'forma_pago_destino_id' => 'required|exists:formas_pago,id',
            'monto' => 'required|numeric|min:0.01',
            'observaciones' => 'nullable|string|max:255',
        ]);

        // 🧾 Caja actual (abierta)
        $caja = Caja::where('estado', 'abierta')->latest()->first();
        if (!$caja) {
            return back()->with('error', 'No hay una caja abierta. No se puede registrar la transferencia.');
        }

        $monto = $request->monto;
        $fpOrigen = FormaPago::find($request->forma_pago_origen_id);
        $fpDestino = FormaPago::find($request->forma_pago_destino_id);

        // 💰 Egreso
        MovimientoCaja::create([
            'caja_id' => $caja->id,
            'tipo' => 'egreso',
            'concepto' => 'Transferencia a ' . $fpDestino->nombre,
            'monto' => $monto,
            'forma_pago_id' => $fpOrigen->id,
        ]);

        // 💰 Ingreso
        MovimientoCaja::create([
            'caja_id' => $caja->id,
            'tipo' => 'ingreso',
            'concepto' => 'Transferencia desde ' . $fpOrigen->nombre,
            'monto' => $monto,
            'forma_pago_id' => $fpDestino->id,
        ]);

        return back()->with('success', 'Transferencia registrada correctamente.');
    }
}
