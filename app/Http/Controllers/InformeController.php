<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Venta;
use App\Models\FormaPago;
use App\Models\Compra;
use App\Models\Proveedor;
use App\Models\MovimientoStock;
use App\Models\DetalleVenta;
use App\Models\MovimientoCaja;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Exports\ProductosExport;

class InformeController extends Controller
{
    public function index(Request $request)
{
// Mes actual según el filtro o hoy
$mesActual = $request->filled('desde')
    ? Carbon::parse($request->desde)->startOfMonth()
    : Carbon::now()->startOfMonth();

// Mes anterior
$mesAnterior = $mesActual->copy()->subMonth();

// Total mes actual (USANDO ventas.fecha)
$total = Venta::whereMonth('fecha', $mesActual->month)
    ->whereYear('fecha', $mesActual->year)
    ->sum('total');

// Total mes anterior
$totalMesAnterior = Venta::whereMonth('fecha', $mesAnterior->month)
    ->whereYear('fecha', $mesAnterior->year)
    ->sum('total');

    // 🔹 Ventas mensuales
    if (DB::getDriverName() === 'sqlite') {
        $ventasMensuales = MovimientoStock::select(
                DB::raw("strftime('%Y-%m', created_at) as mes"),
                DB::raw("SUM(cantidad) as total")
            )
            ->where('tipo', 'venta')
            ->whereBetween('created_at', [$desde, $hasta])
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();
    } else {
        $ventasMensuales = MovimientoStock::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as mes"),
                DB::raw("SUM(cantidad) as total")
            )
            ->where('tipo', 'venta')
            ->whereBetween('created_at', [$desde, $hasta])
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();
    }

    $ventasPorCategoria = MovimientoStock::select(
            'categorias.nombre as categoria',
            DB::raw('SUM(movimientos_stock.cantidad) as total')
        )
        ->join('productos', 'movimientos_stock.producto_id', '=', 'productos.id')
        ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
        ->where('movimientos_stock.tipo', 'venta')
        ->whereBetween('movimientos_stock.created_at', [$desde, $hasta])
        ->groupBy('categorias.nombre')
        ->orderByDesc('total')
        ->get();

    $masVendidos = MovimientoStock::select(
            'productos.nombre as producto',
            DB::raw('SUM(movimientos_stock.cantidad) as total')
        )
        ->join('productos', 'movimientos_stock.producto_id', '=', 'productos.id')
        ->where('tipo', 'venta')
        ->whereBetween('movimientos_stock.created_at', [$desde, $hasta])
        ->groupBy('productos.nombre')
        ->orderByDesc('total')
        ->take(8)
        ->get();

    return view('informes.index', compact(
        'ventasMensuales',
        'ventasPorCategoria',
        'masVendidos',
        'total',
        'totalMesAnterior',
        'desde',
        'hasta'
    ));
}




    // 📦 Informe de Stock
public function stock(Request $request)
{
    $productos = Producto::when($request->buscar, function($q, $buscar) {
            $q->where('nombre', 'like', '%'.$buscar.'%');
        })
        ->when($request->categoria_id, function($q, $categoria_id) {
            $q->where('categoria_id', $categoria_id);
        })
        ->orderBy('nombre')
        ->paginate(30)
        ->appends($request->only('buscar', 'categoria_id')); // mantiene filtros en la paginación

    $categorias = Categoria::orderBy('nombre')->get();

    return view('informes.stock', compact('productos', 'categorias'));
}


public function stockImprimir(Request $request)
{
    ini_set('memory_limit', '512M');
    ini_set('max_execution_time', 300);

    $query = \App\Models\Producto::orderBy('nombre');

    if ($request->filled('buscar')) {
        $query->where('nombre', 'like', '%' . $request->buscar . '%');
    }

    if ($request->filled('categoria_id')) {
        $query->where('categoria_id', $request->categoria_id);
    }

    // cursor() = eficiente en memoria
    $productos = $query->cursor();

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('informes.stock_imprimir', [
        'productos' => $productos,
        'filtros'   => $request->only('buscar','categoria_id')
    ])->setPaper('a4', 'landscape');

    return $pdf->stream('stock.pdf');
}


public function ventas(Request $request)
{
    // 📆 Filtros de fechas principales
    $desde = $request->desde ?? Carbon::now()->startOfMonth()->toDateString();
    $hasta = $request->hasta ?? Carbon::now()->endOfMonth()->toDateString();

    // 💰 Ventas en el período
    $ventas = Venta::with('formaPago')
        ->whereBetween('fecha', [$desde, $hasta])
        ->get();

    $total = $ventas->sum('total');
    $ventasPorDia = $ventas->groupBy('fecha')->map->sum('total');
    $ventasPorFormaPago = $ventas->groupBy(fn($v) => $v->formaPago->nombre ?? 'Sin definir')->map->sum('total');

    // 🗓 Mes actual y anterior (usando ventas.fecha)
$mesActual = Carbon::parse($desde)->startOfMonth();
$mesAnterior = $mesActual->copy()->subMonth();

$totalMesAnterior = Venta::whereMonth('fecha', $mesAnterior->month)
    ->whereYear('fecha', $mesAnterior->year)
    ->sum('total');

    // 🧾 --- NUEVO BLOQUE: Movimientos de Caja ---
    $movDesde = $request->mov_desde ?? $desde;
    $movHasta = Carbon::parse($request->mov_hasta ?? $hasta)->endOfDay();
    $formaPagoId = $request->forma_pago_id ?? 1; // por defecto contado

    $formasPago = FormaPago::orderBy('nombre')->get();

    $movimientos = MovimientoCaja::with('formaPago')
        ->whereBetween('created_at', [$movDesde, $movHasta])
        ->when($formaPagoId, fn($q) => $q->where('forma_pago_id', $formaPagoId))
        ->orderBy('created_at', 'desc')
        ->get();

    $totalIngresos = $movimientos->where('tipo', 'ingreso')->sum('monto');
    $totalEgresos = $movimientos->where('tipo', 'egreso')->sum('monto');

    // 📄 Devolvemos todo a la vista
    return view('informes.ventas', compact(
        'ventas',
        'total',
        'totalMesAnterior',
        'ventasPorDia',
        'ventasPorFormaPago',
        'desde',
        'hasta',
        'formasPago',
        'movimientos',
        'movDesde',
        'movHasta',
        'formaPagoId',
        'totalIngresos',
        'totalEgresos'
    ));
}

    public function ventasImprimir(Request $request)
    {
        $desde = $request->desde ?? Carbon::now()->startOfMonth()->toDateString();
        $hasta = $request->hasta ?? Carbon::now()->endOfMonth()->toDateString();

        $ventas = Venta::with('formaPago')
            ->whereBetween('fecha', [$desde, $hasta])
            ->get();

        $total = $ventas->sum('total');

        $pdf = Pdf::loadView('informes.pdf.ventas', compact('ventas', 'total', 'desde', 'hasta'));
        return $pdf->stream('informe_ventas.pdf');
    }

	public function stockEditable(Request $request)
{
    $query = Producto::orderBy('nombre');

    if ($request->filled('buscar')) {
        $query->where('nombre', 'like', '%' . $request->buscar . '%');
    }

    $productos = $query->paginate(15);

    return view('informes.stock_editable', compact('productos'));
}


    public function actualizarStockEditable(Request $request)
    {
        foreach ($request->productos as $id => $datos) {
            $producto = Producto::find($id);

            if ($producto) {
                // Actualizar stock y precio
                $producto->update([
   			 'stock' => $datos['stock'] ?? $producto->stock,
    			'precio_venta' => $datos['precio_venta'] ?? $producto->precio_venta,
    			'precio_compra' => $datos['precio_compra'] ?? $producto->precio_compra,
                ]);

                // Registrar ajuste en movimiento de stock
                MovimientoStock::create([
                    'producto_id' => $producto->id,
                    'tipo' => 'ajuste',
                    'cantidad' => $datos['stock'],
                    'descripcion' => 'Ajuste manual desde informes.editable'
                ]);
            }
        }

        return redirect()->route('informes.stock.editable')->with('success', 'Stock actualizado correctamente.');
    }


	public function stockEditablePDF()
	{
	    $productos = Producto::orderBy('nombre')->get();
	    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('informes.stock_pdf', compact('productos'));
	    return $pdf->stream('stock_editable.pdf');
	}

public function movimientosStock(Request $request)
{
    $desde = $request->input('desde', Carbon::now()->startOfMonth()->toDateString());
    $hasta = $request->input('hasta', Carbon::now()->endOfMonth()->toDateString());

    // Movimientos completos
    $movimientos = MovimientoStock::with('producto')
        ->whereBetween('created_at', [$desde, $hasta])
        ->orderBy('created_at', 'desc')
        ->get();

    // Productos más vendidos (TOP 10)
    $masVendidos = MovimientoStock::select('producto_id', DB::raw('SUM(cantidad) as total'))
        ->where('tipo', 'venta')
        ->whereBetween('created_at', [$desde, $hasta])
        ->groupBy('producto_id')
        ->with('producto')
        ->orderByDesc('total')
        ->take(10)
        ->get();

    // Evolución mensual de ventas
    if (DB::getDriverName() === 'sqlite') {
        $ventasMensuales = MovimientoStock::select(
                DB::raw("strftime('%Y-%m', created_at) as mes"),
                DB::raw("SUM(cantidad) as total")
            )
            ->where('tipo', 'venta')
            ->whereBetween('created_at', [$desde, $hasta])
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();
    } else {
        $ventasMensuales = MovimientoStock::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as mes"),
                DB::raw("SUM(cantidad) as total")
            )
            ->where('tipo', 'venta')
            ->whereBetween('created_at', [$desde, $hasta])
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();
    }

    // Distribución por categoría
$ventasPorCategoria = MovimientoStock::select(
        'categorias.nombre as categoria',
        DB::raw('SUM(movimientos_stock.cantidad) as total')
    )
    ->join('productos', 'movimientos_stock.producto_id', '=', 'productos.id')
    ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
    ->where('movimientos_stock.tipo', 'venta')
    ->whereBetween('movimientos_stock.created_at', [$desde, $hasta])
    ->groupBy('categorias.nombre')
    ->orderByDesc('total')
    ->get();

    return view('informes.movimientos_stock', compact(
        'movimientos',
        'masVendidos',
        'ventasMensuales',
        'ventasPorCategoria', // 👈 ahora sí
        'desde',
        'hasta'
    ));

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('informes.movimientos_stock_pdf', compact(
        'masVendidos', 'ventasMensuales', 'ventasPorCategoria', 'desde', 'hasta'
    ));

    return $pdf->stream('movimientos_stock.pdf');
}

public function movimientosStockPDF(Request $request)
{
    $desde = $request->input('desde', Carbon::now()->startOfMonth()->toDateString());
    $hasta = $request->input('hasta', Carbon::now()->endOfMonth()->toDateString());

    // Movimientos
    $movimientos = MovimientoStock::with('producto')
        ->whereBetween('created_at', [$desde, $hasta])
        ->orderBy('created_at', 'desc')
        ->get();

    // Top 5 productos más vendidos
    $masVendidos = MovimientoStock::select('producto_id', DB::raw('SUM(cantidad) as total'))
        ->where('tipo', 'venta')
        ->whereBetween('created_at', [$desde, $hasta])
        ->groupBy('producto_id')
        ->with('producto')
        ->orderByDesc('total')
        ->take(5)
        ->get();

    // Evolución mensual de ventas
    $ventasMensuales = MovimientoStock::select(
            DB::raw("strftime('%Y-%m', created_at) as mes"),
            DB::raw("SUM(cantidad) as total")
        )
        ->where('tipo', 'venta')
        ->whereBetween('created_at', [$desde, $hasta])
        ->groupBy('mes')
        ->orderBy('mes')
        ->get();

    // Distribución por categoría
    $ventasPorCategoria = MovimientoStock::where('tipo', 'venta')
        ->whereBetween('created_at', [$desde, $hasta])
        ->with('producto.categoria')
        ->get()
        ->groupBy(fn($m) => $m->producto->categoria->nombre ?? 'Sin categoría')
        ->map(fn($items, $categoria) => [
            'categoria' => $categoria,
            'total' => $items->sum('cantidad')
        ]);

    $pdf = Pdf::loadView('informes.movimientos_stock_pdf', compact(
        'desde', 'hasta', 'masVendidos', 'ventasMensuales', 'ventasPorCategoria'
    ));

    return $pdf->stream('movimientos_stock.pdf');
}

public function ganancias(Request $request)
{
    $query = DetalleVenta::query()->with(['producto', 'venta']);

    // Filtrar por fecha de venta
    if ($request->filled('fecha_desde') && $request->filled('fecha_hasta')) {
        $query->whereHas('venta', function($q) use ($request) {
            $q->whereBetween('fecha', [$request->fecha_desde, $request->fecha_hasta]);
        });
    }

    // Filtrar por producto
    if ($request->filled('producto_id')) {
        $query->where('producto_id', $request->producto_id);
    }

    $detalles = $query->get();

    // Calcular ganancia con precio de venta tomado de DetalleVenta
    $detalles->map(function($detalle) {
        $precioCompra = $detalle->producto->precio_compra ?? 0;
        $precioVenta  = $detalle->precio_unitario; // de la venta real
        $detalle->ganancia = ($precioVenta - $precioCompra) * $detalle->cantidad;
        return $detalle;
    });

    $totalGanancia = $detalles->sum('ganancia');

    $productos = Producto::orderBy('nombre')->get();

    return view('informes.ganancias', compact('detalles', 'totalGanancia', 'productos'));
}

public function cambiarCategoriaMasiva(Request $request)
{
    $request->validate([
        'productos' => 'required|array',
        'nueva_categoria' => 'required|integer|exists:categorias,id',
    ]);

    \App\Models\Producto::whereIn('id', $request->productos)
        ->update(['categoria_id' => $request->nueva_categoria]);

    return back()->with('success', 'Categoría actualizada para los productos seleccionados.');
}

    public function productosAComprar()
    {
        $productos = Producto::select(
                'productos.id',
                'productos.nombre',
                'productos.stock',
                'productos.precio_compra',
                DB::raw('COALESCE(SUM(detalle_ventas.cantidad),0) as total_vendido'),
                DB::raw('COALESCE(SUM(detalle_ventas.cantidad),0) / (COALESCE(SUM(detalle_ventas.cantidad),0) + productos.stock) as criticidad')
            )
            ->leftJoin('detalle_ventas', 'detalle_ventas.producto_id', '=', 'productos.id')
            ->where('productos.stock', '<', 100)
            ->groupBy('productos.id', 'productos.nombre', 'productos.stock', 'productos.precio_compra')
            ->orderByDesc('criticidad')
            ->limit(50) // traemos solo los 50 más críticos
            ->get();

        // Cargamos proveedores de las compras asociadas
        $productos->load(['detalleCompras.compra.proveedor']);

        return view('informes.productos_a_comprar', compact('productos'));
    }

    public function ventasPorVendedor(Request $request)
{
    $query = Venta::query()
        ->select('user_id', DB::raw('COUNT(*) as total_ventas'), DB::raw('SUM(total) as monto_total'))
        ->groupBy('user_id');

    // Filtrar por fechas si vienen
    if ($request->filled('fecha_desde')) {
        $query->whereDate('fecha', '>=', $request->fecha_desde);
    }
    if ($request->filled('fecha_hasta')) {
        $query->whereDate('fecha', '<=', $request->fecha_hasta);
    }

    $ventas = $query->with('usuario')->get();

    return view('informes.ventas_por_vendedor', compact('ventas'));
}

public function comprasPorProveedor(Request $request)
{
    // 📅 Fechas por defecto: mes en curso
    $desde = $request->filled('desde')
        ? Carbon::parse($request->desde)
        : now()->startOfMonth();

    $hasta = $request->filled('hasta')
        ? Carbon::parse($request->hasta)
        : now()->endOfMonth();

    $compras = \App\Models\Compra::with('proveedor')
        ->whereBetween('fecha', [$desde, $hasta])
        ->get();

    // Agrupar por proveedor y calcular totales
    $comprasPorProveedor = $compras
        ->groupBy('proveedor_id')
        ->map(function ($grupo) {
            return [
                'proveedor' => $grupo->first()->proveedor->nombre ?? 'Sin proveedor',
                'total' => $grupo->sum('total'),
                'cantidad' => $grupo->count(),
            ];
        })
        // 🔽 ordenar de mayor a menor por total
        ->sortByDesc('total');

    $totalGeneral = $compras->sum('total');

    return view('informes.compras_proveedor', compact(
        'comprasPorProveedor',
        'totalGeneral',
        'desde',
        'hasta'
    ));
}

}
