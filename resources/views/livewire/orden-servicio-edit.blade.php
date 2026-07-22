<div class="container mt-4" style="font-family: 'Roboto', sans-serif; font-size: 0.85rem;">
    <h2 class="mb-4 text-uppercase fw-bold" style="font-size: 1rem;">
        {{ $ordenId ? 'Editar Orden de Servicio' : 'Crear Orden de Servicio' }}
    </h2>

    <form wire:submit.prevent="guardar" class="card p-3 shadow-sm rounded-0">

        <!-- Cliente y Nº de orden -->
        <div class="row mb-3">
            <div class="col-md-8">
                <label class="form-label small">Cliente</label>
                <select wire:model="cliente_id" class="form-select form-select-sm rounded-0">
                    <option value="">Seleccione...</option>
                    @foreach($clientes as $cliente)
                        <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold">Número de Orden</label>
                <p class="form-control-plaintext small">OT-00000{{ $ordenId }}</p>
            </div>
        </div>

        <!-- Marca, Modelo, Nº Serie -->
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label small">Marca</label>
                <select wire:model="marca_id" class="form-select form-select-sm rounded-0">
                    <option value="">Seleccione...</option>
                    @foreach($marcas as $marca)
                        <option value="{{ $marca->id }}">{{ $marca->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small">Modelo</label>
                <select wire:model="modelo_id" class="form-select form-select-sm rounded-0">
                    <option value="">Seleccione...</option>
                    @foreach($modelos as $modelo)
                        <option value="{{ $modelo->id }}">{{ $modelo->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small">Nº Serie</label>
                <input type="text" wire:model="identificador" class="form-control form-control-sm rounded-0">
            </div>
        </div>

        <!-- Detalle y Observaciones -->
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label small">Detalle Reparación</label>
                <textarea wire:model="detalle_reparacion" class="form-control form-control-sm rounded-0" rows="5"></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label small">Observaciones</label>
                <textarea wire:model="observaciones" class="form-control form-control-sm rounded-0" rows="5"></textarea>
            </div>
        </div>

        <!-- Presupuesto y Estado -->
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label small">Presupuesto</label>
                <input type="number" wire:model="presupuesto" class="form-control form-control-sm rounded-0">
            </div>
            <div class="col-md-6">
                <label class="form-label small">Estado</label>
                <select wire:model="estado" class="form-select form-select-sm rounded-0">
                    <option value="pendiente">Pendiente</option>
                    <option value="en_progreso">En Progreso</option>
                    <option value="finalizada">Finalizada</option>
                    <option value="rechazada">Rechazado</option>
                </select>
            </div>
        </div>

        <!-- Botón -->
        <div class="text-end">
            <button type="submit" class="btn btn-primary btn-sm rounded-0"
                    wire:loading.attr="disabled" wire:target="guardar">
                <span wire:loading.remove wire:target="guardar">
                    {{ $ordenId ? 'Actualizar' : 'Guardar' }}
                </span>
                <span wire:loading wire:target="guardar">
                    Procesando...
                </span>
            </button>
        </div>

    </form>
</div>
