<div>
    <!-- Selector de fecha -->
    <div class="mb-3 d-flex align-items-center gap-2">
        <label for="fecha" class="form-label mb-0 fw-bold">Fecha:</label>
        <input type="date" id="fecha" wire:model="fecha" class="form-control w-auto rounded-0">
    </div>

    <!-- Agenda turnos -->
    <div class="d-flex flex-column gap-1">
        @foreach($turnos as $turno)
            <div class="d-flex justify-content-between align-items-center border p-2 rounded-0"
                 style="background-color: {{ $turno['ocupado'] ? '#f8d7da' : '#d1e7dd' }}">

                <div>
                    <strong>{{ substr($turno['hora_inicio'],0,5) }} - {{ substr($turno['hora_fin'],0,5) }}</strong>
                    <div class="small text-muted">
                        {{ $turno['ocupado'] ? $turno['cliente']->nombre ?? 'Sin nombre' : 'Libre' }}
                    </div>
                    @if($turno['ocupado'] && $turno['nota'])
                        <div class="small fst-italic text-muted">{{ $turno['nota'] }}</div>
                    @endif
                </div>

                <div>
                    @if(!$turno['ocupado'])
                        <button wire:click="seleccionarTurno('{{ $turno['hora_inicio'] }}')"
                                class="btn btn-sm btn-primary rounded-0">
                            Reservar
                        </button>
                    @else
                        <a href="{{ route('ordenes.create', ['turno_id' => $turno['id']]) }}"
                           class="btn btn-sm btn-success rounded-0">
                            Generar OT
                        </a>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Modal reservar turno -->
    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 rounded-0">
                    <div class="modal-header bg-primary text-white rounded-0">
                        <h5 class="modal-title">Reservar turno {{ substr($horaSeleccionada,0,5) }}</h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="cerrarModal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Cliente existente -->
                        <div class="mb-2">
                            <select wire:model="cliente_id" class="form-select rounded-0">
                                <option value="">-- Seleccionar cliente --</option>
                                @foreach($clientes as $cliente)
                                    <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- O crear nuevo -->
                        <div class="mb-2">
                            <input type="text" wire:model.defer="nombre_cliente" class="form-control rounded-0 mb-1" placeholder="Nombre cliente">
                            <input type="text" wire:model.defer="telefono_cliente" class="form-control rounded-0" placeholder="Teléfono cliente">
                        </div>
                        <!-- Nota -->
                        <div class="mb-2">
                            <textarea wire:model.defer="nota" class="form-control rounded-0" placeholder="Nota"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button wire:click="reservarTurno" class="btn btn-success rounded-0">Confirmar</button>
                        <button wire:click="cerrarModal" class="btn btn-secondary rounded-0">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
document.addEventListener('livewire:init', () => {
    Livewire.on('turno-reservado', data => alert(data.mensaje));
    Livewire.on('turno-error', data => alert(data.mensaje));
    Livewire.on('cliente-creado', data => alert(data.mensaje));
});
</script>
