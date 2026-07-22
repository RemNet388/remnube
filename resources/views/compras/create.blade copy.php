@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4 text-uppercase">Nueva Compra</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('compras.store') }}" method="POST" id="formCompra">
        @csrf

        <!-- DATOS DE COMPRA -->
        <div class="row mb-3">
            <div class="col-md-3">
                <label class="form-label">Fecha</label>
                <input type="date" name="fecha" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Proveedor</label>
                <select name="proveedor_id" class="form-control form-control-sm" required>
                    <option value="">-- Seleccione --</option>
                    @foreach($proveedores as $proveedor)
                        <option value="{{ $proveedor->id }}">{{ $proveedor->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Número de comprobante</label>
                <input type="text" name="numero_comprobante" class="form-control form-control-sm" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Forma de Pago</label>
                <select name="forma_pago_id" id="forma_pago_id" class="form-control form-control-sm" required>
                    <option value="">-- Seleccione --</option>
                    @foreach($formasPago as $forma)
                        <option value="{{ $forma->id }}">{{ $forma->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- BUSCADOR + BOTÓN NUEVO PRODUCTO -->
        <div class="row mb-3 align-items-end">
            <div class="col-md-8 position-relative" id="buscador-wrapper">
                <label class="form-label">Buscar Producto</label>
                <div class="d-flex">
                    <input type="text" id="buscador" class="form-control form-control-sm" placeholder="Escriba para buscar...">
                    <button type="button" class="btn btn-success btn-sm ms-1" data-bs-toggle="modal" data-bs-target="#modalProducto">+ Nuevo</button>
                </div>
                <div id="resultados" class="list-group mt-1"></div>
            </div>
        </div>

        <!-- TABLA DE PRODUCTOS -->
        <table class="table table-bordered table-sm" id="tabla-productos">
            <thead class="table-light">
                <tr>
                    <th>Producto</th>
                    <th>Cant.</th>
                    <th>Precio compra</th>
                    <th>Subtotal</th>
                    <th></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        <div class="d-flex justify-content-end mb-3">
            <h5>Total: $<span id="total">0.00</span></h5>
        </div>

        <button type="submit" class="btn btn-primary w-100">Guardar Compra</button>
    </form>
</div>

<!-- MODAL NUEVO PRODUCTO -->
<div class="modal fade" id="modalProducto" tabindex="-1" aria-labelledby="modalProductoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formNuevoProducto">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalProductoLabel">Nuevo Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label>Nombre</label>
                        <input type="text" name="nombre" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-2">
                        <label>Categoría</label>
                        <select name="categoria_id" class="form-control form-control-sm" required>
                            <option value="">-- Seleccione --</option>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label>Precio compra</label>
                        <input type="number" name="precio_compra" step="0.01" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-2">
                        <label>Precio venta</label>
                        <input type="number" name="precio_venta" step="0.01" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-2">
                        <label>Stock inicial</label>
                        <input type="number" name="stock" class="form-control form-control-sm" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-sm">Guardar</button>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
#resultados {
    max-height: 200px;
    overflow-y: auto;
    border: 1px solid #ddd;
    border-radius: 4px;
    position: absolute;
    width: 100%;
    z-index: 1000;
    background: #fff;
}
#buscador-wrapper {
    position: relative;
}
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function(){

    // BUSCADOR DE PRODUCTOS
    $('#buscador').on('keyup', function(){
        let q = $(this).val().trim();
        if(q.length < 2) { $('#resultados').html(''); return; }

        $.get('{{ route("productos.buscar") }}', { term: q }, function(data){
            let html = '';
            data.slice(0,10).forEach(prod => {
                html += `<a href="#" class="list-group-item list-group-item-action seleccionar-producto"
                             data-id="${prod.id}" data-nombre="${prod.nombre}" data-precio="${prod.precio_compra}">${prod.nombre}</a>`;
            });
            $('#resultados').html(html);
        });
    });

    // SELECCIONAR PRODUCTO
    $(document).on('click', '.seleccionar-producto', function(e){
        e.preventDefault();
        agregarProductoTabla($(this).data('id'), $(this).data('nombre'), $(this).data('precio'));
        $('#resultados').html('');
        $('#buscador').val('');
    });

    function agregarProductoTabla(id, nombre, precio){
        if($(`#producto-${id}`).length){ alert('Este producto ya está en la lista'); return; }
        $('#tabla-productos tbody').append(`
            <tr id="producto-${id}">
                <td><input type="hidden" name="productos[${id}][id]" value="${id}">${nombre}</td>
                <td><input type="number" name="productos[${id}][cantidad]" value="1" class="form-control form-control-sm cantidad"></td>
                <td><input type="number" name="productos[${id}][precio_compra]" step="0.01" value="${precio}" class="form-control form-control-sm precio"></td>
                <td class="subtotal">0.00</td>
                <td><button type="button" class="btn btn-danger btn-sm eliminar">X</button></td>
            </tr>
        `);
        calcularTotal();
    }

    // ACTUALIZAR SUBTOTAL Y TOTAL
    $(document).on('input', '.cantidad, .precio', calcularTotal);
    $(document).on('click', '.eliminar', function(){ $(this).closest('tr').remove(); calcularTotal(); });

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

    // NUEVO PRODUCTO DESDE MODAL
    $('#formNuevoProducto').on('submit', function(e){
        e.preventDefault();
        let formData = new FormData(this);
        $.ajax({
            url: "{{ route('productos.store') }}",
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(producto){
                $('#modalProducto').modal('hide');
                $('#formNuevoProducto')[0].reset();
                agregarProductoTabla(producto.id, producto.nombre, producto.precio_compra);
            },
            error: function(xhr){ alert("Error al guardar el producto"); console.error(xhr.responseText); }
        });
    });

    // GUARDAR COMPRA CON CUENTA CORRIENTE
    $('#formCompra').on('submit', function(){
        let formaPago = $('#forma_pago_id').val();
        if(formaPago == 2){
            $('<input>').attr({type:'hidden', name:'cuenta_corriente', value:'1'}).appendTo('#formCompra');
        }
    });

});
</script>
@endpush
