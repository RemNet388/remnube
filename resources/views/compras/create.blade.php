@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Nueva Compra</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('compras.store') }}" method="POST">
        @csrf

        <!-- Fecha, proveedor y forma de pago -->
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="fecha" class="form-label">Fecha</label>
                <input type="date" name="fecha" id="fecha" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-4">
                <label for="proveedor_id" class="form-label">Proveedor</label>
                <select name="proveedor_id" id="proveedor_id" class="form-control form-control-sm" required>
                    <option value="">-- Seleccione --</option>
                    @foreach($proveedores as $proveedor)
                        <option value="{{ $proveedor->id }}">{{ $proveedor->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="forma_pago_id" class="form-label">Forma de Pago</label>
                <select name="forma_pago_id" id="forma_pago_id" class="form-control form-control-sm" required>
                    <option value="">-- Seleccione --</option>
                    @foreach($formasPago as $forma)
                        <option value="{{ $forma->id }}">{{ $forma->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Buscador y botón modal -->
        <div class="row mb-3">
            <div class="col-md-9">
                <label for="buscador" class="form-label">Buscar Producto</label>
                <input type="text" id="buscador" class="form-control form-control-sm" placeholder="Escriba para buscar...">
                <div id="resultados" class="list-group mt-1"></div>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#modalProducto">
                    + Nuevo Producto
                </button>
            </div>
        </div>

        <table class="table table-bordered" id="tabla-productos">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio compra</th>
                    <th>Subtotal</th>
                    <th></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        <h4>Total: $<span id="total">0.00</span></h4>

        <button type="submit" class="btn btn-primary">Guardar Compra</button>
    </form>
</div>

<!-- Modal Nuevo Producto -->
<div class="modal fade" id="modalProducto" tabindex="-1" aria-labelledby="modalProductoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formNuevoProducto">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalProductoLabel">Nuevo Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nombre</label>
                        <input type="text" name="nombre" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-3">
                        <label>Categoría</label>
                        <select name="categoria_id" class="form-control form-control-sm" required>
                            <option value="">-- Seleccione --</option>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Precio compra</label>
                        <input type="number" name="precio_compra" step="0.01" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-3">
                        <label>Precio venta</label>
                        <input type="number" name="precio_venta" step="0.01" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-3">
                        <label>Stock inicial</label>
                        <input type="number" name="stock" class="form-control form-control-sm" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(function(){

    // Buscar producto
    $('#buscador').on('keyup', function(){
        let q = $(this).val();
        if(q.length < 2) {
            $('#resultados').html('');
            return;
        }
        $.get('{{ route("productos.buscar") }}', { q: q }, function(data){
            let html = '';
            data.forEach(prod => {
                html += `<a href="#" class="list-group-item list-group-item-action seleccionar-producto"
                             data-id="${prod.id}" data-nombre="${prod.nombre}" data-precio="${prod.precio_compra}">
                             ${prod.nombre}
                         </a>`;
            });
            $('#resultados').html(html);
        });
    });

    // Seleccionar producto
    $(document).on('click', '.seleccionar-producto', function(e){
        e.preventDefault();
        let id = $(this).data('id');
        let nombre = $(this).data('nombre');
        let precio = $(this).data('precio') || 0;

        agregarProductoTabla(id, nombre, precio);

        $('#resultados').html('');
        $('#buscador').val('');
    });

    // Función para agregar producto a la tabla
    function agregarProductoTabla(id, nombre, precio){
        if($(`#producto-${id}`).length) {
            alert('Este producto ya está en la lista');
            return;
        }
        $('#tabla-productos tbody').append(`
            <tr id="producto-${id}">
                <td>
                    <input type="hidden" name="productos[${id}][id]" value="${id}">
                    ${nombre}
                </td>
                <td><input type="number" name="productos[${id}][cantidad]" value="1" class="form-control form-control-sm cantidad "></td>
                <td><input type="number" name="productos[${id}][precio_compra]" step="0.01" value="${precio}" class="form-control form-control-sm precio "></td>
                <td class="subtotal">0.00</td>
                <td><button type="button" class="btn btn-danger btn-sm eliminar">X</button></td>
            </tr>
        `);
        calcularTotal();
    }

    // Cambiar cantidad o precio
    $(document).on('input', '.cantidad, .precio', function(){
        calcularTotal();
    });

    // Eliminar producto
    $(document).on('click', '.eliminar', function(){
        $(this).closest('tr').remove();
        calcularTotal();
    });

    function calcularTotal(){
        let total = 0;
        $('#tabla-productos tbody tr').each(function(){
            let cantidad = parseFloat($(this).find('.cantidad').val()) || 0;
            let precio = parseFloat($(this).find('.precio').val()) || 0;
            let subtotal = cantidad * precio;
            $(this).find('.subtotal').text(subtotal.toFixed(2));
            total += subtotal;
        });
        $('#total').text(total.toFixed(2));
    }

    // Guardar nuevo producto desde el modal y agregarlo a la tabla
    $('#formNuevoProducto').on('submit', function(e) {
    e.preventDefault();

    let formData = new FormData(this); // Captura todos los campos, incluso imagen

    $.ajax({
        url: "{{ route('productos.store') }}",
        method: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(producto) {
            // Agregar nuevo producto al select
            $('#producto_id').append(
                `<option value="${producto.id}" selected>${producto.nombre}</option>`
            );
            $('#modalNuevoProducto').modal('hide');
            $('#formNuevoProducto')[0].reset();
        },
        error: function(xhr) {
            console.error(xhr.responseText);
            alert("Error al guardar el producto");
        }
    });
});

});
</script>
@endpush
