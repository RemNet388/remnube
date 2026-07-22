<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Caja;
use App\Models\CajaDetalle;
use App\Models\FormaPago;
use App\Models\Venta;
use App\Models\Compra;
use App\Models\DetalleVenta;
use App\Models\Retiro;
use App\Models\Pago;
use App\Models\MovimientoCaja;
use Carbon\Carbon;

class CajaController extends Controller
{

public function index()
{
    // Obtener la caja abierta o crear una nueva
    $cajaAbierta = Caja::where('estado', 'abierta')->latest('fecha_apertura')->first();
    if (!$cajaAbierta) {
        $ultimoCierre = Caja::where('estado', 'cerrada')->latest('fecha_cierre')->first();
        $fondoInicial = $ultimoCierre->fondo_proximo ?? 0;

        $cajaAbierta = Caja::create([
            'fecha_apertura' => now(),
            'estado' => 'abierta',
            'monto_inicial' => $fondoInicial,
            'monto_final' => 0,
            'fondo_proximo' => 0,
        ]);
    }

    // Movimientos de la caja abierta
    $movimientosQuery = MovimientoCaja::where('caja_id', $cajaAbierta->id)->orderBy('created_at', 'desc');
    $movimientosCaja = $movimientosQuery->paginate(20);
    $movimientos = $movimientosQuery->get(); // Para cálculos totales

    // Calculamos saldo por forma de pago
    $formasPago = FormaPago::pluck('nombre', 'id')->toArray();
    $saldosPorFormaPago = [];
    foreach ($formasPago as $fpId => $nombre) {
        $ingresos = $movimientos->where('forma_pago_id', $fpId)->where('tipo', 'ingreso')->sum('monto');
        $egresos = $movimientos->where('forma_pago_id', $fpId)->where('tipo', 'egreso')->sum('monto');
        $saldosPorFormaPago[$fpId] = $ingresos - $egresos;
    }

    // Total de la caja excluyendo cuentas corrientes (fp_id = 2)
    $totalCajaActual = collect($saldosPorFormaPago)->except(2)->sum() + (float) $cajaAbierta->monto_inicial;

    // Total retiros y pagos (no tocar)
    $retiros = Retiro::orderBy('fecha', 'desc')->get();
    $pagos = Pago::orderBy('fecha', 'desc')->get();
    $totalDisponible = $retiros->sum('monto') - $pagos->sum('monto');

    return view('cajas.index', compact(
        'cajaAbierta',
        'totalCajaActual',
        'saldosPorFormaPago',
        'formasPago',
        'totalDisponible',
        'movimientosCaja'
    ));
}

public function cerrar(Request $request, Caja $caja)
{
    // ===============================
    // 1. AGRUPAR VENTAS POR FORMA DE PAGO
    // ===============================
    $ventas = $caja->ventas()->get();
    $totalesVentas = $ventas->groupBy('forma_pago_id')
                            ->map(fn($v) => $v->sum('total'));

    foreach ($totalesVentas as $formaPagoId => $monto) {
        CajaDetalle::create([
            'caja_id' => $caja->id,
            'forma_pago_id' => $formaPagoId,
            'monto' => $monto,
        ]);
    }

    // ===============================
    // 2. EFECTIVO REAL (FORMA DE PAGO = 1)
    // ===============================
    $efectivoId = 1;
    $movimientos = $caja->movimientos()->get();

    $ingresosEfectivo = (float) $movimientos
        ->where('forma_pago_id', $efectivoId)
        ->where('tipo', 'ingreso')
        ->sum('monto');

    $egresosEfectivo = (float) $movimientos
        ->where('forma_pago_id', $efectivoId)
        ->where('tipo', 'egreso')
        ->sum('monto');

    // ===============================
    // 2.b Incluir monto inicial de caja
    // ===============================
    $montoInicial = (float) $caja->monto_inicial;

    // EFECTIVO DISPONIBLE REAL = monto_inicial + ingresos en efectivo - egresos en efectivo
    $efectivoDisponible = $montoInicial + $ingresosEfectivo - $egresosEfectivo;

    // ===============================
    // 3. FONDO PARA LA PRÓXIMA CAJA
    // ===============================
    $fondoProximo = (float) $request->input('fondo_proximo', 0);

    // ===============================
    // 4. RETIRO
    // ===============================
    $montoRetiro = max(0, round($efectivoDisponible - $fondoProximo, 2));

    // ===============================
    // 5. CERRAR LA CAJA
    // ===============================
    // NOTA: monto_final debe reflejar el efectivo disponible total (ya incluye monto_inicial)
    $caja->update([
        'fecha_cierre' => now(),
        'estado' => 'cerrada',
        'monto_final' => $efectivoDisponible,
        'fondo_proximo' => $fondoProximo,
    ]);

    // ===============================
    // 6. GUARDAR RETIRO
    // ===============================
    $fechaRetiro = Carbon::parse($caja->fecha_apertura)->toDateString();

    Retiro::updateOrCreate(
        ['fecha' => $fechaRetiro],
        [
            'monto' => $montoRetiro,
            'dejar_para_siguiente_caja' => $fondoProximo,
        ]
    );

    return redirect()->route('cajas.index')
                     ->with('success', 'Caja cerrada.');
}

public function detalle(Caja $caja)
{
    // Ventas de esa caja
    $ventas = \App\Models\Venta::with(['cliente', 'formaPago'])
                ->where('caja_id', $caja->id)
                ->orderBy('id', 'desc')
                ->get();

    // Compras de esa caja
    $compras = \App\Models\Compra::with(['proveedor', 'formaPago'])
                ->where('caja_id', $caja->id)
                ->orderBy('id', 'desc')
                ->get();

    // Totales por forma de pago (ventas)
    $totalesVentasPorFP = $ventas->groupBy('forma_pago_id')
                                 ->map(fn($grupo) => $grupo->sum('total'));

    // Totales por forma de pago (compras)
    $totalesComprasPorFP = $compras->groupBy('forma_pago_id')
                                   ->map(fn($grupo) => $grupo->sum('total'));

    // Total neto de la caja (ventas - compras)
    $totalCaja = $ventas->sum('total') - $compras->sum('total');

    return view('cajas.detalle', compact(
        'caja',
        'ventas',
        'compras',
        'totalesVentasPorFP',
        'totalesComprasPorFP',
        'totalCaja'
    ));
}

public function detalleMovimiento(MovimientoCaja $movimiento)
{
    if ($movimiento->tipo === 'venta') {
        $venta = \App\Models\Venta::with('detalles.producto', 'cliente')
            ->where('id', $movimiento->operacion_id) // suponer que guardamos la venta_id
            ->first();

        return view('cajas.partials.detalle_venta', compact('venta'));
    }

    if ($movimiento->tipo === 'compra') {
        $compra = \App\Models\Compra::with('detalles.producto', 'proveedor')
            ->where('id', $movimiento->operacion_id)
            ->first();

        return view('cajas.partials.detalle_compra', compact('compra'));
    }

    // Otros tipos de movimientos
    return "<p>Tipo de movimiento: {$movimiento->tipo}</p><p>Concepto: {$movimiento->concepto}</p><p>Monto: {$movimiento->monto}</p>";
}

public function historico()
{
    $cajas = Caja::orderBy('fecha_apertura', 'desc')->paginate(15);

    return view('cajas.historico', compact('cajas'));
}

}
