<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\Request;

class FTiendaController extends Controller
{
    public function index(Request $request)
    {
        $query = Producto::query()
            ->where('stock', '>', 0);

        // Filtro por categoría
        if ($request->filled('categoria')) {
            $query->where('categoria_id', $request->categoria);
        }

        $productos = $query->orderBy('nombre')->paginate(12);

        $categorias = Categoria::orderBy('nombre')->get();

        return view('front.tienda.index', compact(
            'productos',
            'categorias'
        ));
    }
}
