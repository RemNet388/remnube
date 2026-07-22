<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cliente;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\OrdenServicio;

class OrdenServicioEdit extends Component
{
    public $ordenId;
    public $clientes;
    public $marcas;
    public $modelos = [];

    public $cliente_id;
    public $marca_id;
    public $modelo_id;
    public $nro_serie;
    public $detalle_reparacion;
    public $observaciones;
    public $presupuesto;
    public $presupuesto_aprobado;
    public $estado;

    public function mount($id)
    {
        $this->ordenId = $id;

        $this->clientes = Cliente::orderBy('nombre')->get();
        $this->marcas = Marca::orderBy('nombre')->get();

        $orden = OrdenServicio::findOrFail($id);

        $this->numero = $orden->numero;
        $this->cliente_id = $orden->cliente_id;
        $this->marca_id = $orden->marca_id;
        $this->modelo_id = $orden->modelo_id;
        $this->nro_serie = $orden->nro_serie;
        $this->detalle_reparacion = $orden->detalle_reparacion;
        $this->observaciones = $orden->observaciones;
        $this->presupuesto = $orden->presupuesto;
        $this->presupuesto_aprobado = $orden->presupuesto_aprobado;
        $this->estado = $orden->estado;

        // Pre-cargar modelos
        if ($this->marca_id) {
            $this->modelos = Modelo::where('marca_id', $this->marca_id)->get();
        }
    }

    public function updatedMarcaId($value)
    {
        $this->modelos = Modelo::where('marca_id', $value)->get();
        $this->modelo_id = null;
    }

    public function guardar()
    {
        $this->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'marca_id' => 'required|exists:marcas,id',
            'modelo_id' => 'required|exists:modelos,id',
            'detalle_reparacion' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'presupuesto' => 'nullable|numeric',
            'estado' => 'required|in:pendiente,en_progreso,finalizada'
        ]);

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

        session()->flash('success', 'Orden actualizada correctamente.');

        return redirect()->route('ordenes.index');
    }

    public function render()
    {
        return view('livewire.orden-servicio-edit')
            ->extends('layouts.app')
            ->section('content');
    }
}
