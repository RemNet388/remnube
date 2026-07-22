<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Support\Facades\DB;
use App\Models\CuentaCorriente;
use App\Models\FormaPago;
use Illuminate\Http\Request;

class ClienteController extends Controller
{

    public function index(Request $request)
{
    $q = $request->input('buscar');
    $formasPago = FormaPago::all();

    $clientes = Cliente::when($q, function ($query, $q) {
            $query->where('nombre', 'like', "%$q%")
                  ->orWhere('email', 'like', "%$q%");
        })
        ->orderBy('nombre')
        ->paginate(25) // 👈 25 por página
        ->appends(['buscar' => $q]); // mantiene el parámetro en la URL

    // Calcular saldo actual para cada cliente
    foreach ($clientes as $cliente) {
        $cliente->saldo_actual = $cliente->cuentaCorriente()
            ->orderByDesc('id')
            ->value('saldo') ?? 0;
    }

    return view('clientes.index', compact('clientes', 'formasPago'));
}

    public function create()
    {
        return view('clientes.create');
    }

public function store(Request $request) 
{
    $request->validate([
        'nombre' => 'required|string|max:255',
        'dni' => 'nullable|string|max:20',
        'direccion' => 'nullable|string|max:255',
        'telefono' => 'nullable|string|max:20',
        'email' => 'nullable|email|max:255',
    ]);

    $cliente = Cliente::create($request->all());

    // Si la solicitud viene por AJAX, devolvemos JSON estructurado
    if ($request->ajax()) {
        \Log::info('Respondiendo con JSON', ['cliente_id' => $cliente->id]);
        return response()->json($cliente);
    }

    \Log::info('Redirigiendo a clientes.index');
    return redirect()->route('clientes.index')->with('success', 'Cliente creado correctamente');
}



    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'dni' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $cliente->update($request->all());
        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado correctamente');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return redirect()->route('clientes.index')->with('success', 'Cliente eliminado correctamente');
    }

public function show(Cliente $cliente)
{
    // Si no lo usás, redirigí a index
    return redirect()->route('clientes.index');
}

public function deudores(Request $request)
    {
        $buscar = $request->get('buscar');

        // Subconsulta: saldo actual por cliente
        $saldos = CuentaCorriente::select(
                'entidad_id',
                DB::raw('SUM(debe - haber) as saldo_actual')
            )
            ->where('entidad_tipo', 'cliente')
            ->groupBy('entidad_id');

        // Clientes con deuda (saldo > 0)
        $clientes = Cliente::select('clientes.*', DB::raw('COALESCE(saldos.saldo_actual, 0) as saldo_actual'))
            ->leftJoinSub($saldos, 'saldos', 'clientes.id', '=', 'saldos.entidad_id')
            ->when($buscar, function($q) use ($buscar) {
                $q->where('clientes.nombre', 'like', "%{$buscar}%")
                  ->orWhere('clientes.email', 'like', "%{$buscar}%")
                  ->orWhere('clientes.telefono', 'like', "%{$buscar}%");
            })
            ->orderByDesc('saldo_actual') // 👈 clave
            ->orderBy('clientes.nombre', 'asc')
            ->get();

        // Formas de pago
        $formasPago = FormaPago::orderBy('nombre')->get();

        return view('clientes.deudores', compact('clientes', 'formasPago'));
    }

}
