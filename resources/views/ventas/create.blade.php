@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Título grande, mayúscula y fuente profesional -->
    <h3 class="mb-4 text-uppercase" style="font-family: 'Roboto', sans-serif; font-weight: 500;">
        VENTA NORMAL
    </h3>

    <form id="ventaForm" action="{{ route('ventas.store') }}" method="POST">
        @csrf

        <div class="row g-2 mb-3">
            <!-- Fecha -->
            <div class="col-md-3">
                <label for="fecha" class="form-label small">Fecha</label>
                <input type="date" class="form-control form-control-sm rounded-0" name="fecha" value="{{ date('Y-m-d') }}" required>
            </div>

            <!-- Cliente -->
            <div class="col-md-5">
                <label for="cliente_id" class="form-label small">Cliente</label>
                <select name="cliente_id" id="cliente_id" class="form-select form-select-sm rounded-0" required>
                    @foreach($clientes as $cliente)
                        <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Forma de pago -->
            <div class="col-md-4">
                <label for="forma_pago_id" class="form-label small">Forma de Pago</label>
                <select name="forma_pago_id" id="forma_pago_id" class="form-select form-select-sm rounded-0" required>
                    @foreach($formasPago as $fp)
                        <option value="{{ $fp->id }}">{{ $fp->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <!-- Observaciones -->
        <div class="mb-3">
            <label for="observaciones" class="form-label small">Observaciones</label>
            <input type="text" name="observaciones" id="observaciones"
                   class="form-control form-control-sm rounded-0"
                   placeholder="Comentarios u observaciones de la venta (opcional)"
                   value="{{ old('observaciones') }}">
        </div>
<!-- Buscar productos -->
<div class="card bg-light border-0 shadow-sm mb-3">
    <div class="card-body py-3">

        <h6 class="text-uppercase text-muted mb-2"
            style="font-size:12px; letter-spacing:1px;">
            Buscar producto
        </h6>

        <div class="position-relative">
            <input type="text"
                   id="buscarProductoNormal"
                   class="form-control form-control-sm rounded-0"
                   placeholder="Código de barras o nombre"
                   autocomplete="off">

            <ul id="resultadosNormal"
                class="list-group"
                style="position:absolute; top:100%; left:0; width:100%; z-index:9999; display:none;">
            </ul>
        </div>

    </div>
</div>

        <!-- Tabla de productos -->
        <table class="table table-sm table-striped">
            <thead class="table-light">
                <tr>
                    <th>Producto</th>
                    <th>Stock</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                    <th>Subtotal</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="tbodyProductos"></tbody>
        </table>

        <div class="mb-3">
            <strong>Total: $<span id="totalVenta">0.00</span></strong>
        </div>

        <button type="submit" class="btn btn-primary btn-sm rounded-0">Guardar Venta</button>
    </form>
</div>
@endsection

@section('scripts')
<script>
let productosNormal = [];
let selectedIndexNormal = -1;

// Elementos
const formVenta = document.getElementById('ventaForm');
const formaPagoVenta = document.querySelector('select[name="forma_pago_id"]');
const clienteVenta = document.getElementById('cliente_id');

const inputBuscarNormal = document.getElementById('buscarProductoNormal');
const listaResultadosNormal = document.getElementById('resultadosNormal');
const tbodyProductos = document.getElementById('tbodyProductos');

// --- Validación cliente CC ---
formVenta.addEventListener('submit', function(e) {
    if (formaPagoVenta.value == 2 && clienteVenta.value == '1') {
        e.preventDefault();
        alert('No se puede usar Cuenta Corriente con Consumidor Final.');
        return false;
    }
});

// --- Autocomplete productos ---
inputBuscarNormal.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') e.preventDefault();
});

inputBuscarNormal.addEventListener('keyup', async function(e) {
    const query = this.value.trim();
    if (!query) {
        listaResultadosNormal.style.display = 'none';
        selectedIndexNormal = -1;
        return;
    }

    if (e.key === 'ArrowDown') { e.preventDefault(); moveSelectionNormal(1); return; }
    if (e.key === 'ArrowUp')   { e.preventDefault(); moveSelectionNormal(-1); return; }

    if (e.key === 'Enter') {
        e.preventDefault();
        if (selectedIndexNormal >= 0 && selectedIndexNormal < listaResultadosNormal.children.length) {
            listaResultadosNormal.children[selectedIndexNormal].click();
        } else {
            let res = await fetch('/productos/buscar?term=' + encodeURIComponent(query));
            let data = await res.json();
            if (data.length > 0) {
                let prod = data.find(p => p.codigo === query) || data[0];
                agregarProductoNormal(prod);
            } else {
                alert('Producto no encontrado');
            }
        }
        inputBuscarNormal.value = '';
        listaResultadosNormal.style.display = 'none';
        selectedIndexNormal = -1;
        return;
    }

    if (query.length < 2) {
        listaResultadosNormal.style.display = 'none';
        selectedIndexNormal = -1;
        return;
    }

    let res = await fetch('/productos/buscar?term=' + encodeURIComponent(query));
    let data = await res.json();
    mostrarResultadosNormal(data);
});

function mostrarResultadosNormal(data) {
    listaResultadosNormal.innerHTML = '';
    selectedIndexNormal = -1;

    if (data.length === 0) { 
        listaResultadosNormal.style.display = 'none'; 
        return; 
    }

    data.forEach((prod, i) => {
        let li = document.createElement('li');
        li.classList.add('list-group-item', 'small');
        li.innerHTML = `<strong>${prod.nombre}</strong> - Stock: ${prod.stock} - $${prod.precio_venta}`;
        li.onclick = () => {
            agregarProductoNormal(prod);
            inputBuscarNormal.value = '';
            listaResultadosNormal.style.display = 'none';
        };
        listaResultadosNormal.appendChild(li);
    });

    listaResultadosNormal.style.display = 'block';
}

function moveSelectionNormal(direction) {
    if (listaResultadosNormal.children.length === 0) return;
    if (selectedIndexNormal >= 0 && selectedIndexNormal < listaResultadosNormal.children.length) {
        listaResultadosNormal.children[selectedIndexNormal].classList.remove('active');
    }
    selectedIndexNormal += direction;
    if (selectedIndexNormal < 0) selectedIndexNormal = listaResultadosNormal.children.length - 1;
    if (selectedIndexNormal >= listaResultadosNormal.children.length) selectedIndexNormal = 0;

    const item = listaResultadosNormal.children[selectedIndexNormal];
    item.classList.add('active');

    const itemTop = item.offsetTop;
    const itemBottom = itemTop + item.offsetHeight;
    const scrollTop = listaResultadosNormal.scrollTop;
    const scrollBottom = scrollTop + listaResultadosNormal.clientHeight;

    if (itemTop < scrollTop) listaResultadosNormal.scrollTop = itemTop;
    else if (itemBottom > scrollBottom) listaResultadosNormal.scrollTop = itemBottom - listaResultadosNormal.clientHeight;
}

function agregarProductoNormal(prod) {
    let existe = productosNormal.find(p => p.id === prod.id);
    if (existe) existe.cantidad++;
    else productosNormal.push({...prod, cantidad:1});
    renderTablaNormal();
}

function renderTablaNormal() {
    tbodyProductos.innerHTML = '';
    let total = 0;
    productosNormal.forEach((p,i)=>{
        let subtotal = p.cantidad * p.precio_venta;
        total += subtotal;
        tbodyProductos.innerHTML += `
            <tr>
                <td>${p.nombre}<input type="hidden" name="productos[${i}][id]" value="${p.id}"></td>
                <td>${p.stock}</td>
                <td><input type="number" class="form-control form-control-sm rounded-0" name="productos[${i}][cantidad]" value="${p.cantidad}" min="1" onchange="cambiarCantidadNormal(${i}, this.value)"></td>
                <td><input type="number" class="form-control form-control-sm rounded-0" name="productos[${i}][precio_venta]" value="${p.precio_venta}" min="0" step="0.01" onchange="cambiarPrecioNormal(${i}, this.value)"></td>
                <td>$${subtotal.toFixed(2)}</td>
                <td><button type="button" class="btn btn-danger btn-sm rounded-0" onclick="eliminarNormal(${i})">X</button></td>
            </tr>
        `;
    });
    document.getElementById('totalVenta').textContent = total.toFixed(2);
}

function cambiarCantidadNormal(i, cant){ productosNormal[i].cantidad=parseInt(cant); renderTablaNormal(); }
function cambiarPrecioNormal(i, valor){ productosNormal[i].precio_venta=parseFloat(valor); renderTablaNormal(); }
function eliminarNormal(i){ productosNormal.splice(i,1); renderTablaNormal(); }
</script>
@endsection
