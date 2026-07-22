<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Proveedor;
use App\Models\Caja;
use App\Models\MovimientoCaja;
use App\Models\FormaPago;
use Illuminate\Http\Request;

class CuentaCorrienteController extends Controller
{
    // Registrar pago parcial o total de cuenta corriente de cliente
    public function pagar(Request $request, Cliente $cliente)
{
    $saldoActual = $cliente->cuentaCorriente()->latest('id')->value('saldo') ?? 0;

    $request->validate([
            'monto' => ['required', 'numeric', 'min:0.01'],
        'comentario' => ['nullable','string']
    ]);

    $monto = $request->input('monto');
    $formaPagoId = $request->input('forma_pago_id');
    $comentario = $request->input('comentario');

    $nuevoSaldo = $saldoActual - $monto;

    // Registro en cuenta corriente
    $cliente->cuentaCorriente()->create([
        'fecha' => now(),
        'tipo' => 'pago',
        'debe' => 0,
        'haber' => $monto,
        'saldo' => $nuevoSaldo,
        'concepto' => $comentario ?: 'Pago parcial de cuenta corriente',
        'forma_pago_id' => $formaPagoId,
        'entidad_tipo' => 'cliente',
    ]);

    // 💰 Registrar movimiento en caja (si hay caja abierta)
    $cajaAbierta = Caja::where('estado', 'abierta')->latest('fecha_apertura')->first();
    if ($cajaAbierta) {
        MovimientoCaja::create([
            'caja_id' => $cajaAbierta->id,
            'tipo' => 'ingreso', // ✅ porque entra dinero del cliente
            'concepto' => 'Pago Cta Cte - Cliente: ' . $cliente->nombre . ($comentario ? " - $comentario" : ""),
            'monto' => $monto,
            'forma_pago_id' => $formaPagoId,
        ]);
    }

    return redirect()->route('clientes.index')
                     ->with('success','Pago registrado correctamente.');
}


    // Registrar pago a proveedor
    public function pagarProveedor(Request $request, Proveedor $proveedor)
    {
        $saldoActual = $proveedor->cuentaCorriente()->latest('id')->value('saldo') ?? 0;

        $request->validate([
            'monto' => ['required','numeric','min:0.01'],
            'forma_pago_id' => ['required','exists:formas_pago,id'],
            'comentario' => ['nullable','string']
        ]);

        $monto = $request->input('monto');
        $formaPagoId = $request->input('forma_pago_id');
        $comentario = $request->input('comentario');

        $nuevoSaldo = $saldoActual - $monto;

        // Registrar en cuenta corriente del proveedor
        $proveedor->cuentaCorriente()->create([
            'fecha' => now(),
            'tipo' => 'pago',
            'debe' => 0,
            'haber' => $monto,
            'saldo' => $nuevoSaldo,
            'concepto' => $comentario ?: 'Pago a proveedor',
            'forma_pago_id' => $formaPagoId,
            'entidad_tipo' => 'proveedor',
        ]);

        // Actualizar saldo del proveedor
        $proveedor->saldo = $nuevoSaldo;
        $proveedor->save();

        // Registrar egreso en caja
        $cajaAbierta = Caja::where('estado','abierta')->latest('fecha_apertura')->first();
        if ($cajaAbierta) {
            MovimientoCaja::create([
                'caja_id' => $cajaAbierta->id,
                'tipo' => 'egreso',
                'concepto' => 'Pago a Proveedor: ' . $proveedor->nombre . ($comentario ? " - $comentario" : ""),
                'monto' => $monto,
                'forma_pago_id' => $formaPagoId,
            ]);
        }

        return redirect()->route('proveedores.index')
                         ->with('success','Pago registrado correctamente.');
    }
}
