<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\TurnoServicio;
use App\Models\Cliente;
use Illuminate\Support\Facades\DB;

class TurnosDia extends Component
{
    public $fecha;
    public $turnos = [];
    public $horaSeleccionada;
    public $cliente_id;
    public $nombre_cliente;
    public $telefono_cliente;
    public $nota;
    public $showModal = false;
    public $clientes;

    protected $listeners = ['crearCliente'];

    public function mount($fecha = null)
    {
        $this->fecha = $fecha ?? now()->toDateString();
        $this->cargarTurnos();
        $this->cargarClientes();
    }

    public function updatedFecha()
    {
        $this->cargarTurnos();
        $this->horaSeleccionada = null;
    }

    public function cargarClientes()
    {
        $this->clientes = Cliente::orderBy('nombre')->get();
    }

    public function cargarTurnos()
    {
        $turnosDB = TurnoServicio::where('fecha', $this->fecha)->get()->keyBy('hora_inicio');

        $this->turnos = [];
        for ($h = 8; $h < 18; $h++) {
            $hora_inicio = sprintf('%02d:00:00', $h);
            $hora_fin = sprintf('%02d:00:00', $h + 1);

            $turno = $turnosDB->get($hora_inicio);

            $this->turnos[] = [
                'hora_inicio' => $hora_inicio,
                'hora_fin' => $hora_fin,
                'ocupado' => $turno ? true : false,
                'cliente' => $turno?->cliente,
                'nota' => $turno?->nota,
                'id' => $turno?->id,
            ];
        }
    }

    public function seleccionarTurno($hora)
    {
        $this->horaSeleccionada = $hora;
        $this->showModal = true;
    }

    public function cerrarModal()
    {
        $this->showModal = false;
        $this->horaSeleccionada = null;
        $this->cliente_id = null;
        $this->nombre_cliente = '';
        $this->telefono_cliente = '';
        $this->nota = '';
    }

    public function crearCliente($nombre, $telefono)
    {
        $cliente = Cliente::create([
            'nombre' => $nombre,
            'telefono' => $telefono
        ]);
        $this->cargarClientes();
        $this->cliente_id = $cliente->id;
        $this->dispatch('cliente-creado', ['mensaje' => 'Cliente creado correctamente']);
    }

    public function reservarTurno()
    {
        if (!$this->horaSeleccionada) {
            $this->dispatch('turno-error', ['mensaje' => 'Seleccione una hora primero']);
            return;
        }

        $clienteId = $this->cliente_id ?? Cliente::firstOrCreate(
            ['telefono' => $this->telefono_cliente],
            ['nombre' => $this->nombre_cliente ?? 'Sin nombre']
        )->id;

        $existe = TurnoServicio::where('fecha', $this->fecha)
            ->where('hora_inicio', $this->horaSeleccionada)
            ->exists();

        if ($existe) {
            $this->dispatch('turno-error', ['mensaje' => 'Este turno ya está reservado']);
            $this->cerrarModal();
            return;
        }

        DB::transaction(function () use ($clienteId) {
            TurnoServicio::create([
                'fecha' => $this->fecha,
                'hora_inicio' => $this->horaSeleccionada,
                'hora_fin' => date('H:i:s', strtotime($this->horaSeleccionada . ' +1 hour')),
                'cliente_id' => $clienteId,
                'nota' => $this->nota,
            ]);
        });

        $this->dispatch('turno-reservado', ['mensaje' => 'Turno reservado correctamente']);
        $this->cerrarModal();
        $this->cargarTurnos();
    }

    public function render()
    {
        return view('livewire.turnos-dia');
    }
}
