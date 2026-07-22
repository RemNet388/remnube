<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\OrdenServicio;

class OrdenesListado extends Component
{
    use WithPagination;

    public $search = '';

    protected $updatesQueryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $ordenes = OrdenServicio::with(['cliente', 'marca', 'modelo'])
            ->when($this->search, function ($query) {
                $query->whereHas('cliente', fn($q) => $q->where('nombre', 'like', "%{$this->search}%"))
                      ->orWhere('numero', 'like', "%{$this->search}%");
            })
            ->orderByRaw("
                CASE 
                    WHEN estado = 'pendiente' THEN 1
                    WHEN estado = 'en_progreso' THEN 2
                    WHEN estado = 'finalizada' THEN 3
                END
            ")
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.ordenes-listado', compact('ordenes'));
    }
}
