<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Cliente;
use App\Models\Proveedor;

class DashboardController extends Controller
{
    public function index()
    {
        // Total ventas del mes (año y mes actual)
        $ventasMes = Venta::whereYear('fecha', now()->year)
            ->whereMonth('fecha', now()->month)
            ->sum('total');

        // Ventas día a día (del mes y año actual)
        $ventasPorDia = Venta::selectRaw("DAY(fecha) as dia, SUM(total) as total")
            ->whereYear('fecha', now()->year)
            ->whereMonth('fecha', now()->month)
            ->groupBy('dia')
            ->orderBy('dia')
            ->get();

        $ventasLabels = $ventasPorDia->pluck('dia');
        $ventasData   = $ventasPorDia->pluck('total');

        // Top 10 clientes deudores
        $topClientes = Cliente::select('*')
            ->selectSub(function($query) {
                $query->from('cuentas_corrientes')
                    ->selectRaw('SUM(saldo)')
                    ->whereColumn('clientes.id', 'cuentas_corrientes.entidad_id')
                    ->where('entidad_tipo', 'cliente');
            }, 'saldo')
            ->orderByDesc('saldo')
            ->take(10)
            ->get();

        // Top 10 proveedores a los que debemos
        $topProveedores = Proveedor::select('*')
            ->selectSub(function($query) {
                $query->from('cuentas_corrientes')
                    ->selectRaw('SUM(saldo)')
                    ->whereColumn('proveedores.id', 'cuentas_corrientes.entidad_id')
                    ->where('entidad_tipo', 'proveedor');
            }, 'saldo')
            ->orderByDesc('saldo')
            ->take(10)
            ->get();

        return view('dashboard', compact(
            'ventasMes',
            'ventasLabels',
            'ventasData',
            'topClientes',
            'topProveedores'
        ));
    }
}
