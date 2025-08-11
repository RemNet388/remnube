<?php

namespace App\Http\Controllers;

use App\Models\FormaPago;
use Illuminate\Http\Request;

class FormaPagoController extends Controller
{
    public function index()
{
    $formas_pago = FormaPago::all();
    return view('formas_pago.index', compact('formas_pago'));
}


    public function create()
    {
        return view('formas_pago.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        FormaPago::create($request->all());
        return redirect()->route('formas_pago.index')->with('success', 'Forma de pago creada correctamente');
    }

    public function edit(FormaPago $formasPago)
    {
        return view('formas_pago.edit', compact('formasPago'));
    }

    public function update(Request $request, FormaPago $formasPago)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        $formasPago->update($request->all());
        return redirect()->route('formas_pago.index')->with('success', 'Forma de pago actualizada correctamente');
    }

    public function destroy(FormaPago $formasPago)
    {
        $formasPago->delete();
        return redirect()->route('formas_pago.index')->with('success', 'Forma de pago eliminada correctamente');
    }
}
