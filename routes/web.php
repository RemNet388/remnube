<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ClienteController, CategoriaController, FormaPagoController, ProductoController,
    ProveedorController, CompraController, DetalleCompraController, VentaController,
    DetalleVentaController, ProductoImportController, OrdenServicioController, CajaController,
    ProfileController, UserController, InformeController, RetiroController, PagoController, CuentaCorrienteController,
    MovimientoCajaController, StockEditableController, TransferenciaController
};
use App\Http\Controllers\FacturacionController;
use App\Livewire\{
    OrdenServicioIndex, OrdenServicioForm, OrdenServicioFEdit,
    MarcasCrud, ModelosCrud, ProductosIndex
};
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TurnoController;
use App\Http\Controllers\SeccionController;

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('secciones', SeccionController::class)
        ->parameters([
            'secciones' => 'seccion'
        ]);
});

//Front
use App\Http\Controllers\Front\FHomeController;
use App\Http\Controllers\Front\FTiendaController;
use App\Http\Controllers\Front\FProductoController;
use App\Http\Controllers\Front\FSeccionController;

//Route::get('/', [FHomeController::class, 'index']);
Route::get('/tienda', [FTiendaController::class, 'index'])->name('front.tienda');
Route::get('/front/producto/{id}', [FProductoController::class, 'show']);
Route::get('/front/seccion/{slug}', [FSeccionController::class, 'show'])->name('front.seccion');
Route::post('/contacto/enviar', [FSeccionController::class, 'enviar']);

Route::get('/', function () {
    if (env('TIENDA_ONLINE', false)) {
        // Si la tienda online está activa, mostrar el front
        return app(FHomeController::class)->index();
    } else {
        // Si no, ir al dashboard del admin
        return redirect()->route('dashboard');
    }
});

// 🔒 Todo lo demás requiere login
Route::middleware(['auth'])->group(function () {

    // 🔁 Transferencias entre formas de pago
    Route::get('/formas-pago/transferencias', [TransferenciaController::class, 'transferencias'])
        ->name('formas_pago.transferencias');

    Route::post('/formas-pago/transferir', [TransferenciaController::class, 'transferirFormaPago'])
        ->name('formas_pago.transferir');

    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Usuarios (solo admin)
    Route::resource('users', UserController::class)->middleware('role:admin');

    // Importación Excel
    Route::get('/productos/import', [ProductoImportController::class, 'showUploadForm'])->name('productos.import.form')->middleware('role:admin');
    Route::post('/productos/preview', [ProductoImportController::class, 'preview'])->name('productos.import.preview')->middleware('role:admin');
    Route::post('/productos/import/mapped', [ProductoImportController::class, 'importMapped'])->name('productos.import.mapped')->middleware('role:admin');

    // Productos
    Route::get('/productos/buscar', [ProductoController::class, 'buscar'])->name('productos.buscar');
    Route::get('/productos/{producto}/movimientos', [ProductoController::class, 'movimientos'])->name('productos.movimientos');
    Route::get('/productos/stock', ProductosIndex::class)->name('productos.stock');
    Route::resource('productos', ProductoController::class)->except(['show', 'create']);
    Route::get('/productos/create', [ProductoController::class, 'create'])->name('productos.create')->middleware('role:admin');
    Route::patch('/productos/{producto}/codigo', [ProductoController::class, 'actualizarCodigo'])->name('productos.actualizarCodigo');
    Route::get('/productos/por-vencer', [App\Http\Controllers\ProductoController::class, 'porVencer'])
    ->name('productos.por-vencer');


    // Clientes (ambos roles)
    Route::get('/clientes/deudores', [App\Http\Controllers\ClienteController::class, 'deudores'])
    ->name('clientes.deudores');

    Route::get('/clientes/nuevo', [ClienteController::class, 'create'])->name('clientes.nuevo');
    Route::resource('clientes', ClienteController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy', 'show']);

    
    // Cta cte
    Route::post('/cuentas_corrientes/{cliente}/pagar', [CuentaCorrienteController::class, 'pagar'])
    ->name('cuentas_corrientes.pagar');


    // Categorías (solo admin)
    Route::resource('categorias', CategoriaController::class)->middleware('role:admin');

    // Formas de pago (solo admin)
    Route::resource('formas_pago', FormaPagoController::class)
        ->parameters(['formas_pago' => 'forma_pago'])
        ->middleware('role:admin');

    // Proveedores (solo admin)
    Route::resource('proveedores', ProveedorController::class)
        ->parameters(['proveedores' => 'proveedor'])
        ->middleware('role:admin');
    Route::post('proveedores/{proveedor}/pagar', [CuentaCorrienteController::class, 'pagarProveedor'])->name('proveedores.pagar.store');
    Route::get('/proveedores/{proveedor}/cta-corriente', [ProveedorController::class, 'ctaCorrienteProveedor'])
    ->name('proveedores.cta_corriente');


    // Ventas (ambos roles)
    Route::get('ventas/rapida', [VentaController::class, 'ventaRapida'])->name('ventas.rapida');
    Route::post('ventas/storeRapida', [VentaController::class, 'storeRapida'])->name('ventas.storeRapida');
    Route::get('/ventas/{id}/print', [VentaController::class, 'print'])->name('ventas.print');
    Route::resource('ventas', VentaController::class);
    Route::resource('detalle-ventas', DetalleVentaController::class);

    // Caja (solo admin)
    Route::get('/cajas', [CajaController::class, 'index'])->name('cajas.index');
    Route::post('/cajas/{caja}/cerrar', [CajaController::class, 'cerrar'])->name('cajas.cerrar')->middleware('role:admin');
    Route::get('cajas/{caja}/detalle', [CajaController::class, 'detalle'])->name('cajas.detalle')->middleware('role:admin');
    Route::get('/movimientos/{movimiento}', [CajaController::class, 'detalleMovimiento']);
    Route::get('/movimientos/{movimiento}/detalle', [MovimientoCajaController::class, 'detalleModal'])->name('movimientos.detalleModal');
    Route::get('/cajas/historico', [CajaController::class, 'historico'])->name('cajas.historico');

    
    // Compras (ambos roles)
    Route::resource('compras', CompraController::class);
    Route::get('compras/{id}/print', [CompraController::class, 'print'])->name('compras.print');

    // Marcas y Modelos (ambos roles)
    Route::get('/marcas', MarcasCrud::class)->name('marcas.index');
    Route::get('/modelos', ModelosCrud::class)->name('modelos.index');

    // Ordenes de servicio (ambos roles)
    Route::get('/ordenes', [OrdenServicioController::class, 'index'])->name('ordenes.index');
    Route::get('/ordenes/create', OrdenServicioForm::class)->name('ordenes.create');
    Route::get('/ordenes/imprimir/{orden}', [OrdenServicioController::class, 'imprimir'])->name('ordenes.imprimir');
    Route::get('/ordenes/{id}/edit', \App\Livewire\OrdenServicioEdit::class)->name('ordenes.edit');
    Route::delete('/ordenes/{orden}', [OrdenServicioController::class, 'destroy'])->name('ordenes.destroy');
    Route::get('/ordenes/{orden}/vista-imprimir', [OrdenServicioController::class, 'vistaImprimir']);
    Route::get('/informes/stock-exportar-excel', [StockEditableController::class, 'exportarExcel'])
    ->name('informes.stock.exportar_excel');

    // Informes
    Route::prefix('informes')->group(function () {
        Route::get('/', [InformeController::class, 'index'])->name('informes.index');
        Route::get('/stock', [InformeController::class, 'stock'])->name('informes.stock');
        Route::get('/stock/imprimir', [InformeController::class, 'stockImprimir'])->name('informes.stock.imprimir');
        Route::get('/ventas', [InformeController::class, 'ventas'])->name('informes.ventas');
        Route::get('/ventas/imprimir', [InformeController::class, 'ventasImprimir'])->name('informes.ventas.imprimir');
    });
    Route::get('/informes/compras-proveedor', [InformeController::class, 'comprasPorProveedor'])
    ->name('informes.compras_proveedor');
    Route::post('/informes/cambiarCategoriaMasiva', [InformeController::class, 'cambiarCategoriaMasiva'])
    ->name('informes.cambiarCategoriaMasiva');
    Route::get('/informes/productos-a-comprar', [App\Http\Controllers\InformeController::class, 'productosAComprar'])
    ->name('informes.productos_a_comprar');
    Route::get('/informes/ventas-por-vendedor', [InformeController::class, 'ventasPorVendedor'])
    ->name('informes.ventas_por_vendedor');

    Route::post('/productos/{producto}/actualizar-campo', [ProductoController::class, 'actualizarCampo']);

    // Stock editable (solo admin)
    Route::get('/informes/stock-editable', [InformeController::class, 'stockEditable'])
        ->name('informes.stock_editable')->middleware('role:admin');
    Route::post('/informes/stock-editable', [InformeController::class, 'actualizarStockEditable'])
        ->name('informes.stock_editable.update')->middleware('role:admin');
    Route::get('/informes/stock-editable/pdf', [InformeController::class, 'stockEditablePDF'])
        ->name('informes.stock_editable.pdf')->middleware('role:admin');
    Route::get('/informes/ganancias', [InformeController::class, 'ganancias'])->name('informes.ganancias');


    Route::get('/informes/movimientos-stock', [InformeController::class, 'movimientosStock'])->name('informes.movimientos_stock');
    Route::get('/informes/movimientos-stock/pdf', [InformeController::class, 'movimientosStockPDF'])->name('informes.movimientos_stock.pdf');

    // Retiros y pagos (ambos roles)
    Route::resource('retiros', RetiroController::class)->only(['index','store']);
    Route::post('/pagos', [PagoController::class, 'store'])->name('pagos.store');
    Route::get('/pagos', [PagoController::class, 'index'])->name('pagos.index');
    Route::get('/pagos/formas', [PagoController::class, 'indexPorFormaPago'])->name('pagos.formas');

Route::get('/facturacion', function () {
    return view('facturacion');
});

Route::post('/facturar', [App\Http\Controllers\FacturacionController::class, 'emitir'])->name('facturar');

Route::get('/turnos', [TurnoController::class, 'index'])->name('turnos.index');
Route::post('/turnos/reservar', [TurnoController::class, 'reservar'])->name('turnos.reservar');
Route::post('/turnos/update-fecha-hora', [TurnoController::class, 'updateFechaHora'])->name('turnos.update-fecha-hora');
Route::delete('/turnos/{id}', [TurnoController::class, 'destroy'])->name('turnos.destroy');

});

// Auth scaffolding
require __DIR__.'/auth.php';
