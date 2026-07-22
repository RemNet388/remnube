<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use App\Models\FormaPago;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{

    public function index(Request $request)
{
    $q = $request->input('buscar');
    $formasPago = FormaPago::all();

    $proveedores = Proveedor::when($q, function ($query, $q) {
            $query->where('nombre', 'like', "%$q%")
                  ->orWhere('email', 'like', "%$q%")
                  ->orWhere('telefono', 'like', "%$q%");
        })
        ->orderBy('nombre')
        ->paginate(25) // 👈 25 por página
        ->appends(['buscar' => $q]); // mantiene el parámetro en la URL

    return view('proveedores.index', compact('proveedores', 'formasPago'));
}
 
    public function create()
    {
        return view('proveedores.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'cuit' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        Proveedor::create($request->all());
        return redirect()->route('proveedores.index')->with('success', 'Proveedor creado correctamente');
    }

    public function edit(Proveedor $proveedor)
{
    return view('proveedores.edit', compact('proveedor'));
}

public function update(Request $request, Proveedor $proveedor)
{
    $request->validate([
        'nombre' => 'required|string|max:255',
        'cuit' => 'nullable|string|max:20',
        'direccion' => 'nullable|string|max:255',
        'telefono' => 'nullable|string|max:20',
        'email' => 'nullable|email|max:255',
    ]);

    $proveedor->update($request->all());
    return redirect()->route('proveedores.index')->with('success', 'Proveedor actualizado correctamente');
}

public function destroy(Proveedor $proveedor)
{
    $proveedor->delete();
    return redirect()->route('proveedores.index')->with('success', 'Proveedor eliminado correctamente');
}

public function pagarStore(Request $request, Proveedor $proveedor)
{
    $request->validate([
        'monto' => 'required|numeric|min:0.01',
        'forma_pago_id' => 'required|exists:formas_pagos,id',
        'comentario' => 'nullable|string|max:255',
    ]);

    DB::transaction(function() use ($request, $proveedor) {
        // Aquí podés registrar el pago, ejemplo con cuentas corrientes:
        $proveedor->cuentaCorriente()->create([
            'fecha' => now(),
            'tipo' => 'pago',
            'monto' => $request->monto,
            'saldo' => $proveedor->saldo - $request->monto,
            'concepto' => 'Pago a proveedor',
            'forma_pago_id' => $request->forma_pago_id,
            'comentario' => $request->comentario,
        ]);

        // Actualizar saldo del proveedor
        $proveedor->saldo -= $request->monto;
        $proveedor->save();
    });

    return redirect()->route('proveedores.index')->with('success', 'Pago realizado correctamente');
}

public function ctaCorrienteProveedor($proveedorId)
{
    // Traemos el proveedor
    $proveedor = Proveedor::findOrFail($proveedorId);

    // Movimientos de su cuenta corriente
    $movimientos = $proveedor->cuentaCorriente()
        ->orderByRaw('id DESC')
        ->paginate(20); // paginación, 20 por página

    return view('proveedores.cta_corriente', compact('proveedor', 'movimientos'));
}


}
