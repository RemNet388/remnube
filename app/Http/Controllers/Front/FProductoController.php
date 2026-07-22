<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Producto;

class FProductoController extends Controller
{
    public function show($id)
    {
        $producto = Producto::where('id', $id)
            ->where('stock', '>', 0)
            ->firstOrFail();

        return view('front.productos.show', compact('producto'));
    }
}
