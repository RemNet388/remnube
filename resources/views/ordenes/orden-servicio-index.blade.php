<div class="container mt-3">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Órdenes de Servicio</h5>
            <a href="{{ route('ordenes.create') }}" class="btn btn-sm btn-primary">Nueva Orden</a>
        </div>
        <div class="card-body">
            <input type="text" id="search" placeholder="Buscar por cliente o número" class="form-control form-control-sm mb-3">

            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>Cliente</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Estado</th>
                        <th>Presupuesto</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ordenes as $orden)
                        <tr>
                            <td>{{ $orden->numero }}</td>
                            <td>{{ $orden->cliente->nombre ?? '-' }}</td>
                            <td>{{ $orden->marca->nombre ?? '-' }}</td>
                            <td>{{ $orden->modelo->nombre ?? '-' }}</td>
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
    </div>
</div>
<script>
$('#search').on('input', function() {
    let query = $(this).val();
    $.ajax({
        url: '{{ route("ordenes.buscar") }}',
        method: 'GET',
        data: { search: query },
        success: function(data) {
            // Actualiza el tbody de la tabla con los resultados
            $('tbody').html(data);
        }
    });
});
</script>