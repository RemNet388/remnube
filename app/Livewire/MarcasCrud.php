<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Marca;

class MarcasCrud extends Component
{
    public $marcas;
    public $nombre;
    public $marca_id;

    public function mount()
    {
        $this->listar();
    }

    public function listar()
    {
        $this->marcas = Marca::orderBy('nombre')->get();
    }

    public function guardar()
    {
        $this->validate([
            'nombre' => 'required|string|max:255'
        ]);
\Log::info('Guardando marca...', ['nombre' => $this->nombre]);
        Marca::updateOrCreate(
            ['id' => $this->marca_id],
            ['nombre' => $this->nombre]
        );

        $this->reset(['nombre', 'marca_id']);
        $this->listar();
    }

    public function editar($id)
    {
        $marca = Marca::findOrFail($id);
        $this->marca_id = $marca->id;
        $this->nombre = $marca->nombre;
    }

    public function borrar($id)
    {
        Marca::findOrFail($id)->delete();
        $this->listar();
    }

    public function render()
{
    return view('livewire.marcas-crud')
        ->layout('layouts.app'); // usamos tu layout aquí
}
}
