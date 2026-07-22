<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\CajaDiaria;
use App\Models\FormaPago;
use App\Models\MovimientoCaja;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MovimientoCajaController extends Controller
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
            'estado' => 'abierta',
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
            'estado' => 'cerrada',
        ]);

        return redirect()->route('caja.index')->with('success', 'Caja cerrada correctamente');
    }

    public function detalle(Caja $caja)
    {
        $movimientos = MovimientoCaja::where('caja_id', $caja->id)
            ->with('formaPago')
            ->orderBy('id', 'desc')
            ->get();

        $totalCaja = $movimientos->sum(function ($mov) {
            return $mov->tipo === 'ingreso'
                ? $mov->monto
                : -$mov->monto;
        });

        return view('cajas.detalle', compact('caja', 'movimientos', 'totalCaja'));
    }

    public function detalleModal(MovimientoCaja $movimiento)
    {
        $movimiento->load([
            'formaPago',
            'venta.formaPago',
            'venta.cliente',
            'venta.detalles.producto',
            'compra.formaPago',
            'compra.proveedor',
            'compra.detalles.producto',
        ]);

        return view('cajas.partials.detalle', compact('movimiento'));
    }

    public function transferirFormaPago(Request $request)
    {
        $request->validate([
            'forma_pago_origen_id' => 'required|exists:formas_pago,id|different:forma_pago_destino_id',
            'forma_pago_destino_id' => 'required|exists:formas_pago,id',
            'monto' => 'required|numeric|min:0.01',
            'observaciones' => 'nullable|string|max:255',
        ]);

        $fecha = Carbon::now()->toDateString();
        $monto = $request->monto;

        MovimientoCaja::create([
            'fecha' => $fecha,
            'tipo' => 'egreso',
            'concepto' => 'Transferencia a ' . FormaPago::find($request->forma_pago_destino_id)->nombre,
            'monto' => $monto,
            'forma_pago_id' => $request->forma_pago_origen_id,
        ]);

        MovimientoCaja::create([
            'fecha' => $fecha,
            'tipo' => 'ingreso',
            'concepto' => 'Transferencia desde ' . FormaPago::find($request->forma_pago_origen_id)->nombre,
            'monto' => $monto,
            'forma_pago_id' => $request->forma_pago_destino_id,
        ]);

        return back()->with('success', 'Transferencia registrada correctamente.');
    }
}
