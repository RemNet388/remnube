<div class="container py-3">
    <h2 class="mb-3">Gestión de Marcas</h2>

    <form wire:submit.prevent="guardar" class="row g-2 mb-4">
        <div class="col-md-6">
            <input type="text" wire:model="nombre" class="form-control" placeholder="Nombre de la marca">
            @error('nombre') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100">
                {{ $marca_id ? 'Actualizar' : 'Guardar' }}
            </button>
        </div>
        @if($marca_id)
        <div class="col-md-3">
            <button type="button" wire:click="$set('marca_id', null)" class="btn btn-secondary w-100">
                Cancelar
            </button>
        </div>
        @endif
    </form>

    <table class="table table-sm table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th>Nombre</th>
                <th width="150">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($marcas as $marca)
                <tr>
                    <td>{{ $marca->nombre }}</td>
                    <td>
                        <button wire:click="editar({{ $marca->id }})" class="btn btn-sm btn-warning">Editar</button>
                        <button wire:click="borrar({{ $marca->id }})" class="btn btn-sm btn-danger">Borrar</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="text-center">No hay marcas cargadas</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@push('submenu')        
<a href="{{ route('ordenes.index') }}" class="btn btn-primary btn-sm">Ordenes de Servicio</a>
<a href="{{ route('marcas.index') }}" class="btn btn-primary btn-sm"> Marcas</a>
<a href="{{ route('modelos.index') }}" class="btn btn-primary btn-sm"> Modelos</a>            
@endpush