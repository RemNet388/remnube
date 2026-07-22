<div class="container mt-3">
    <h4>Modelos</h4>
    <form wire:submit.prevent="guardar" class="row g-2 mb-3">
        <div class="col-md-4">
            <select wire:model="marca_id" class="form-select form-select-sm">
                <option value="">-- Seleccionar marca --</option>
                @foreach($marcas as $marca)
                    <option value="{{ $marca->id }}">{{ $marca->nombre }}</option>
                @endforeach
            </select>
            @error('marca_id') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
        <div class="col-md-4">
            <input type="text" wire:model="nombre" class="form-control form-control-sm" placeholder="Nombre del modelo">
            @error('nombre') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-sm btn-primary">
                {{ $editId ? 'Actualizar' : 'Agregar' }}
            </button>
        </div>
    </form>

    <table class="table table-sm table-bordered">
        <thead>
            <tr>
                <th>Marca</th>
                <th>Modelo</th>
                <th width="100">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($modelos as $modelo)
                <tr>
                    <td>{{ $modelo->marca->nombre }}</td>
                    <td>{{ $modelo->nombre }}</td>
                    <td>
                        <button wire:click="editar({{ $modelo->id }})" class="btn btn-sm btn-warning">✏</button>
                        <button wire:click="borrar({{ $modelo->id }})" class="btn btn-sm btn-danger">🗑</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@push('submenu')        
<a href="{{ route('ordenes.index') }}" class="btn btn-primary btn-sm">Ordenes de Servicio</a>
<a href="{{ route('marcas.index') }}" class="btn btn-primary btn-sm"> Marcas</a>
<a href="{{ route('modelos.index') }}" class="btn btn-primary btn-sm"> Modelos</a>            
@endpush