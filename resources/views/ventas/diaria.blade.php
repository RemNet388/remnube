@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Venta Rápida</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form id="ventaRapidaForm" action="{{ route('ventas.storeRapida') }}" method="POST">
        @csrf

        <!-- Selección de forma de pago -->
        <div class="mb-3">
            <label for="forma_pago_id">Forma de Pago</label>
            <select class="form-control" name="forma_pago_id" required>
                @foreach($formasPago as $fp)
                    <option value="{{ $fp->id }}">{{ $fp->nombre }}</option>
                @endforeach
            </select>
        </div>

        <!-- Buscador de productos -->
        <div class="mb-3" style="position: relative;">
            <input type="text" id="buscarProducto" class="form-control" placeholder="Código de barras o nombre">
            <ul id="resultados" class="list-group" 
                style="position: absolute; top:100%; left:0; width:100%; z-index:9999; display:none;"></ul>
        </div>

        <!-- Tabla de productos seleccionados -->
        <table class="table table-bordered" id="tablaProductos">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cant</th>
                    <th>Precio</th>
                    <th>Subtotal</th>
                    <th></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        <h3>Total: $<span id="total">0</span></h3>

        <button type="submit" class="btn btn-success">Confirmar Venta</button>
    </form>
</div>

<script>
let productos = [];

document.getElementById('buscarProducto').addEventListener('keyup', async function(e) {
    let query = this.value;

    if (query.length < 2) {
        document.getElementById('resultados').style.display = 'none';
        return;
    }

    let res = await fetch('/api/productos/buscar?q=' + query);
    let data = await res.json();

    let lista = document.getElementById('resultados');
    lista.innerHTML = '';
    data.forEach(prod => {
        let li = document.createElement('li');
        li.classList.add('list-group-item');
        li.textContent = prod.nombre + ' - $' + prod.precio;
        li.onclick = () => agregarProducto(prod);
        lista.appendChild(li);
    });
    lista.style.display = 'block';
});

function agregarProducto(prod) {
    let existente = productos.find(p => p.id === prod.id);
    if (existente) {
        existente.cantidad++;
    } else {
        productos.push({...prod, cantidad: 1});
    }
    renderTabla();
    document.getElementById('buscarProducto').value = '';
    document.getElementById('resultados').style.display = 'none';
}

function renderTabla() {
    let tbody = document.querySelector('#tablaProductos tbody');
    tbody.innerHTML = '';
    let total = 0;

    productos.forEach((p, i) => {
        let subtotal = p.cantidad * p.precio;
        total += subtotal;

        tbody.innerHTML += `
            <tr>
                <td>${p.nombre}<input type="hidden" name="productos[${i}][id]" value="${p.id}"></td>
                <td><input type="number" class="form-control form-control-sm" 
                           name="productos[${i}][cantidad]" value="${p.cantidad}" min="1"
                           onchange="cambiarCantidad(${i}, this.value)"></td>
                <td>$${p.precio}<input type="hidden" name="productos[${i}][precio]" value="${p.precio}"></td>
                <td>$${subtotal}</td>
                <td><button type="button" class="btn btn-danger btn-sm" onclick="eliminar(${i})">X</button></td>
            </tr>`;
    });

    document.getElementById('total').textContent = total;
}

function cambiarCantidad(i, cant) {
    productos[i].cantidad = parseInt(cant);
    renderTabla();
}

function eliminar(i) {
    productos.splice(i, 1);
    renderTabla();
}
</script>
@endsection
