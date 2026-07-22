@extends('layouts.app')

@section('content')
<div class="container py-3">
    <h3>Registrar Compra</h3>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('compras.store') }}" method="POST" id="form-compra">
        @csrf

        <div class="row mb-3">
            <div class="col-md-4">
                <label>Proveedor</label>
                <select name="proveedor_id" class="form-select form-select-sm" required>
                    <option value="">Seleccionar</option>
                    @foreach($proveedores as $p)
                        <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label>Forma de pago</label>
                <select name="forma_pago_id" class="form-select form-select-sm" required>
                    <option value="">Seleccionar</option>
                    @foreach($formasPago as $f)
                        <option value="{{ $f->id }}">{{ $f->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label>Fecha</label>
                <input type="date" name="fecha" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-4">
                <label>Nro. Comprobante</label>
                <input type="text" name="numero_comprobante" class="form-control form-control-sm" value="">
            </div>            
        </div>

        <table class="table table-bordered table-sm">
            <thead class="table-light">
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio compra</th>
                    <th>Subtotal</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody id="productos-body">
                <tr class="fila-producto">
<td style="position: relative;">
    <div class="input-group input-group-sm">
        <input type="text" class="form-control form-control-sm producto-buscar" placeholder="Buscar producto...">
        <button type="button" class="btn btn-success btn-sm btn-nuevo-producto">+</button>
    </div>
    <input type="hidden" name="productos[0][id]" class="producto-id">
    <div class="resultados position-absolute bg-white border w-100"></div>
</td>

                    <td><input type="number" name="productos[0][cantidad]" class="form-control form-control-sm cantidad" min="1" value="1"></td>
                    <td><input type="number" name="productos[0][precio_compra]" class="form-control form-control-sm precio" step="0.01" value="0"></td>
                    <td class="subtotal">$0.00</td>
                    <td><button type="button" class="btn btn-danger btn-sm eliminar-fila">Eliminar</button></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-end"><strong>Total:</strong></td>
                    <td colspan="2" id="total-compra">$0.00</td>
                </tr>
            </tfoot>
        </table>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary btn-sm me-2" id="btn-guardar">Guardar Compra</button>
            <a href="{{ route('compras.index') }}" class="btn btn-secondary btn-sm">Cancelar</a>
        </div>
    </form>
</div>

<!-- Modal nuevo producto -->
 <div class="modal fade" id="modalNuevoProducto" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formNuevoProducto">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Nuevo Producto</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-2">
            <label>Nombre</label>
            <input type="text" name="nombre" class="form-control" required>
          </div>
<div class="mb-3">
    <label for="categoria_id" class="form-label">Categoría</label>
    <select name="categoria_id" id="categoria_id" class="form-select" required>
        <option value="">Seleccionar...</option>
        @foreach ($categorias as $categoria)
            <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
        @endforeach
    </select>
</div>          
          <div class="mb-2">
            <label>Precio compra</label>
            <input type="number" name="precio_compra" class="form-control" step="0.01" required>
          </div>
          <div class="mb-2">
            <label>Precio venta</label>
            <input type="number" name="precio_venta" class="form-control" step="0.01" required>
          </div>
          <div class="mb-2">
            <label>Stock inicial</label>
            <input type="number" name="stock" class="form-control" value="0">
          </div>
          <div class="mb-2">
            <label>Fecha Vencimiento</label>
            <input type="date" name="fecha_vencimiento" class="form-control">
          </div>          
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>
<style>
    .resultados {
    max-height: 200px;       /* altura máxima del dropdown */
    overflow-y: auto;        /* scroll si hay muchos productos */
    position: absolute;
    top: 100%;               /* justo debajo del input */
    left: 0;
    width: 100%;
    z-index: 1050;           /* encima de otros elementos */
    background-color: #fff;  
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
}

.resultados div {
    padding: 0.25rem 0.5rem;
    cursor: pointer;
}

.resultados div:hover {
    background-color: #e9ecef;
}
</style>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    let filaActual = null;

    const formCompra = document.getElementById('form-compra');
    const btnGuardar = document.getElementById('btn-guardar');

    function actualizarTotales() {
        let total = 0;
        document.querySelectorAll('#productos-body tr').forEach(fila => {
            let cant = parseFloat(fila.querySelector('.cantidad').value) || 0;
            let precio = parseFloat(fila.querySelector('.precio').value) || 0;
            let subtotal = cant * precio;
            fila.querySelector('.subtotal').textContent = '$' + subtotal.toFixed(2);
            total += subtotal;
        });
        document.getElementById('total-compra').textContent = '$' + total.toFixed(2);
    }

    function actualizarIndices() {
        document.querySelectorAll('#productos-body tr').forEach((fila, index) => {
            fila.querySelector('.producto-id').setAttribute('name', `productos[${index}][id]`);
            fila.querySelector('.cantidad').setAttribute('name', `productos[${index}][cantidad]`);
            fila.querySelector('.precio').setAttribute('name', `productos[${index}][precio_compra]`);
        });
    }

    function nuevaFila() {
        const tbody = document.getElementById('productos-body');
        const fila = document.createElement('tr');
        fila.classList.add('fila-producto');
        fila.innerHTML = `
            <td style="position: relative;">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control form-control-sm producto-buscar" placeholder="Buscar producto...">
                    <button type="button" class="btn btn-success btn-sm btn-nuevo-producto">+</button>
                </div>
                <input type="hidden" name="" class="producto-id">
                <div class="resultados position-absolute bg-white border w-100"></div>
            </td>
            <td><input type="number" name="" class="form-control form-control-sm cantidad" min="1" value="1"></td>
            <td><input type="number" name="" class="form-control form-control-sm precio" step="0.01" value="0"></td>
            <td class="subtotal">$0.00</td>
            <td><button type="button" class="btn btn-danger btn-sm eliminar-fila">Eliminar</button></td>
        `;
        tbody.appendChild(fila);
        attachEvents(fila);
        actualizarIndices();
    }

    function attachEvents(fila) {
        const cantidad = fila.querySelector('.cantidad');
        const precio = fila.querySelector('.precio');
        const eliminar = fila.querySelector('.eliminar-fila');
        const inputProd = fila.querySelector('.producto-buscar');
        const inputId = fila.querySelector('.producto-id');
        const btnNuevo = fila.querySelector('.btn-nuevo-producto');

        cantidad.addEventListener('input', actualizarTotales);
        precio.addEventListener('input', actualizarTotales);
        eliminar.addEventListener('click', () => {
            fila.remove();
            actualizarTotales();
            actualizarIndices();
        });

        inputProd.addEventListener('keyup', function(e) {
    const resultados = fila.querySelector('.resultados');
    if(this.value.length < 2) { 
        resultados.innerHTML = ''; 
        resultados.style.display = 'none';
        return; 
    }

    fetch(`/productos/buscar?term=${this.value}`)
        .then(res => res.json())
        .then(data => {
            resultados.innerHTML = '';
            if(data.length === 0){
                resultados.style.display = 'none';
                return;
            }
            data.forEach(p => {
                const div = document.createElement('div');
                div.textContent = p.nombre;
                div.addEventListener('click', function() {
                    inputProd.value = p.nombre;
                    fila.querySelector('.precio').value = p.precio_compra ?? 0;
                    fila.querySelector('.producto-id').value = p.id;
                    resultados.innerHTML = '';
                    resultados.style.display = 'none';
                    actualizarTotales();

                    if (!document.querySelectorAll('#productos-body tr .producto-id[value=""]').length) {
                        nuevaFila();
                    }
                    actualizarIndices();
                });
                resultados.appendChild(div);
            });
            resultados.style.display = 'block';
        });
});

// cerrar dropdown si se hace click afuera
document.addEventListener('click', function(e) {
    if(!fila.contains(e.target)) {
        fila.querySelector('.resultados').style.display = 'none';
    }
});


        btnNuevo.addEventListener('click', function() {
            filaActual = fila;
            const modal = new bootstrap.Modal(document.getElementById('modalNuevoProducto'));
            modal.show();
        });
    }

    document.querySelectorAll('#productos-body tr').forEach(fila => attachEvents(fila));

// Guardar nuevo producto desde el modal
document.getElementById('formNuevoProducto').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const modalElement = document.getElementById('modalNuevoProducto');
    const modal = bootstrap.Modal.getInstance(modalElement);

    try {
        const res = await fetch("{{ route('productos.store') }}", {
            method: "POST",
            body: formData,
            headers: {
                "X-CSRF-TOKEN": document.querySelector('input[name=_token]').value,
                "Accept": "application/json" // clave para que Laravel devuelva JSON
            }
        });

        if (!res.ok) {
            // Si Laravel devuelve errores de validación
            const errorData = await res.json();
            const mensajes = Object.values(errorData.errors).flat();
            alert("Errores:\n" + mensajes.join("\n"));
            return;
        }

        const data = await res.json();

        // Cerrar modal y resetear formulario
        modal.hide();
        this.reset();

        if (filaActual) {
            filaActual.querySelector('.producto-buscar').value = data.producto.nombre;
            filaActual.querySelector('.precio').value = data.producto.precio_compra ?? 0;
            filaActual.querySelector('.producto-id').value = data.producto.id;

            // Crear nueva fila si no hay filas vacías
            if (!document.querySelectorAll('#productos-body tr .producto-id[value=""]').length) {
                nuevaFila();
            }

            actualizarTotales();
            actualizarIndices();
        }

    } catch (err) {
        console.error("Error en fetch:", err);
        alert("Error al guardar el producto. Revisa la consola para más detalles.");
    }
});


    // Prevención de envío si no hay productos
    formCompra.addEventListener('submit', function(e) {
        btnGuardar.disabled = true;

        document.querySelectorAll('#productos-body tr').forEach(fila => {
            const prodId = fila.querySelector('.producto-id').value;
            if (!prodId) fila.remove();
        });

        if (!document.querySelectorAll('#productos-body tr .producto-id').length) {
            e.preventDefault();
            btnGuardar.disabled = false;
            alert('Debe seleccionar al menos un producto');
            return false;
        }
    });

});

</script>
@endpush
@endsection

