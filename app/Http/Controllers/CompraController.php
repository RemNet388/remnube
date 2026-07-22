<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\Caja;
use App\Models\DetalleCompra;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\FormaPago;
use App\Models\Categoria;
use App\Models\MovimientoStock;
use App\Models\MovimientoCaja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class CompraController extends Controller
{
public function index()
{
    $compras = Compra::with("proveedor")
        ->latest()
        ->paginate(10); // 10 por página, ajusta según necesites

    return view("compras.index", compact("compras"));
}

    public function create()
    {
        $proveedores = Proveedor::all();
        $categorias = Categoria::all(); // Para el modal de nuevo producto

        $formasPago = \App\Models\FormaPago::activas()->get();
        return view(
            "compras.create",
            compact("proveedores", "formasPago", "categorias")
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            "fecha" => "required|date",
            "proveedor_id" => "required|exists:proveedores,id",
            "forma_pago_id" => "required|exists:formas_pago,id",
            "numero_comprobante" => "required|string|max:255", // <-- nuevo
            "productos" => "required|array|min:1",
            "productos.*.id" => "required|exists:productos,id",
            "productos.*.cantidad" => "required|integer|min:1",
            "productos.*.precio_compra" => "required|numeric|min:0",
        ]);

        // Registrar movimiento en caja
        $cajaActiva = Caja::where("estado", "abierta")->first();
        if (!$cajaActiva) {
            return back()->withErrors(["error" => "No hay caja abierta"]);
        }

        $conceptoCuenta = $request->numero_comprobante
        ? 'Comprobante ' . $request->numero_comprobante
        : 'Compra #' . $compra->id;

        DB::beginTransaction();
        try {
            // Crear compra
            $compra = Compra::create([
                "fecha" => $request->fecha,
                "proveedor_id" => $request->proveedor_id,
                "forma_pago_id" => $request->forma_pago_id,
                "caja_id" => $cajaActiva->id,
                "numero_comprobante" => $request->numero_comprobante, // <-- agregado                
                "total" => collect($request->productos)->sum(function ($prod) {
                    return $prod["cantidad"] * $prod["precio_compra"];
                }),
            ]);

            // Guardar detalles
            foreach ($request->productos as $prod) {
                DetalleCompra::create([
                    "compra_id" => $compra->id,
                    "producto_id" => $prod["id"],
                    "cantidad" => $prod["cantidad"],
                    "precio_unitario" => $prod["precio_compra"],
                    "subtotal" => $prod["cantidad"] * $prod["precio_compra"],
                ]);

                Producto::where("id", $prod["id"])->update([
                    "stock" => DB::raw("stock + {$prod["cantidad"]}"),
                    "precio_compra" => $prod["precio_compra"],
                ]);

                // Registrar movimiento de stock
                MovimientoStock::create([
                    "producto_id" => $prod["id"],
                    "tipo" => "compra", // porque es una compra
                    "cantidad" => $prod["cantidad"],
                    "descripcion" => "Compra #" . $compra->id . " - Comprobante: " . $request->numero_comprobante,
                ]);
            }

            MovimientoCaja::create([
                "caja_id" => $cajaActiva->id,
                "tipo" => "egreso",
                "monto" => $compra->total,
                "forma_pago_id" => $request->forma_pago_id,
                "concepto" => "Compra #" . $compra->id . " - Comprobante: " . $request->numero_comprobante,
            ]);

if ($request->forma_pago_id == 2) {
    // Registrar movimiento en cuenta corriente del proveedor
    $proveedor = Proveedor::find($request->proveedor_id);

$saldoAnterior = $proveedor->cuentaCorriente()
    ->latest('id')
    ->value('saldo') ?? 0;

$conceptoCuenta = $request->numero_comprobante
    ? 'Comprobante ' . $request->numero_comprobante
    : 'Compra #' . $compra->id;

$proveedor->cuentaCorriente()->create([
    'fecha' => $request->fecha,
    'entidad_tipo' => 'proveedor', // ✅ correcto
    'compra_id' => $compra->id, // ✅ clave
    'debe' => $compra->total,
    'haber' => 0,
    'saldo' => $saldoAnterior + $compra->total,
    'concepto' => $conceptoCuenta,
]);

    // Actualizar saldo del proveedor
    $proveedor->saldo = ($proveedor->saldo ?? 0) + $compra->total;
    $proveedor->save();
}
 
            DB::commit();

            return redirect()
                ->route("compras.index")
                ->with("success", "Compra registrada correctamente.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(
                "Error al guardar la compra: " . $e->getMessage()
            );
        }
    }

    public function show($id)
    {
        $compra = Compra::with([
            "proveedor",
            "formaPago",
            "detalles.producto",
        ])->findOrFail($id);
        return view("compras.show", compact("compra"));
    }

    public function destroy($id)
{
    DB::transaction(function () use ($id) {

        $compra = Compra::with(['detalles', 'proveedor'])->findOrFail($id);
        $proveedor = $compra->proveedor;

        // 🔹 1. Revertir stock
        foreach ($compra->detalles as $detalle) {
            Producto::where('id', $detalle->producto_id)
                ->decrement('stock', $detalle->cantidad);
        }

        // 🔹 2. Eliminar movimiento de caja
        MovimientoCaja::where('concepto', 'like', 'Compra #' . $compra->id . '%')
            ->delete();

        // 🔹 3. Eliminar cuenta corriente asociada
        if ($compra->forma_pago_id == 2) {

            $proveedor->cuentaCorriente()
                ->where('compra_id', $compra->id)
                ->delete();

            // 🔹 4. Recalcular saldo real
            $proveedor->saldo = $proveedor->cuentaCorriente()
                ->sum(DB::raw('debe - haber'));

            $proveedor->save();
        }

        // 🔹 5. Eliminar compra
        $compra->detalles()->delete();
        $compra->delete();
    });

    return redirect()
        ->route('compras.index')
        ->with('success', 'Compra eliminada correctamente.');
}

    public function print($id)
    {
        $compra = Compra::with([
            "proveedor",
            "formaPago",
            "detalles.producto",
        ])->findOrFail($id);
        $pdf = Pdf::loadView("compras.boleta", compact("compra"));
        return $pdf->stream("compra_" . $compra->id . ".pdf");
    }
}
