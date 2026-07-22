<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\MovimientoStock;
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

    // Filtro stock
    if ($request->boolean('solo_stock', false)) {
        $query->where('stock', '>', 0);
    }

    // 👇 Orden dinámico
    $sort = $request->get('sort', 'nombre'); // campo por defecto
    $direction = $request->get('direction', 'asc'); // dirección por defecto

    if ($sort === 'categoria') {
        $query->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
              ->select('productos.*', 'categorias.nombre as categoria_nombre')
              ->orderBy('categoria_nombre', $direction);
    } else {
        $query->orderBy($sort, $direction);
    }

    $productos = $query->paginate(40)->appends($request->all());

    $categorias = Categoria::orderBy('nombre', 'asc')->get();

    return view('productos.index', compact('productos', 'categorias'));
}

    public function create()
    {
        $categorias = Categoria::orderBy('nombre', 'asc')->get();
        return view('productos.create', compact('categorias'));
    }

    public function store(Request $request)
{
    // Determinar si la solicitud espera JSON (AJAX)
    $isAjax = $request->wantsJson() || $request->ajax();

    // Validación de los datos
    $rules = [
        'nombre' => 'required|string|max:255',
        'descripcion' => 'nullable|string',
        'categoria_id' => 'required|integer|exists:categorias,id',
        'precio_compra' => $isAjax ? 'nullable|numeric|min:0' : 'required|numeric|min:0',
        'precio_venta' => 'nullable|numeric|min:0',
        'stock' => $isAjax ? 'nullable|integer|min:0' : 'required|integer|min:0',
        'imagen' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'codigo' => 'nullable|string|max:255|unique:productos,codigo',
        'fecha_vencimiento' => 'nullable|date',
    ];

    $validated = $request->validate($rules);

    // Valores por defecto si vienen vacíos
    $validated['precio_compra'] = $validated['precio_compra'] ?? 0;
    $validated['precio_venta'] = $validated['precio_venta'] ?? 0;
    $validated['stock'] = $validated['stock'] ?? 0;

    // Guardar imagen si hay
    if ($request->hasFile('imagen')) {
        $validated['imagen'] = $request->file('imagen')->store('productos', 'public');
    }

    // Crear producto
    $producto = Producto::create($validated)->load('categoria');

    if ($isAjax) {
        // Respuesta JSON para modal
        return response()->json([
            'success' => true,
            'producto' => [
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'categoria' => $producto->categoria ? $producto->categoria->nombre : null,
                'precio_compra' => $producto->precio_compra,
                'precio_venta' => $producto->precio_venta,
                'stock' => $producto->stock,
                'fecha_vencimiento' => $producto->fecha_vencimiento,
            ]
        ]);
    }

    // Flujo normal: redirigir con mensaje de éxito
    return redirect()
        ->route('productos.index')
        ->with('success', 'Producto creado correctamente');
}

    public function edit(Producto $producto)
    {
        $categorias = Categoria::orderBy('nombre', 'asc')->get();
        return view('productos.edit', compact('producto', 'categorias'));
    }

    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'categoria_id' => 'required|integer|exists:categorias,id',
            'precio_compra' => 'nullable|numeric|min:0',
            'precio_venta' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'fecha_vencimiento' => 'nullable|date',
            'codigo' => 'nullable|string|max:255|unique:productos,codigo,' . $producto->id,
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

    // 👇 Reenviar todos los filtros (query string)
    return redirect()
        ->route('productos.index', $request->query())
        ->with('success', 'Producto actualizado correctamente');

    }

public function movimientos(Producto $producto)
{
    $movimientos = $producto->movimientos()
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function($m) {
            return [
                'fecha' => $m->created_at->format('d/m/Y H:i'),
                'tipo' => ucfirst($m->tipo),
                'cantidad' => $m->cantidad,
                'descripcion' => $m->descripcion ?? '-'
            ];
        });

    return response()->json($movimientos);
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
    $term = trim($request->get('term'));

    if (strlen($term) < 2) {
        // Evitamos consultas con 1 sola letra que son pesadas
        return response()->json([]);
    }

    $productos = Producto::query()
        ->where(function($q) use ($term) {
            $q->where('nombre', 'like', "%{$term}%")
              ->orWhere('codigo', 'like', "%{$term}%");
        })
        ->select('id', 'codigo', 'nombre', 'precio_venta', 'precio_compra', 'stock')
        ->orderBy('nombre')
        ->limit(20) // ⚡ trae solo 20 resultados
        ->get();

    return response()->json($productos);
}


public function actualizarCodigo(Request $request, Producto $producto)
{
    // Validar el nuevo código
    $request->validate([
        'codigo' => 'required|string|max:255|unique:productos,codigo,' . $producto->id,
    ]);

    // Actualizar el código
    $producto->codigo = $request->codigo;
    $producto->save();

    // Redirigir con mensaje
    return redirect()->back()->with('success', 'Código de barras actualizado correctamente.');
}

public function porVencer()
{
    $hoy = now();
    $limite = now()->addDays(30); // próximos 30 días

    $productos = \App\Models\Producto::whereNotNull('fecha_vencimiento')
        ->whereBetween('fecha_vencimiento', [$hoy, $limite])
        ->orderBy('fecha_vencimiento', 'asc')
        ->get();

    return view('productos.por-vencer', compact('productos', 'hoy', 'limite'));
}

public function actualizarCampo(Request $request, Producto $producto)
{
    $request->validate([
        'campo' => 'required|in:stock,precio_compra,precio_venta',
        'valor' => 'required|numeric'
    ]);

    $producto->{$request->campo} = $request->valor;
    $producto->save();

    return response()->json(['ok' => true]);
}

}
