<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cliente;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\OrdenServicio;

class OrdenServicioForm extends Component
{
    public $ordenId; // <-- Agregada aquí
    public $clientes;
    public $marcas;
    public $modelos = [];

    public $cliente_id;
    public $nuevo_cliente_nombre;
    public $nuevo_cliente_tel;
    public $nuevo_cliente_email;
    
    public $marca_id; // 🔹 Usamos solo UNA propiedad
    public $modelo_id;
    public $nro_serie;

    public $detalle_reparacion;
    public $observaciones;

    public $presupuesto;
    public $presupuesto_aprobado = false;
    public $estado = 'pendiente';

public function mount($id = null)
{
    $this->clientes = Cliente::orderBy('nombre')->get();
    $this->marcas = Marca::orderBy('nombre')->get();

    // ✅ Capturamos el turno_id desde la query string
    $this->turno_id = request()->query('turno_id');

    // 🔹 Si venimos desde un turno
    if ($this->turno_id) {
        $turno = \App\Models\TurnoServicio::with('cliente')->find($this->turno_id);

        if ($turno) {
            $this->cliente_id = $turno->cliente_id;
            $this->nota_turno = $turno->nota;
            $this->detalle_reparacion = $turno->nota;
        }
    }

    // 🔹 Si estamos editando una orden existente
    if ($id) {
        $this->ordenId = $id;
        $orden = \App\Models\OrdenServicio::findOrFail($id);

        $this->cliente_id = $orden->cliente_id;
        $this->marca_id = $orden->marca_id;
        $this->modelo_id = $orden->modelo_id;
        $this->identificador = $orden->identificador;
        $this->detalle_reparacion = $orden->detalle_reparacion;
        $this->observaciones = $orden->observaciones;
        $this->presupuesto = $orden->presupuesto;
        $this->presupuesto_aprobado = $orden->presupuesto_aprobado;
        $this->estado = $orden->estado;

        if ($this->marca_id) {
            $this->modelos = \App\Models\Modelo::where('marca_id', $this->marca_id)->get();
        }
    }
}


    public function cambiarMarca($value)
{
    \Log::info("Marca cambiada manualmente a ID: {$value}");

    $this->modelos = Modelo::where('marca_id', $value)->get();
    $this->modelo_id = null;

    // Evento para JavaScript en Livewire 3
    $this->dispatch('marca-cambiada', id: $value);
}


    // 🔹 Este método se disparará automáticamente al cambiar el select
    public function updatedMarcaId($value)
    {
        \Log::info("Marca cambiada a ID: " . $value); // para debug en Laravel log
        $this->modelos = Modelo::where('marca_id', $value)->get();
        $this->modelo_id = null;
    }

    public function guardar()
{
    if (!$this->cliente_id && $this->nuevo_cliente_nombre) {
        $cliente = Cliente::create([
            'nombre' => $this->nuevo_cliente_nombre,
            'telefono' => $this->nuevo_cliente_tel,
            'email' => $this->nuevo_cliente_email,
        ]);
        $this->cliente_id = $cliente->id;
    }

    $this->validate([
        'cliente_id' => 'required|exists:clientes,id',
        'marca_id' => 'required|exists:marcas,id',
        'modelo_id' => 'required|exists:modelos,id',
        'detalle_reparacion' => 'nullable|string',
        'observaciones' => 'nullable|string',
        'presupuesto' => 'nullable|numeric',
        'estado' => 'required|in:pendiente,en_progreso,finalizada'
    ]);

    if ($this->ordenId) {
        // 🔹 Editar
        $orden = OrdenServicio::findOrFail($this->ordenId);
        $orden->update([
            'cliente_id' => $this->cliente_id,
            'marca_id' => $this->marca_id,
            'modelo_id' => $this->modelo_id,
            'nro_serie' => $this->nro_serie,
            'detalle_reparacion' => $this->detalle_reparacion,
            'observaciones' => $this->observaciones,
            'presupuesto' => $this->presupuesto,
            'presupuesto_aprobado' => $this->presupuesto_aprobado,
            'estado' => $this->estado,
        ]);
    } else {
        // 🔹 Crear
        $orden = OrdenServicio::create([
            'cliente_id' => $this->cliente_id,
            'marca_id' => $this->marca_id,
            'modelo_id' => $this->modelo_id,
            'nro_serie' => $this->nro_serie,
            'detalle_reparacion' => $this->detalle_reparacion,
            'observaciones' => $this->observaciones,
            'presupuesto' => $this->presupuesto,
            'presupuesto_aprobado' => $this->presupuesto_aprobado,
            'estado' => $this->estado,
            'numero_orden' => $this->generarNumeroOrden(),
        ]);
    }

    // 🔹 Dispara evento JS para abrir ventana de impresión
    $this->dispatch('abrir-impresion', id: $orden->id);

    // 🔹 Redirige al listado
    return redirect()->route('ordenes.index');
}

    public function guardarNuevoCliente()
    {
        $this->validate([
            'nuevo_cliente_nombre' => 'required|string|max:255',
            'nuevo_cliente_tel'    => 'nullable|string|max:50',
            'nuevo_cliente_email'  => 'nullable|email|max:255',
        ]);

        $cliente = Cliente::create([
            'nombre' => $this->nuevo_cliente_nombre,
            'telefono' => $this->nuevo_cliente_tel,
            'email' => $this->nuevo_cliente_email,
        ]);

        // Seleccionar el cliente recién creado
        $this->cliente_id = $cliente->id;

        // Refrescar lista de clientes
        $this->clientes = Cliente::all();

        // Limpiar inputs del modal
        $this->nuevo_cliente_nombre = '';
        $this->nuevo_cliente_tel = '';
        $this->nuevo_cliente_email = '';
    }

    private function generarNumeroOrden()
    {
        $ultimo = OrdenServicio::max('id');
        return str_pad(($ultimo + 1), 6, '0', STR_PAD_LEFT);
    }

    public function render()
    {
        return view('livewire.orden-servicio-form')
            ->extends('layouts.app')
            ->section('content');
    }

}
