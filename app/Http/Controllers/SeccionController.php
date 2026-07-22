<?php

namespace App\Http\Controllers;

use App\Models\Seccion;
use Illuminate\Http\Request;

class SeccionController extends Controller
{
    public function index()
    {
        $secciones = Seccion::orderBy('orden')->get();
        return view('secciones.index', compact('secciones'));
    }

    public function create()
    {
        return view('secciones.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'slug' => 'required|unique:secciones,slug',
            'titulo' => 'required|string|max:255',
            'contenido' => 'nullable|string',
            'activo' => 'nullable|boolean',
            'orden' => 'nullable|integer'
        ]);

        Seccion::create($validated);

        return redirect()->route('secciones.index')
                         ->with('success', 'Sección creada correctamente');
    }

    public function edit(Seccion $seccion)
    {
        return view('secciones.edit', compact('seccion'));
    }

    public function update(Request $request, Seccion $seccion)
    {
        $validated = $request->validate([
            'slug' => 'required|unique:secciones,slug,' . $seccion->id,
            'titulo' => 'required|string|max:255',
            'contenido' => 'nullable|string',
            'activo' => 'nullable|boolean',
            'orden' => 'nullable|integer'
        ]);

        $seccion->update($validated);

        return redirect()->route('secciones.index')
                         ->with('success', 'Sección actualizada correctamente');
    }

    public function destroy(Seccion $seccion)
    {
        $seccion->delete();
        return redirect()->route('secciones.index')
                         ->with('success', 'Sección eliminada correctamente');
    }
}
