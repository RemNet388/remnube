<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Producto;

class FHomeController extends Controller
{
public function index()
{
    $destacados = Producto::where('stock', '>', 0)
        ->orderBy('created_at', 'desc')
        ->limit(12)
        ->get();

    return view('front.index', compact('destacados'));
}
}
