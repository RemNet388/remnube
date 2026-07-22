<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Producto;

class ProductosIndex extends Component
{
    use WithPagination;

    public $buscar = '';

    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'buscar' => ['except' => ''],
    ];

    public function updatedBuscar()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Producto::query();

        if (!empty($this->buscar)) {
            $term = '%' . trim($this->buscar) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('nombre', 'like', $term)
                  ->orWhere('codigo', 'like', $term);
            });
        }

        return view('livewire.productos-index', [
            'productos' => $query->paginate(10),
        ])->layout('layouts2.app'); // 👈 usa el layout principal
    }
}
