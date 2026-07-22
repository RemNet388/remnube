<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrdenServicio;

class OrdenServicioController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $ordenes = OrdenServicio::with(['cliente', 'marca', 'modelo'])
            ->when($search, function($query) use ($search) {
                $query->whereHas('cliente', fn($q) => $q->where('nombre', 'like', "%{$search}%"))
                      ->orWhere('numero', 'like', "%{$search}%");
            })
            ->orderByRaw("
                CASE 
                    WHEN estado = 'pendiente' THEN 1
                    WHEN estado = 'en_progreso' THEN 2
                    WHEN estado = 'finalizada' THEN 3
                END
            ")
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('ordenes.index', compact('ordenes', 'search'));
    }

    public function create()
    {
        return view('ordenes.create');
    }

        public function imprimir($id)
    {
        $orden = OrdenServicio::with(['cliente', 'marca'])->findOrFail($id);

        return view('ordenes.imprimir', compact('orden'));
    }

public function vistaImprimir($id)
{
    $orden = OrdenServicio::with(['cliente','marca','modelo'])->findOrFail($id);
    return view('ordenes.partials.vista-imprimir', compact('orden'));
}

}
