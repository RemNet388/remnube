<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Seccion;
use Illuminate\Http\Request;

class FSeccionController extends Controller
{
    // Mostrar sección por slug
    public function show($slug)
    {
        $seccion = Seccion::where('slug', $slug)
                          ->where('activo', 1)
                          ->firstOrFail();

        return view('front.seccion', compact('seccion'));
    }

    public function enviar(Request $request)
    {
        $data = $request->validate([
            'nombre'   => 'required|string|max:255',
            'email'    => 'required|email',
            'telefono' => 'nullable|string|max:50',
            'mensaje'  => 'required|string',
        ]);

        /*
         | OPCIÓN 1 (RECOMENDADA): ENVIAR A WHATSAPP
         | Simple y efectivo para tiendas
         */
        $texto = urlencode(
            "Nuevo contacto desde la web:\n\n".
            "Nombre: {$data['nombre']}\n".
            "Email: {$data['email']}\n".
            "Teléfono: {$data['telefono']}\n\n".
            "Mensaje:\n{$data['mensaje']}"
        );

        return back()->with('success', 'Mensaje enviado correctamente. Te responderemos a la brevedad.');
    }

}
