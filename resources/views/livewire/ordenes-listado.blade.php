<div>
    <h1>Órdenes de Servicio</h1>
    <input type="text" wire:model="search" placeholder="Buscar por cliente o número" class="form-control form-control-sm mb-3">

    <table class="table table-sm table-bordered">
        <thead>
            <tr>
                <th>N°</th>
                <th>Cliente</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Ident.</th>
                <th>Estado</th>
                <th>Presupuesto</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ordenes as $orden)
                <tr @class([
                    'bg-success bg-opacity-25' => $orden->estado === 'pendiente',
                    'bg-warning bg-opacity-25' => $orden->estado === 'en_progreso',
                    'bg-danger bg-opacity-25' => $orden->estado === 'finalizada',
                ])>
                    <td>{{ $orden->numero }}</td>
                    <td>{{ $orden->cliente->nombre ?? '-' }}</td>
                    <td>{{ $orden->marca->nombre ?? '-' }}</td>
                    <td>{{ $orden->modelo->nombre ?? '-' }}</td>
                    <td>{{ $orden->orden->identificador ?? '-' }}</td>
                    <td>{{ ucfirst($orden->estado) }}</td>
                    <td>{{ number_format($orden->presupuesto, 2) }}</td>
                    <td>
                        <a href="{{ route('ordenes.edit', $orden->id) }}" class="btn btn-sm btn-warning">Editar</a>
                        <a href="{{ route('ordenes.imprimir', $orden->id) }}" target="_blank" class="btn btn-sm btn-secondary">Imprimir</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No hay órdenes registradas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $ordenes->links() }}
</div>
