<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ClienteController, CategoriaController, FormaPagoController, ProductoController,
    ProveedorController, CompraController, DetalleCompraController, VentaController,
    DetalleVentaController, CajaDiariaController, MovimientoCajaController
};

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/productos/buscar', [ProductoController::class, 'buscar'])->name('productos.buscar');
Route::resource('clientes', ClienteController::class);
Route::resource('categorias', CategoriaController::class);
Route::resource('formas_pago', FormaPagoController::class);
Route::resource('productos', ProductoController::class);
Route::resource('proveedores', ProveedorController::class);
Route::resource('ventas', VentaController::class);
Route::resource('detalle-ventas', DetalleVentaController::class);
Route::resource('caja-diaria', CajaDiariaController::class);
Route::resource('movimientos-caja', MovimientoCajaController::class);
Route::resource('caja', CajaDiariaController::class);
Route::get('/ventas/{id}/print', [VentaController::class, 'print'])->name('ventas.print');
Route::get('/caja/actual', [CajaDiariaController::class, 'actual'])->name('caja.actual');
Route::get('/caja', [CajaDiariaController::class, 'index'])->name('caja.index');
Route::post('/caja/abrir', [CajaDiariaController::class, 'abrir'])->name('caja.abrir');
Route::resource('compras', CompraController::class);
Route::get('compras/{id}/print', [CompraController::class, 'print'])->name('compras.print');

Route::middleware(['auth', 'role:Administrador'])->group(function () {
    Route::resource('usuarios', UserController::class)->only(['index', 'edit', 'update']);
});

require __DIR__.'/auth.php';
