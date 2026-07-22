@extends('layouts.app')

@section('content')
<div class="table-responsive">
    <table class="table table-sm table-bordered table-striped align-middle" style="font-family: 'Roboto', sans-serif; font-size: 13px; table-layout: fixed; width: 100%;">
<thead class="table-light">
    <tr>
        <th style="width: 41%;">
            <a href="{{ route('productos.index', array_merge(request()->all(), [
                'sort' => 'nombre',
                'direction' => request('sort') === 'nombre' && request('direction') === 'asc' ? 'desc' : 'asc'
            ])) }}">
                Nombre
                @if(request('sort') === 'nombre')
                    <i class="bi bi-caret-{{ request('direction') === 'asc' ? 'up' : 'down' }}-fill"></i>
                @endif
            </a>
        </th>
        <th style="width: 15%;">
            <a href="{{ route('productos.index', array_merge(request()->all(), [
                'sort' => 'categoria',
                'direction' => request('sort') === 'categoria' && request('direction') === 'asc' ? 'desc' : 'asc'
            ])) }}">
                Categoría
                @if(request('sort') === 'categoria')
                    <i class="bi bi-caret-{{ request('direction') === 'asc' ? 'up' : 'down' }}-fill"></i>
                @endif
            </a>
        </th>
        <th style="width: 6%;" class="text-end">Stock</th>
        <th style="width: 8%;" class="text-end">Precio Compra</th>
        <th style="width: 8%;" class="text-end">Precio Venta</th>
        <th style="width: 8%;" class="text-center">Imagen</th>
        <th style="width: 14%;" class="text-center">Acciones</th>
    </tr>
</thead>

        <tbody>
            @forelse($productos as $producto)
                <tr>
                    <td>
                        {{ $producto->nombre }}
                        @if($producto->fecha_vencimiento)
                            - <span class="fw-bold small">Vence: {{ $producto->fecha_vencimiento->format('d/m/Y') }}</span>
                        @endif
                    </td>
                    <td>{{ $producto->categoria->nombre ?? 'Sin categoría' }}</td>
                    <td class="text-end">{{ $producto->stock }}</td>
                    <td class="text-end">${{ number_format((float)$producto->precio_compra, 2) }}</td>
                    <td class="text-end">${{ number_format((float)$producto->precio_venta, 2) }}</td>
                    <td class="text-center">
                        @if($producto->imagen)
                            <a href="#" data-bs-toggle="modal" data-bs-target="#imagenModal"
                               data-imagen="{{ asset('storage/'.$producto->imagen) }}"
                               data-nombre="{{ $producto->nombre }}"
                               data-categoria="{{ $producto->categoria->nombre ?? 'Sin categoría' }}">
                                <img src="{{ asset('storage/'.$producto->imagen) }}" alt="{{ $producto->nombre }}" class="img-thumbnail mx-auto d-block" style="max-width: 50px;">
                            </a>
                        @else
                            <a href="#" data-bs-toggle="modal" data-bs-target="#imagenModal"
                               data-imagen="" data-nombre="{{ $producto->nombre }}"
                               data-categoria="{{ $producto->categoria->nombre ?? 'Sin categoría' }}">
                                <i class="bi bi-image text-muted" style="font-size: 2rem;"></i>
                            </a>
                        @endif
                    </td>
                    <td class="text-center">
                        <a href="#" class="text-primary ms-2" data-bs-toggle="modal" data-bs-target="#modalCodigo" data-id="{{ $producto->id }}" data-codigo="{{ $producto->codigo ?? '' }}">
                            <i class="bi bi-upc-scan fs-5"></i>
                        </a>
                        <button class="btn btn-sm btn-info ver-movimientos" data-id="{{ $producto->id }}">📦</button>
                        <a href="{{ route('productos.edit', ['producto' => $producto->id] + request()->all()) }}" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('productos.destroy', $producto) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Seguro que quieres eliminar este producto?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">No hay productos que coincidan con la búsqueda.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

<!-- Modal para mostrar imagen grande -->
<div class="modal fade" id="imagenModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content bg-dark text-white">
      <div class="modal-body text-center">
        <img id="imagenModalSrc" src="" alt="Imagen producto" class="img-fluid rounded mb-3">
        <h5 id="imagenModalNombre"></h5>
        <p id="imagenModalCategoria" class="text-warning"></p>
      </div>
    </div>
  </div>
</div>

<!-- Modal Cambiar Código -->
<div class="modal fade" id="modalCodigo" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" id="formCodigo">
      @csrf
      @method('PATCH')
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Cambiar Código de Barras</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <input type="text" name="codigo" id="codigoInput" class="form-control" placeholder="Ingrese nuevo código">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Modal para movimientos -->
<div class="modal fade" id="modalMovimientos" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Historial de movimientos</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Fecha</th>
              <th>Tipo</th>
              <th>Cantidad</th>
              <th>Descripción</th>
            </tr>
          </thead>
          <tbody id="tabla-movimientos">
            <!-- Cargado por AJAX -->
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Modal Código
    var modalCodigo = document.getElementById('modalCodigo');
    modalCodigo.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
        var codigo = button.getAttribute('data-codigo');

        document.getElementById('codigoInput').value = codigo || '';
        document.getElementById('formCodigo').action = '/productos/' + id + '/codigo';
    });

    // Modal Movimientos
    const modalMovimientos = document.getElementById('modalMovimientos');
    const tablaMovimientos = modalMovimientos.querySelector('#tabla-movimientos');

    document.querySelectorAll('.ver-movimientos').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            tablaMovimientos.innerHTML = '<tr><td colspan="4" class="text-center">Cargando...</td></tr>';

            fetch(`/productos/${id}/movimientos`)
                .then(res => res.json())
                .then(data => {
                    let html = '';
                    if (!data || data.length === 0) {
                        html = '<tr><td colspan="4" class="text-center">Sin movimientos</td></tr>';
                    } else {
                        data.forEach(m => {
                            html += `<tr>
                                <td>${m.fecha}</td>
                                <td>${m.tipo}</td>
                                <td>${m.cantidad}</td>
                                <td>${m.descripcion ?? '-'}</td>
                            </tr>`;
                        });
                    }
                    tablaMovimientos.innerHTML = html;
                    new bootstrap.Modal(modalMovimientos).show();
                })
                .catch(err => {
                    tablaMovimientos.innerHTML = '<tr><td colspan="4" class="text-center text-danger">Error cargando movimientos</td></tr>';
                    console.error(err);
                });
        });
    });

    // Checkbox Solo Stock
    let check = document.getElementById('solo_stock');
    if (check) {
        check.addEventListener('change', function() {
            if (!check.checked) {
                let hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'solo_stock';
                hidden.value = '0';
                check.form.appendChild(hidden);
            }
            check.form.submit();
        });
    }

    // Modal Imagen
    const modal = document.getElementById('imagenModal');
    modal.addEventListener('show.bs.modal', function (event) {
        const link = event.relatedTarget;
        const imagen = link.getAttribute('data-imagen');
        const nombre = link.getAttribute('data-nombre');
        const categoria = link.getAttribute('data-categoria');

        const modalImg = modal.querySelector('#imagenModalSrc');
        const modalNombre = modal.querySelector('#imagenModalNombre');
        const modalCategoria = modal.querySelector('#imagenModalCategoria');

        if (imagen) {
            modalImg.src = imagen;
            modalImg.classList.remove('d-none');
        } else {
            modalImg.src = "";
            modalImg.classList.add('d-none');
            modalNombre.insertAdjacentHTML("beforebegin", `<i class="bi bi-image text-secondary" style="font-size: 5rem;"></i>`);
        }

        modalNombre.textContent = nombre;
        modalCategoria.textContent = categoria;
    });
});
</script>

@push('submenu')
<form method="GET" action="{{ route('productos.index') }}" class="d-flex flex-wrap align-items-center gap-2">
    <input type="text" name="buscar" value="{{ request('buscar') }}" 
        class="form-control form-control-sm" 
        placeholder="Buscar producto o categoría..." 
        style="border-radius: 0; width: 200px; height: calc(1.5em + .5rem + 2px);">

    <button type="submit" class="btn btn-secondary btn-sm" style="border-radius: 0;">
        <i class="bi bi-search"></i>
    </button>

    <select name="categoria_id" class="form-select form-select-sm" style="border-radius: 0; width: 150px; height: calc(1.5em + .5rem + 2px);" onchange="this.form.submit()">
        <option value="todas">Categoria</option>
        @foreach($categorias as $categoria)
            <option value="{{ $categoria->id }}" {{ request('categoria_id') == $categoria->id ? 'selected' : '' }}>
                {{ $categoria->nombre }}
            </option>
        @endforeach
    </select>

    <div class="form-check d-flex align-items-center mb-0" style="height: 100%;">
        <input type="checkbox" class="form-check-input" name="solo_stock" id="solo_stock" value="1" {{ request()->boolean('solo_stock', false) ? 'checked' : '' }}>
        <label class="form-check-label ms-1" for="solo_stock" style="font-size: 12px;">Solo con stock</label>
    </div>

    <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary btn-sm" style="border-radius: 0;">
        <i class="bi bi-arrow-clockwise"></i>
    </a>
    <a href="{{ route('productos.create') }}" class="btn btn-primary btn-sm" style="border-radius: 0;">
        <i class="bi bi-plus-circle"></i> Nuevo
    </a>
    <a href="{{ route('categorias.index') }}" class="btn btn-primary btn-sm" style="border-radius: 0;">
        <i class="bi bi-plus-circle"></i> Categorías
    </a>
    <a href="{{ route('informes.stock') }}" class="btn btn-primary btn-sm rounded-0 fw-semibold small">
        <i class="bi bi-box"></i> Stock
    </a>    
</form>
    <div class="ms-auto">
        {{ $productos->links() }}
    </div>
@endpush
