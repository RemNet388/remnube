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