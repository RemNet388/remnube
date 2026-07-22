@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4 text-uppercase" style="font-family: 'Roboto', sans-serif; font-weight: 500;">
        VENTA RAPIDA
    </h3>

    {{-- Mensajes de estado --}}
    @if(session('success'))
        <div class="alert alert-success alert-sm py-2 mb-2">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-sm py-2 mb-2">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-sm py-2 mb-2">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="ventaRapidaForm" action="{{ route('ventas.storeRapida') }}" method="POST" class="p-3 border bg-white shadow-sm">
        @csrf
        <input type="hidden" name="cliente_id" id="cliente_id" value="">

        <!-- Forma de Pago -->
<div class="row g-2 mb-3">
    <div class="col-md-4">
        <label class="form-label small mb-1">Forma de pago</label>
        <select class="form-select form-select-sm border-dark rounded-0" name="forma_pago_id" required>
            @foreach($formasPago as $fp)
                <option value="{{ $fp->id }}">{{ $fp->nombre }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-8">
        <label class="form-label small mb-1">Observaciones</label>
        <input type="text" name="observaciones"
               class="form-control form-control-sm border-dark rounded-0"
               placeholder="Observaciones (opcional)">
    </div>
</div>

<!-- Buscador y Observaciones -->
<div class="card bg-light border-0 shadow-sm mb-3">
    <div class="card-body py-3">

        <h6 class="text-uppercase text-muted mb-2" style="font-size:12px; letter-spacing:1px;">
            Buscar producto
        </h6>

        <div class="position-relative">
            <input type="text" id="buscarProducto"
                   class="form-control form-control-sm border-dark rounded-0"
                   placeholder="Código de barras o nombre"
                   autocomplete="off">

            <ul id="resultados"
                class="list-group position-absolute w-100 shadow-sm"
                style="top:100%; left:0; z-index:9999; display:none; font-size:13px;">
            </ul>
        </div>

    </div>
</div>


        <!-- Tabla productos -->
        <div class="table-responsive">
            <table class="table table-sm table-bordered border-dark align-middle" id="tablaProductos">
                <thead class="table-light">
                    <tr class="text-center small">
                        <th>Producto</th>
                        <th style="width:70px;">Cant</th>
                        <th style="width:90px;">Precio</th>
                        <th style="width:100px;">Subtotal</th>
                        <th style="width:40px;"></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <!-- Total -->
        <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-2">
            <strong class="text-uppercase">Total:</strong>
            <h5 class="mb-0">$<span id="total">0</span></h5>
        </div>

        <!-- Botón -->
        <div class="mt-3 text-end">
            <button type="submit" id="btnConfirmar" class="btn btn-success btn-sm rounded-0 px-3">
                Confirmar Venta
            </button>
        </div>
    </form>
</div>

<style>
#resultados {
    position: absolute;
    top: 100%;
    left: 0;
    width: 100%;
    z-index: 9999;
    display: none;
    max-height: 350px;   /* altura máxima */
    overflow-y: auto;    /* scroll vertical */
    overflow-x: hidden;  /* opcional: oculta scroll horizontal */
}

#resultados li.active {
    background-color: #0d6efd;
    color: white;
}
.card {
    border-radius: 0;
}

.form-control:focus, .form-select:focus {
    box-shadow: none;
}
</style>

<div class="modal fade" id="modalSeleccionCliente" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Seleccionar Cliente</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">

        <div class="input-group input-group-sm mb-3">
            <select id="selectCliente" class="form-select">
                <option value="">-- Seleccionar --</option>
                @foreach($clientes as $c)
                    @if($c->id != 1)
                        <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                    @endif
                @endforeach
            </select>
            <button type="button" class="btn btn-outline-primary"
                    data-bs-toggle="modal" data-bs-target="#modalNuevoCliente">
                + Nuevo
            </button>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" id="confirmarCliente" class="btn btn-success btn-sm">
          Confirmar
        </button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalNuevoCliente" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <form id="formNuevoCliente" action="{{ route('clientes.store') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Nuevo Cliente</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-2">
            <label>Nombre</label>
            <input type="text" name="nombre" class="form-control" required>
          </div>
          <div class="mb-2">
            <label>Teléfono</label>
            <input type="text" name="telefono" class="form-control">
          </div>
          <div class="mb-2">
            <label>Email</label>
            <input type="email" name="email" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-success btn-sm" type="submit">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
const form = document.getElementById('ventaRapidaForm');
const formaPago = document.querySelector('select[name="forma_pago_id"]');
const clienteInput = document.getElementById('cliente_id');

//Evita doble submit
form.addEventListener('submit', function() {
    const btn = document.getElementById('btnConfirmar');
    btn.disabled = true;
    btn.innerText = "Procesando..."; // opcional
});

// Modales
const modalSeleccionCliente = new bootstrap.Modal(document.getElementById('modalSeleccionCliente'));
const modalNuevoCliente = new bootstrap.Modal(document.getElementById('modalNuevoCliente'));

// Elementos del modal de selección
const selectCliente = document.getElementById('selectCliente');
const confirmarClienteBtn = document.getElementById('confirmarCliente');

// Formulario de nuevo cliente
const formNuevoCliente = document.getElementById('formNuevoCliente');

// --- Interceptar submit de venta rápida ---
form.addEventListener('submit', function(e) {
    if (formaPago.value == 2 && !clienteInput.value) {
        e.preventDefault();
        modalSeleccionCliente.show();
    }
});

// --- Confirmar cliente desde modal ---
confirmarClienteBtn.addEventListener('click', function() {
    if (selectCliente.value) {
        clienteInput.value = selectCliente.value;
        modalSeleccionCliente.hide();
        form.submit(); // Reintenta envío con cliente cargado
    } else {
        alert('Debes seleccionar un cliente válido');
    }
});

// --- Abrir modal de nuevo cliente desde selección ---
document.querySelector('[data-bs-target="#modalNuevoCliente"]').addEventListener('click', function() {
    modalSeleccionCliente.hide();
    modalNuevoCliente.show();
});

// --- Guardar cliente nuevo y volver al modal de selección ---
formNuevoCliente.addEventListener('submit', async function(e) {
    e.preventDefault();
    const data = new FormData(this);

    try {
        const res = await fetch(this.action, {
            method: 'POST',
            body: data,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        if (!res.ok) throw new Error('Error al guardar cliente');

        const json = await res.json();

        // Agregar cliente al select
        const option = document.createElement('option');
        option.value = json.id;
        option.text = json.nombre;
        option.selected = true;
        selectCliente.appendChild(option);

        // Cerrar modal nuevo y volver al modal de selección
        modalNuevoCliente.hide();
        modalSeleccionCliente.show();
    } catch (error) {
        alert(error.message);
    }
});
</script>


<script>
let productos = [];
let selectedIndex = -1;
const inputBuscar = document.getElementById('buscarProducto');
const listaResultados = document.getElementById('resultados');

// Evitar que Enter envíe el formulario desde el input de búsqueda
inputBuscar.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') e.preventDefault();
});

// Manejo de búsqueda y selección
inputBuscar.addEventListener('keyup', async function(e) {
    const query = this.value.trim();
    if (!query) {
        listaResultados.style.display = 'none';
        selectedIndex = -1;
        return;
    }

    // Flechas
    if (e.key === 'ArrowDown') { e.preventDefault(); moveSelection(1); return; }
    if (e.key === 'ArrowUp')   { e.preventDefault(); moveSelection(-1); return; }

    // Enter: agregar producto seleccionado
    if (e.key === 'Enter') {
        e.preventDefault();
        if (selectedIndex >= 0 && selectedIndex < listaResultados.children.length) {
            listaResultados.children[selectedIndex].click();
        } else {
            // Buscar por código exacto si no hay selección
            let res = await fetch('/productos/buscar?term=' + encodeURIComponent(query));
            let data = await res.json();
            if (data.length > 0) {
                let prod = data.find(p => p.codigo === query) || data[0];
                agregarProducto(prod);
            } else {
                alert('Producto no encontrado');
            }
        }
        inputBuscar.value = '';
        listaResultados.style.display = 'none';
        selectedIndex = -1;
        return;
    }

    // Mostrar lista solo si hay 2+ caracteres
    if (query.length < 2) {
        listaResultados.style.display = 'none';
        selectedIndex = -1;
        return;
    }

    // Fetch para autocompletar
    let res = await fetch('/productos/buscar?term=' + encodeURIComponent(query));
    let data = await res.json();
    mostrarResultados(data);
});

// Mostrar resultados en dropdown
// Mostrar resultados en dropdown
function mostrarResultados(data) {
    // Primero los que tienen stock > 0
    data.sort((a, b) => {
        if (a.stock > 0 && b.stock === 0) return -1;
        if (a.stock === 0 && b.stock > 0) return 1;
        return 0;
    });

    listaResultados.innerHTML = '';
    selectedIndex = -1;

    if (data.length === 0) { 
        listaResultados.style.display = 'none'; 
        return; 
    }

    data.forEach((prod, i) => {
        let li = document.createElement('li');
        li.classList.add('list-group-item', 'py-1', 'px-2', 'rounded-0', 'small');
        li.textContent = prod.nombre + ' - $' + prod.precio_venta + (prod.stock === 0 ? ' (Sin stock)' : '');
        li.onclick = () => {
            agregarProducto(prod);
            inputBuscar.value = '';
            listaResultados.style.display = 'none';
        };
        listaResultados.appendChild(li);
    });

    listaResultados.style.display = 'block';
}


// Flechas arriba/abajo con scroll
function moveSelection(direction) {
    if (listaResultados.children.length === 0) return;

    if (selectedIndex >= 0 && selectedIndex < listaResultados.children.length) {
        listaResultados.children[selectedIndex].classList.remove('active');
    }

    selectedIndex += direction;
    if (selectedIndex < 0) selectedIndex = listaResultados.children.length - 1;
    if (selectedIndex >= listaResultados.children.length) selectedIndex = 0;

    const item = listaResultados.children[selectedIndex];
    item.classList.add('active');

    // Scroll automático si el item activo no es visible
    const itemTop = item.offsetTop;
    const itemBottom = itemTop + item.offsetHeight;
    const scrollTop = listaResultados.scrollTop;
    const scrollBottom = scrollTop + listaResultados.clientHeight;

    if (itemTop < scrollTop) {
        listaResultados.scrollTop = itemTop;
    } else if (itemBottom > scrollBottom) {
        listaResultados.scrollTop = itemBottom - listaResultados.clientHeight;
    }
}

// Agregar producto o sumar cantidad
function agregarProducto(prod) {
    let existente = productos.find(p => p.id === prod.id);
    if (existente) {
        existente.cantidad++;
    } else {
        productos.push({...prod, cantidad: 1});
    }
    renderTabla();
}

// Render de tabla
function renderTabla() {
    let tbody = document.querySelector('#tablaProductos tbody');
    tbody.innerHTML = '';
    let total = 0;

    productos.forEach((p, i) => {
        let subtotal = p.cantidad * p.precio_venta;
        total += subtotal;

        tbody.innerHTML += `
            <tr>
                <td>${p.nombre}<input type="hidden" name="productos[${i}][id]" value="${p.id}"></td>
                
                <!-- Cantidad editable -->
                <td>
                    <input type="number" class="form-control form-control-sm"
                           name="productos[${i}][cantidad]" value="${p.cantidad}" min="1"
                           onchange="cambiarCantidad(${i}, this.value)">
                </td>
                
                <!-- Precio editable -->
                <td>
                    <input type="number" class="form-control form-control-sm"
                           name="productos[${i}][precio_venta]" value="${p.precio_venta}" min="0" step="0.01"
                           onchange="cambiarPrecio(${i}, this.value)">
                </td>
                
                <td>$${subtotal.toFixed(2)}</td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm" onclick="eliminar(${i})">X</button>
                </td>
            </tr>`;
    });

    document.getElementById('total').textContent = total.toFixed(2);
}

// Nueva función para actualizar precio
function cambiarPrecio(i, valor) {
    productos[i].precio_venta = parseFloat(valor);
    renderTabla();
}

function cambiarCantidad(i, cant) {
    productos[i].cantidad = parseInt(cant);
    renderTabla();
}

function eliminar(i) {
    productos.splice(i, 1);
    renderTabla();
}

// Ocultar resultados al click fuera
document.addEventListener('click', function(e) {
    if (!inputBuscar.contains(e.target) && !listaResultados.contains(e.target)) {
        listaResultados.style.display = 'none';
    }
});
</script>
@endsection
