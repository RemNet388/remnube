<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gasto;
use App\Models\Retiro;
use App\Models\FormaPago;
use App\Models\Caja;
use App\Models\MovimientoCaja;
use Illuminate\Support\Facades\DB;

class GastoController extends Controller
{
    public function index()
    {
        $gastos = Gasto::with(['formaPago', 'retiro'])->orderByDesc('fecha')->get();
        return view('gastos.index', compact('gastos'));
    }

    public function create()
    {
        $formasPago = FormaPago::all();
        $retiros = Retiro::orderByDesc('fecha')->get();
        return view('gastos.create', compact('formasPago', 'retiros'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'descripcion' => 'required|string|max:255',
            'monto' => 'required|numeric|min:0.01',
            'forma_pago_id' => 'required|exists:formas_pago,id',
            'retiro_id' => 'nullable|exists:retiros,id'
        ]);

        DB::beginTransaction();
        try {
            // Si se paga desde un retiro, validar saldo disponible
            if ($request->filled('retiro_id')) {
                $retiro = Retiro::findOrFail($request->retiro_id);
                if ($retiro->disponible < $request->monto) {
                    return back()->withErrors(['monto' => 'El retiro seleccionado no tiene saldo suficiente.'])->withInput();
                }
            }

            $gasto = Gasto::create($request->only('fecha','descripcion','monto','forma_pago_id','retiro_id'));

            // Si se pagó directamente desde la caja (sin retiro), registramos movimiento en caja
            if (!$request->filled('retiro_id')) {
                // buscar caja abierta
                $caja = Caja::where('estado', 'abierta')->latest('fecha_apertura')->first();
                if ($caja) {
                    MovimientoCaja::create([
                        'caja_id' => $caja->id,
                        'tipo' => 'egreso',
                        'monto' => $gasto->monto,
                        'concepto' => 'Gasto: '.$gasto->descripcion
                    ]);
                } else {
                    // Opcional: manejar caso sin caja abierta (error o registrar sin movimiento)
                    // return back()->withErrors(['caja' => 'No hay caja abierta para registrar el egreso.']);
                }
            } else {
                // Si se usó retiro, no registramos movimiento en caja (se paga con fondos ya retirados).
                // El saldo disponible se calcula con $retiro->disponible (no modificamos el monto inicial).
            }

            DB::commit();
            return redirect()->route('gastos.index')->with('success', 'Gasto registrado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $gasto = Gasto::findOrFail($id);
        $formasPago = FormaPago::all();
        $retiros = Retiro::orderByDesc('fecha')->get();
        return view('gastos.edit', compact('gasto','formasPago','retiros'));
    }

    public function update(Request $request, $id)
    {
        $gasto = Gasto::findOrFail($id);

        $request->validate([
            'fecha' => 'required|date',
            'descripcion' => 'required|string|max:255',
            'monto' => 'required|numeric|min:0.01',
            'forma_pago_id' => 'required|exists:formas_pago,id',
            'retiro_id' => 'nullable|exists:retiros,id'
        ]);

        DB::beginTransaction();
        try {
            // Si el gasto estaba asociado a un retiro y ahora cambia de retiro o monto,
            // validá el nuevo retiro (si aplica)
            if ($request->filled('retiro_id')) {
                $retiro = Retiro::findOrFail($request->retiro_id);
                // calcular disponible sin contar el propio gasto actual
                $gastadoOtros = $retiro->gastos()->where('id','<>',$gasto->id)->sum('monto');
                $disponible = $retiro->monto - $gastadoOtros;
                if ($disponible < $request->monto) {
                    return back()->withErrors(['monto' => 'El retiro seleccionado no tiene saldo suficiente.'])->withInput();
                }
            }

            // Si el gasto se pagó desde caja y existió un movimiento en movimiento_caja,
            // deberíamos actualizar/eliminar ese movimiento. Aquí asumimos que el movimiento
            // fue guardado con concepto 'Gasto: '.$descripcion; se puede ajustar según tu esquema.
            if (!$request->filled('retiro_id')) {
                // actualizar movimiento caja si existe
                $mov = MovimientoCaja::where('concepto', 'Gasto: '.$gasto->descripcion)
                    ->where('monto', $gasto->monto)
                    ->first();
                if ($mov) {
                    $mov->update(['monto' => $request->monto, 'concepto' => 'Gasto: '.$request->descripcion]);
                }
            } else {
                // si antes no era con retiro y ahora sí, borrar el movimiento asociado (si existe)
                $mov = MovimientoCaja::where('concepto', 'Gasto: '.$gasto->descripcion)
                    ->where('monto', $gasto->monto)
                    ->first();
                if ($mov) $mov->delete();
            }

            $gasto->update($request->only('fecha','descripcion','monto','forma_pago_id','retiro_id'));

            DB::commit();
            return redirect()->route('gastos.index')->with('success', 'Gasto actualizado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $gasto = Gasto::findOrFail($id);

        DB::transaction(function() use($gasto) {
            // eliminar movimiento de caja si existió
            MovimientoCaja::where('concepto', 'Gasto: '.$gasto->descripcion)
                ->where('monto', $gasto->monto)
                ->delete();

            $gasto->delete();
        });

        return redirect()->route('gastos.index')->with('success', 'Gasto eliminado correctamente.');
    }
}
