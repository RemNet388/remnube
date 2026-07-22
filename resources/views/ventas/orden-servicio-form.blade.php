<div>
    <div class="container mt-3">
        <div class="card">
            <div class="card-header py-2">
                <strong>Nueva Orden de Servicio</strong>
            </div>
            <div class="card-body p-3">
                
                <!-- Cliente existente / nuevo -->
                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label mb-1">Cliente existente</label>
                        <div class="input-group input-group-sm">
                            <select wire:model="cliente_id" class="form-select">
                                <option value="">-- Seleccionar --</option>
                                @foreach($clientes as $c)
                                    <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-outline-primary"
                                    data-bs-toggle="modal" data-bs-target="#modalNuevoCliente">
                                + Nuevo
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Marca / Modelo / Serie -->
                <div class="row g-2 mt-2">
                    <div class="col-md-4">
                        <label class="form-label mb-1">Marca</label>
                        <select wire:model="marca_id" wire:change="cambiarMarca($event.target.value)" class="form-select form-select-sm">
                            <option value="">-- Seleccionar --</option>
                            @foreach($marcas as $m)
                                <option value="{{ $m->id }}">{{ $m->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label mb-1">Modelo</label>
                        <select wire:model="modelo_id" class="form-select form-select-sm">
                            <option value="">-- Selecciona un modelo --</option>
                            @foreach($modelos as $modelo)
                                <option value="{{ $modelo->id }}">{{ $modelo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label mb-1">Identificacion</label>
                        <input type="text" wire:model="nro_serie" class="form-control form-control-sm">
                    </div>
                </div>

                <!-- Detalles / Observaciones -->
                <div class="row g-2 mt-2">
                    <div class="col-md-6">
                        <label class="form-label mb-1">Detalles</label>
                        <textarea wire:model="detalle_reparacion" rows="3" class="form-control form-control-sm"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label mb-1">Observaciones</label>
                        <textarea wire:model="observaciones" rows="3" class="form-control form-control-sm"></textarea>
                    </div>
                </div>

                <!-- Presupuesto / Estado -->
                <div class="row g-2 mt-2">
                    <div class="col-md-3">
                        <label class="form-label mb-1">Presupuesto</label>
                        <input type="number" step="0.01" wire:model="presupuesto" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3 d-flex align-items-center">
                        <div class="form-check mt-3">
                            <input type="checkbox" wire:model="presupuesto_aprobado" class="form-check-input" id="checkAprobado">
                            <label class="form-check-label" for="checkAprobado">Aprobado</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label mb-1">Estado</label>
                        <select wire:model="estado" class="form-select form-select-sm">
                            <option value="pendiente">Pendiente</option>
                            <option value="en_progreso">En Progreso</option>
                            <option value="finalizada">Finalizada</option>
                        </select>
                    </div>
                </div>

                <!-- Botón -->
                <div class="text-end mt-3">
<button wire:click="guardar"
        wire:loading.attr="disabled"
        class="btn btn-sm btn-primary">

    <span wire:loading.remove>Guardar e Imprimir</span>
    <span wire:loading>Procesando...</span>
</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL NUEVO CLIENTE -->
    <div wire:ignore.self class="modal fade" id="modalNuevoCliente" tabindex="-1" aria-labelledby="modalNuevoClienteLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header py-2">
            <h5 class="modal-title" id="modalNuevoClienteLabel">Nuevo Cliente</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body">
            <div class="mb-2">
                <label class="form-label">Nombre</label>
                <input type="text" wire:model="nuevo_cliente_nombre" class="form-control form-control-sm">
            </div>
            <div class="mb-2">
                <label class="form-label">Teléfono</label>
                <input type="text" wire:model="nuevo_cliente_tel" class="form-control form-control-sm">
            </div>
            <div class="mb-2">
                <label class="form-label">Email</label>
                <input type="email" wire:model="nuevo_cliente_email" class="form-control form-control-sm">
            </div>
          </div>
          <div class="modal-footer py-2">
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" wire:click="guardarNuevoCliente" class="btn btn-primary btn-sm" data-bs-dismiss="modal">
                Guardar y Seleccionar
            </button>
          </div>
        </div>
      </div>
    </div>
    
</div>
