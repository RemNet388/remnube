<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\FormaPago;
use App\Models\Categoria;
use App\Models\MovimientoCaja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class CompraController extends Controller
{
    public function index()
    {
        $compras = Compra::with('proveedor')->latest()->get();
        return view('compras.index', compact('compras'));
    }

    public function create()
    {
            $proveedores = Proveedor::all();
            $categorias = Categoria::all(); // Para el modal de nuevo producto

            $formasPago = FormaPago::all();
            return view('compras.create', compact('proveedores', 'formasPago', 'categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'proveedor_id' => 'required|exists:proveedores,id',
            'forma_pago_id' => 'required|exists:formas_pago,id',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio_compra' => 'required|numeric|min:0'
        ]);

        DB::beginTransaction();
        try {
            // Crear compra
            $compra = Compra::create([
                'fecha' => $request->fecha,
                'proveedor_id' => $request->proveedor_id,
                'forma_pago_id' => $request->forma_pago_id,
                'total' => collect($request->productos)->sum(function($prod) {
                    return $prod['cantidad'] * $prod['precio_compra'];
                })
            ]);

            // Guardar detalles
            foreach ($request->productos as $producto) {
                DetalleCompra::create([
                    'compra_id' => $compra->id,
                    'producto_id' => $producto['id'],
                    'cantidad' => $producto['cantidad'],
                    'precio_unitario' => $producto['precio_compra'],
                    'subtotal' => $producto['cantidad'] * $producto['precio_compra']
                ]);

                // Actualizar stock (sumar)
                Producto::where('id', $producto['id'])
                    ->increment('stock', $producto['cantidad']);
            }

            // Registrar movimiento en caja si es contado
            if (strtolower($compra->formaPago->nombre) === 'efectivo') {
                MovimientoCaja::create([
                    'caja_id' => 1, // Esto lo adaptamos para usar caja activa
                    'tipo' => 'egreso',
                    'monto' => $compra->total,
                    'concepto' => 'Compra #' . $compra->id
                ]);
            }

            DB::commit();

            return redirect()->route('compras.index')
                             ->with('success', 'Compra registrada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Error al guardar la compra: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $compra = Compra::with(['proveedor', 'formaPago', 'detalles.producto'])->findOrFail($id);
        return view('compras.show', compact('compra'));
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $compra = Compra::with('detalles')->findOrFail($id);

            // Devolver stock (restar lo que se había sumado)
            foreach ($compra->detalles as $detalle) {
                Producto::where('id', $detalle->producto_id)
                    ->decrement('stock', $detalle->cantidad);
            }

            // Eliminar movimiento de caja
            MovimientoCaja::where('concepto', 'Compra #' . $compra->id)->delete();

            // Eliminar detalles y compra
            $compra->detalles()->delete();
            $compra->delete();
        });

        return redirect()->route('compras.index')->with('success', 'Compra eliminada correctamente.');
    }

    public function print($id)
    {
        $compra = Compra::with(['proveedor', 'formaPago', 'detalles.producto'])->findOrFail($id);
        $pdf = Pdf::loadView('compras.boleta', compact('compra'));
        return $pdf->stream('compra_' . $compra->id . '.pdf');
    }
}
