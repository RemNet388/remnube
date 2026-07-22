<div>
    <div class="mb-3">
        <input type="text"
               class="form-control"
               placeholder="Buscar productos..."
               wire:model.debounce.500ms="buscar">
    </div>

    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>Stock</th>
                <th>Precio Venta</th>
            </tr>
        </thead>
        <tbody>
            @forelse($productos as $p)
                <tr>
                    <td>{{ $p->codigo }}</td>
                    <td>{{ $p->nombre }}</td>
                    <td>{{ $p->stock }}</td>
                    <td>${{ number_format($p->precio_venta, 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">
                        No se encontraron productos
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $productos->links() }}
</div>
