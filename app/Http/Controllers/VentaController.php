<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\MovimientoCaja;
use App\Models\Caja;
use App\Models\Cliente;
use App\Models\MovimientoStock;
use App\Models\CuentaCorriente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class VentaController extends Controller
{
    public function index(Request $request)
    {
        $query = Venta::with("cliente", "formaPago")
              ->orderBy("fecha", "desc")
              ->orderBy("id", "desc"); // si hay facturas con misma fecha

        if ($request->filled("buscar")) {
            $buscar = $request->input("buscar");
            $query
                ->whereHas("cliente", function ($q) use ($buscar) {
                    $q->where("nombre", "like", "%{$buscar}%");
                })
                ->orWhere("id", $buscar); // Buscar por ID exacto
        }

        $ventas = $query->paginate(25)->withQueryString(); // Mantener query en la paginación

        return view("ventas.index", compact("ventas"));
    }

    public function create()
    {
        $clientes = \App\Models\Cliente::orderByRaw('id = 1 DESC') // el 1 primero
                                ->orderBy('nombre', 'asc')  // luego por nombre
                                ->get();
        $productos = Producto::all();
        $formasPago = \App\Models\FormaPago::activas()->get();
        return view(
            "ventas.create",
            compact("clientes", "productos", "formasPago")
        );
    }

    public function store(Request $request)
    {
        // Obtener la caja abierta del día
        $cajaAbierta = Caja::where("estado", "abierta")
            ->latest("fecha_apertura")
            ->first();

        if (!$cajaAbierta) {
            // Si no existe una caja abierta, crearla automáticamente
            $cajaAbierta = Caja::create([
                "fecha_apertura" => now(),
                "estado" => "abierta",
                "monto_inicial" => 0,
            ]);
        }

        // Validar
        $request->validate([
            "fecha" => "required|date",
            "cliente_id" => "required|exists:clientes,id",
            "forma_pago_id" => "required",
            "observaciones" => "nullable|string",
            "productos" => "required|array|min:1",
            "productos.*.id" => "required|exists:productos,id",
            "productos.*.cantidad" => "required|integer|min:1",
            "productos.*.precio_venta" => "required|numeric|min:0",
        ]);

        DB::beginTransaction();
        try {
            // Crear venta
            $venta = Venta::create([
                "fecha" => $request->fecha,
                "cliente_id" => $request->cliente_id,
                "forma_pago_id" => $request->forma_pago_id,
                "observaciones" => $request->observaciones,
                "caja_id" => $cajaAbierta->id,
                "user_id" => auth()->id(),
                "total" => collect($request->productos)->sum(function ($prod) {
                    return $prod["cantidad"] * $prod["precio_venta"];
                }),
            ]);

            // Guardar detalles
            foreach ($request->productos as $prod) {
                // Crear detalle de venta
                DetalleVenta::create([
                    "venta_id" => $venta->id,
                    "producto_id" => $prod["id"],
                    "cantidad" => $prod["cantidad"],
                    "precio_unitario" => $prod["precio_venta"],
                    "subtotal" => $prod["cantidad"] * $prod["precio_venta"],
                ]);

                // Buscar el producto real
                $producto = Producto::find($prod["id"]);

                if ($producto) {
                    // Actualizar stock
                    $producto->decrement("stock", $prod["cantidad"]);

                    // Registrar movimiento de stock
                    MovimientoStock::create([
                        "producto_id" => $producto->id,
                        "tipo" => "venta",
                        "cantidad" => $prod["cantidad"],
                        "descripcion" => "Venta #" . $venta->id,
                    ]);
                }
            }

            MovimientoCaja::create([
                "caja_id" => $cajaAbierta->id,
                "tipo" => "ingreso",
                "concepto" => "Venta #" . $venta->id,
                "monto" => $venta->total,
                "forma_pago_id" => $request->forma_pago_id,
            ]);

            if ($venta->forma_pago_id == 2) {
                // calcular el saldo anterior
                $saldoAnterior =
                    CuentaCorriente::where("entidad_id", $venta->cliente_id)
                        ->where("entidad_tipo", "cliente")
                        ->orderByDesc("id")
                        ->value("saldo") ?? 0;

                // nuevo saldo
                $nuevoSaldo = $saldoAnterior + $venta->total;

                // insertar movimiento
                CuentaCorriente::create([
                    "entidad_id" => $venta->cliente_id,
                    "entidad_tipo" => "cliente",
                    "fecha" => now(),
                    "concepto" => "Venta #" . $venta->id,
                    "debe" => $venta->total, // cargo al cliente
                    "haber" => 0,
                    "saldo" => $nuevoSaldo,
                ]);
            }

            DB::commit();

            return redirect()
                ->route("ventas.index")
                ->with("success", "Venta registrada correctamente.");
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage(), $e->getTraceAsString());
        }
    }

    public function ventaRapida()
    {
        $formasPago = \App\Models\FormaPago::activas()->get();
        $clientes = Cliente::where("id", "!=", 1)->get(); // excluimos consumidor final

        return view("ventas.rapida", compact("formasPago", "clientes"));
    }

    public function storeRapida(Request $request)
    {
        // Obtener la caja abierta del día
        $cajaAbierta = Caja::where("estado", "abierta")
            ->latest("fecha_apertura")
            ->first();

        if (!$cajaAbierta) {
            // Si no existe una caja abierta, crearla automáticamente
            $cajaAbierta = Caja::create([
                "fecha_apertura" => now(),
                "estado" => "abierta",
                "monto_inicial" => 0,
            ]);
        }

        // Validación
        $request->validate([
            "forma_pago_id" => "required|exists:formas_pago,id",
            "productos" => "required|array|min:1",
            "productos.*.id" => "required|exists:productos,id",
            "productos.*.cantidad" => "required|integer|min:1",
            "productos.*.precio_venta" => "required|numeric|min:0",
        ]);

        // Debug: log de request
        \Log::info("Venta rápida request:", $request->all());

        DB::beginTransaction();

        try {
            \Log::info("Intentando guardar venta rápida", $request->all());

            // Crear la venta
            $venta = Venta::create([
                "fecha" => now(),
                "observaciones" => $request->observaciones,
                "cliente_id" => $request->cliente_id ?? null,
                "forma_pago_id" => $request->forma_pago_id,
                "caja_id" => $cajaAbierta->id,
                "user_id" => auth()->id(),
                "total" => collect($request->productos)->sum(
                    fn($p) => $p["cantidad"] * $p["precio_venta"]
                ),
            ]);

            // Guardar los detalles de la venta y actualizar stock
            foreach ($request->productos as $prod) {
                DetalleVenta::create([
                    "venta_id" => $venta->id,
                    "producto_id" => $prod["id"],
                    "cantidad" => $prod["cantidad"],
                    "precio_unitario" => $prod["precio_venta"],
                    "subtotal" => $prod["cantidad"] * $prod["precio_venta"],
                ]);

                $producto = Producto::find($prod["id"]);
                if ($producto) {
                    $producto->decrement("stock", $prod["cantidad"]);

                    MovimientoStock::create([
                        "producto_id" => $producto->id,
                        "tipo" => "venta",
                        "cantidad" => $prod["cantidad"],
                        "descripcion" => "Venta rápida #" . $venta->id,
                    ]);
                }
            }

            MovimientoCaja::create([
                "caja_id" => $cajaAbierta->id,
                "tipo" => "ingreso",
                "concepto" => "Venta #" . $venta->id,
                "monto" => $venta->total,
                "forma_pago_id" => $request->forma_pago_id,
            ]);

            if ($venta->forma_pago_id == 2) {
                // calcular el saldo anterior
                $saldoAnterior =
                    CuentaCorriente::where("entidad_id", $venta->cliente_id)
                        ->where("entidad_tipo", "cliente")
                        ->orderByDesc("id")
                        ->value("saldo") ?? 0;

                // nuevo saldo
                $nuevoSaldo = $saldoAnterior + $venta->total;

                // insertar movimiento
                CuentaCorriente::create([
                    "entidad_id" => $venta->cliente_id,
                    "entidad_tipo" => "cliente",
                    "fecha" => now(),
                    "concepto" => "Venta #" . $venta->id,
                    "debe" => $venta->total, // cargo al cliente
                    "haber" => 0,
                    "saldo" => $nuevoSaldo,
                ]);
            }

            DB::commit();

            return redirect()
                ->route("ventas.rapida")
                ->with("success", "Venta rápida realizada correctamente ✅");
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error al guardar venta rápida: " . $e->getMessage(), [
                "trace" => $e->getTraceAsString(),
                "request" => $request->all(),
            ]);

            return back()->withErrors(["error" => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $venta = Venta::with("detalles")->findOrFail($id);

            // 1️⃣ Devolver stock
            foreach ($venta->detalles as $detalle) {
                $producto = Producto::find($detalle->producto_id);
                $producto->stock += $detalle->cantidad;
                $producto->save();
            }

            // 2️⃣ Eliminar movimiento de caja
            MovimientoCaja::where("concepto", "Venta #" . $venta->id)->delete();

            // 3️⃣ Eliminar detalles y venta
            $venta->detalles()->delete();
            $venta->delete();
        });

        return redirect()
            ->route("ventas.index")
            ->with("success", "Venta eliminada y stock devuelto correctamente");
    }

    public function show($id)
    {
        $venta = Venta::with([
            "cliente",
            "formaPago",
            "detalles.producto",
        ])->findOrFail($id);
        return view("ventas.show", compact("venta"));
    }

    public function print($id)
    {
        $venta = Venta::with([
            "cliente",
            "formaPago",
            "detalles.producto",
        ])->findOrFail($id);

        $pdf = Pdf::loadView("ventas.boleta", compact("venta"));
        return $pdf->stream("venta_" . $venta->id . ".pdf");
    }
}
