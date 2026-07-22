@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h3 class="text-uppercase" style="font-family: 'Roboto', sans-serif; font-size: 16px; font-weight: 600;">
            INFORME DE STOCK
        </h3>
        <div>
            <a href="{{ route('informes.stock.imprimir', request()->only('buscar','categoria_id')) }}"
                class="btn btn-success btn-sm rounded-0">
                Imprimir PDF
            </a>
            <a href="{{ route('informes.stock.exportar_excel') }}" class="btn btn-success btn-sm rounded-0">
                📊 Exportar Excel
            </a>
        </div>
    </div>

    <!-- Buscador -->
    <form method="GET" action="{{ route('informes.stock') }}" class="mb-2">
        <div class="input-group input-group-sm">
            <input type="text" name="buscar" class="form-control form-control-sm"
                   placeholder="Buscar producto..." value="{{ request('buscar') }}">
            <button class="btn btn-primary btn-sm" type="submit"
                    style="border-radius: 0; padding: 0.25rem 0.5rem;">Buscar</button>
        </div>

        <div class="mb-2" style="max-width: 250px;">
            <select name="categoria_id" class="form-select form-select-sm">
                <option value="">-- Todas las categorías --</option>
                @foreach($categorias as $cat)
                    <option value="{{ $cat->id }}" {{ request('categoria_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->nombre }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>

<!-- Formulario de cambio masivo de categoría -->
<form action="{{ route('informes.cambiarCategoriaMasiva') }}" method="POST">
    @csrf

    <div class="mb-2 d-flex align-items-center gap-2" style="max-width: 400px;">
        <select name="nueva_categoria" class="form-select form-select-sm" required style="max-width: 220px;">
            <option value="">-- Cambiar a categoría --</option>
            @foreach($categorias as $cat)
                <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
            @endforeach
        </select>

        <button type="submit" class="btn btn-danger btn-sm rounded-0">Aplicar</button>
    </div>

    <!-- Tabla -->
    <table class="table table-bordered table-sm mb-0" style="font-size: 0.8rem;">
        <thead class="table-light">
            <tr>
                <th style="width: 30px; text-align: center;">
                    <input type="checkbox" id="select-all">
                </th>
                <th>Categoria</th>
                <th>Producto</th>
                <th>Stock</th>
                <th>Precio Compra</th>
                <th>Precio Venta</th>
            </tr>
        </thead>
        <tbody>
            @forelse($productos as $prod)
                <tr>
                    <td style="text-align: center;">
                        <input type="checkbox" name="productos[]" value="{{ $prod->id }}">
                    </td>
                    <td>{{ $prod->categoria->nombre }}</td>
                    <td>{{ $prod->nombre }}</td>
<td>
    <div class="d-flex align-items-center">
        <input type="number" class="form-control form-control-sm input-stock" 
               value="{{ $prod->stock }}" 
               data-id="{{ $prod->id }}" 
               style="width: 80px;">
        <span class="ms-1 estado-guardado"></span>
    </div>
</td>
<td>
    <div class="d-flex align-items-center">
        <input type="number" step="0.01" class="form-control form-control-sm input-precio-compra" 
               value="{{ $prod->precio_compra }}" 
               data-id="{{ $prod->id }}" 
               style="width: 100px;">
        <span class="ms-1 estado-guardado"></span>
    </div>
</td>
<td>
    <div class="d-flex align-items-center">
        <input type="number" step="0.01" class="form-control form-control-sm input-precio-venta" 
               value="{{ $prod->precio_venta }}" 
               data-id="{{ $prod->id }}" 
               style="width: 100px;">
        <span class="ms-1 estado-guardado"></span>
    </div>
</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No hay productos registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</form>


    <div class="d-flex justify-content-center mt-3">
        {{ $productos->links() }}
    </div>
</div>

<script>
    // Check/uncheck all
    document.getElementById('select-all').addEventListener('change', function(e) {
        let checkboxes = document.querySelectorAll('input[name="productos[]"]');
        checkboxes.forEach(cb => cb.checked = e.target.checked);
    });
</script>
<script>
function actualizarCampo(id, campo, valor, input) {
    let estado = input.parentElement.querySelector('.estado-guardado');
    estado.innerHTML = '<i class="bi bi-arrow-repeat text-secondary"></i>'; // ⏳ cargando

    fetch(`/productos/${id}/actualizar-campo`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ campo: campo, valor: valor })
    })
    .then(res => res.json())
    .then(data => {
        if (data.ok) {
            estado.innerHTML = '<i class="bi bi-check-circle-fill text-success"></i>'; // ✅
        } else {
            estado.innerHTML = '<i class="bi bi-x-circle-fill text-danger"></i>'; // ❌
        }
    })
    .catch(() => {
        estado.innerHTML = '<i class="bi bi-x-circle-fill text-danger"></i>'; // ❌
    });

    // Ocultar ícono después de 2 segundos
    setTimeout(() => { estado.innerHTML = ''; }, 2000);
}

document.querySelectorAll('.input-stock, .input-precio-compra, .input-precio-venta')
    .forEach(input => {
        input.addEventListener('change', e => {
            let campo = e.target.classList.contains('input-stock') ? 'stock' :
                        e.target.classList.contains('input-precio-compra') ? 'precio_compra' :
                        'precio_venta';

            actualizarCampo(e.target.dataset.id, campo, e.target.value, e.target);
        });
    });
</script>
@endsection
@push('submenu')
<a href="{{ route('informes.ventas') }}" class="btn btn-primary btn-sm rounded-0 fw-semibold small">
    <i class="bi bi-cash-stack"></i> Ventas
</a>
<span style="display: inline-block; width: 10px;"></span>

<a href="{{ route('informes.ganancias') }}" class="btn btn-primary btn-sm rounded-0 fw-semibold small">
    <i class="bi bi-graph-up"></i> Ganancias
</a>
<span style="display: inline-block; width: 10px;"></span>

<a href="{{ route('informes.movimientos_stock') }}" class="btn btn-primary btn-sm rounded-0 fw-semibold small">
    <i class="bi bi-arrow-left-right"></i> Movimientos Stock
</a>
<span style="display: inline-block; width: 10px;"></span>

<a href="{{ route('informes.stock') }}" class="btn btn-primary btn-sm rounded-0 fw-semibold small">
    <i class="bi bi-box"></i> Stock
</a>
<span style="display: inline-block; width: 10px;"></span>

<a href="{{ route('productos.por-vencer') }}" class="btn btn-danger btn-sm rounded-0 fw-semibold small">
    <i class="bi bi-exclamation-triangle"></i> Productos por vencer
</a>
<a href="{{ route('informes.productos_a_comprar') }}"  class="btn btn-danger btn-sm rounded-0 fw-semibold small">
        <i class="bi bi-box-seam"></i> Productos a Comprar
</a>
<a href="{{ route('informes.ventas_por_vendedor') }}" class="btn btn-danger btn-sm rounded-0 fw-semibold small">
    <i class="bi bi-person-badge"></i> Ventas por Vendedor
</a>
@endpush