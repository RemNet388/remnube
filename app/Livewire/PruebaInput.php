<?php

namespace App\Livewire;

use Livewire\Component;

class PruebaInput extends Component
{
    public $texto = '';

    public function render()
    {
        return view('livewire.prueba-input');
    }
}
