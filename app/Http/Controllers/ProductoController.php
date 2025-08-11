<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $query = Producto::with('categoria');

        // Filtro por texto
        if ($request->filled('buscar')) {
            $busqueda = $request->buscar;
            $query->where(function ($q) use ($busqueda) {
                $q->where('nombre', 'like', "%$busqueda%")
                    ->orWhereHas('categoria', function ($q2) use ($busqueda) {
                        $q2->where('nombre', 'like', "%$busqueda%");
                    });
            });
        }

        // Filtro por categoría
        if ($request->filled('categoria_id') && $request->categoria_id != 'todas') {
            $query->where('categoria_id', $request->categoria_id);
        }

        $productos = $query->get();

        // Pasamos todas las categorías para el select
        $categorias = Categoria::all();

        return view('productos.index', compact('productos', 'categorias'));
    }

    public function create()
    {
        $categorias = Categoria::all();
        return view('productos.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        // Si es AJAX desde el modal, validaciones más flexibles
        if ($request->ajax()) {
            $request->validate([
                'nombre' => 'required|string|max:255',
                'categoria_id' => 'required|integer|exists:categorias,id',
                'precio_compra' => 'nullable|numeric|min:0',
                'precio_venta' => 'nullable|numeric|min:0',
                'stock' => 'nullable|integer|min:0',
                'imagen' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'codigo' => 'nullable|string|max:255|unique:productos,codigo',
            ]);
        } else {
            // Validación estricta para formulario normal
            $request->validate([
                'nombre' => 'required|string|max:255',
                'categoria_id' => 'required|integer|exists:categorias,id',
                'precio_compra' => 'required|numeric|min:0',
                'precio_venta' => 'nullable|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'imagen' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'codigo' => 'nullable|string|max:255|unique:productos,codigo',
            ]);
        }

        $data = $request->all();
        $data['precio_compra'] = $data['precio_compra'] ?? 0;
        $data['precio_venta']  = $data['precio_venta'] ?? 0;
        $data['stock']         = $data['stock'] ?? 0;

        // Guardar imagen si hay
        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $producto = Producto::create($data)->load('categoria');

        // Respuesta AJAX (para modal)
        if ($request->ajax()) {
            return response()->json([
                'success'  => true,
                'producto' => [
                    'id'            => $producto->id,
                    'nombre'        => $producto->nombre,
                    'categoria'     => $producto->categoria ? $producto->categoria->nombre : null,
                    'precio_compra' => $producto->precio_compra,
                    'precio_venta'  => $producto->precio_venta,
                    'stock'         => $producto->stock
                ]
            ]);
        }

        // Flujo normal
        return redirect()
            ->route('productos.index')
            ->with('success', 'Producto creado correctamente');
    }

    public function edit(Producto $producto)
    {
        $categorias = Categoria::all();
        return view('productos.edit', compact('producto', 'categorias'));
    }

    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'categoria_id' => 'required|integer|exists:categorias,id',
            'precio_compra' => 'nullable|numeric|min:0',
            'precio_venta' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'codigo' => 'nullable|string|max:255|unique:productos,codigo',
        ]);

        $data = $request->all();

        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior si existe
            if ($producto->imagen && Storage::disk('public')->exists($producto->imagen)) {
                Storage::disk('public')->delete($producto->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $producto->update($data);

        return redirect()->route('productos.index')->with('success', 'Producto actualizado correctamente');
    }

    public function destroy(Producto $producto)
    {
        // Eliminar imagen si existe
        if ($producto->imagen && Storage::disk('public')->exists($producto->imagen)) {
            Storage::disk('public')->delete($producto->imagen);
        }

        $producto->delete();

        return redirect()->route('productos.index')->with('success', 'Producto eliminado correctamente');
    }

    public function buscar(Request $request)
{
    $term = $request->get('term');

    $productos = Producto::where(function($q) use ($term) {
            $q->where('nombre', 'like', "%{$term}%")
              ->orWhere('codigo', 'like', "%{$term}%");
        })
        ->where('stock', '>', -1) // solo productos con stock
        ->get(['id', 'codigo', 'nombre', 'precio_venta', 'stock']);

    return response()->json($productos);
}

}
