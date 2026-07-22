<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Modelo;
use App\Models\Marca;

class ModelosCrud extends Component
{
    public $marca_id;
    public $nombre;
    public $editId;

    public function guardar()
    {
        $this->validate([
            'marca_id' => 'required|exists:marcas,id',
            'nombre' => 'required|unique:modelos,nombre,' . $this->editId
        ]);

        Modelo::updateOrCreate(
            ['id' => $this->editId],
            [
                'marca_id' => $this->marca_id,
                'nombre' => $this->nombre
            ]
        );

        $this->reset(['marca_id', 'nombre', 'editId']);
    }

    public function editar($id)
    {
        $modelo = Modelo::find($id);
        $this->marca_id = $modelo->marca_id;
        $this->nombre = $modelo->nombre;
        $this->editId = $id;
    }

    public function borrar($id)
    {
        Modelo::destroy($id);
    }

    public function render()
    {
        return view('livewire.modelos-crud', [
            'modelos' => Modelo::with('marca')->orderBy('nombre')->get(),
            'marcas'  => Marca::orderBy('nombre')->get()
        ])->layout('layouts.app'); // usa tu layout principal
    }
}
