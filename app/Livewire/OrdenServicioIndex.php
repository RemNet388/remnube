<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\OrdenServicio;

class OrdenServicioIndex extends Component
{
    use WithPagination;

    public $search = '';

    protected $queryString = ['search']; // 👈 Esto guarda el valor en la URL

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $ordenes = OrdenServicio::with(['cliente', 'marca', 'modelo'])
            ->where(function($q) {
                $q->whereHas('cliente', function($qc) {
                    $qc->where('nombre', 'like', '%' . $this->search . '%');
                })
                ->orWhere('numero', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.orden-servicio-index', [
            'ordenes' => $ordenes
        ])->extends('layouts.app')->section('content');
    }
}
