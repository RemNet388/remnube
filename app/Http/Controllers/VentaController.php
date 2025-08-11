<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\MovimientoCaja;
use App\Models\CajaDiaria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class VentaController extends Controller
{
    public function index()
    {
        $ventas = Venta::with('cliente')->latest()->get();
        return view('ventas.index', compact('ventas'));
    }

    public function create()
    {
        $clientes = \App\Models\Cliente::all();
        $productos = Producto::all();
        $formasPago = \App\Models\FormaPago::all();
        return view('ventas.create', compact('clientes', 'productos', 'formasPago'));
    }

    public function store(Request $request)
{
    // Validar
    $request->validate([
        'fecha' => 'required|date',
        'cliente_id' => 'required|exists:clientes,id',
        'forma_pago_id' => 'required',
        'productos' => 'required|array|min:1',
        'productos.*.id' => 'required|exists:productos,id',
        'productos.*.cantidad' => 'required|integer|min:1',
        'productos.*.precio' => 'required|numeric|min:0'
    ]);

    DB::beginTransaction();
    try {
        // Crear venta
        $venta = Venta::create([
            'fecha' => $request->fecha,
            'cliente_id' => $request->cliente_id,
            'forma_pago_id' => $request->forma_pago_id,
            'total' => collect($request->productos)->sum(function ($prod) {
                return $prod['cantidad'] * $prod['precio'];
            })
        ]);

        // Guardar detalles
        foreach ($request->productos as $producto) {
            DetalleVenta::create([
                'venta_id' => $venta->id,
                'producto_id' => $producto['id'],
                'cantidad' => $producto['cantidad'],
                'precio_unitario' => $producto['precio'],
                'subtotal' => $producto['cantidad'] * $producto['precio']
            ]);

            // Actualizar stock
            Producto::where('id', $producto['id'])
                ->decrement('stock', $producto['cantidad']);
        }

        DB::commit();

        return redirect()->route('ventas.index')
                         ->with('success', 'Venta registrada correctamente.');

    } catch (\Exception $e) {
        DB::rollBack();
        dd($e->getMessage(), $e->getTraceAsString());
    }
}


public function destroy($id)
{
    DB::transaction(function () use ($id) {
        $venta = Venta::with('detalles')->findOrFail($id);

        // 1️⃣ Devolver stock
        foreach ($venta->detalles as $detalle) {
            $producto = Producto::find($detalle->producto_id);
            $producto->stock += $detalle->cantidad;
            $producto->save();
        }

        // 2️⃣ Eliminar movimiento de caja
        MovimientoCaja::where('concepto', 'Venta #' . $venta->id)->delete();

        // 3️⃣ Eliminar detalles y venta
        $venta->detalles()->delete();
        $venta->delete();
    });

    return redirect()->route('ventas.index')->with('success', 'Venta eliminada y stock devuelto correctamente');
}

public function show($id)
{
    $venta = Venta::with(['cliente', 'formaPago', 'detalles.producto'])->findOrFail($id);
    return view('ventas.show', compact('venta'));
}

public function print($id)
{
    $venta = Venta::with(['cliente', 'formaPago', 'detalles.producto'])->findOrFail($id);

    $pdf = Pdf::loadView('ventas.boleta', compact('venta'));
    return $pdf->stream('venta_'.$venta->id.'.pdf');
}

}
