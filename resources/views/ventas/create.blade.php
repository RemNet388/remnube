@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Nueva Venta</h2>

    <form action="{{ route('ventas.store') }}" method="POST">
        @csrf

        <div class="row mb-3">
            {{-- Fecha --}}
            <div class="col-md-3">
                <label for="fecha" class="form-label">Fecha</label>
                <input type="date" name="fecha" id="fecha" class="form-control form-control-sm form-control-sm" value="{{ date('Y-m-d') }}">
            </div>
            {{-- Cliente --}}
            <div class="col-md-6">
                <label for="cliente_id" class="form-label">Cliente</label>
                <select name="cliente_id" id="cliente_id" class="form-select form-select-sm">
                    <option value="">Seleccione un cliente</option>
                    @foreach($clientes as $cliente)
                        <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                    @endforeach
                </select>
            </div>
            {{-- Forma de pago --}}
            <div class="col-md-3">
                <label for="forma_pago_id" class="form-label">Forma de Pago</label>
                    <select name="forma_pago_id" class="form-control form-control-sm form-control-sm">
                    @foreach ($formasPago as $forma)
                        <option value="{{ $forma->id }}">{{ $forma->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <hr>

        {{-- Buscador --}}
        <div class="mb-3 position-relative">
            <label for="buscar_producto" class="form-label">Buscar producto</label>
            <input type="text" id="buscar_producto" class="form-control form-select-sm form-control-sm" placeholder="Escriba el nombre del producto...">
            <ul id="lista_productos" class="list-group mt-1" style="display:none; position:absolute; z-index:1000; width:100%;"></ul>
        </div>

        {{-- Datos producto seleccionado --}}
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Producto</label>
                <input type="text" id="producto_nombre" class="form-control form-control-sm form-control-sm" readonly>
                <input type="hidden" id="producto_id">
            </div>
            <div class="col-md-2">
                <label class="form-label">Stock</label>
                <input type="number" id="producto_stock" class="form-control form-control-sm form-control-sm" readonly>
            </div>
            <div class="col-md-2">
                <label class="form-label">Precio</label>
                <input type="number" id="producto_precio" class="form-control form-control-sm form-control-sm" min="0" step="0.01">
            </div>
            <div class="col-md-2">
                <label class="form-label">Cantidad</label>
                <input type="number" id="producto_cantidad" class="form-control form-control-sm form-control-sm" min="1">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" id="agregar_producto" class="btn btn-primary w-100">Agregar</button>
            </div>
        </div>

        <hr>

        {{-- Tabla detalle --}}
        <table class="table table-bordered" id="tabla_detalle">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unit.</th>
                    <th>Subtotal</th>
                    <th></th>
                </tr>
            </thead>
            <tbody></tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-end">Total:</th>
                    <th id="total_venta">0.00</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>

        <button type="submit" class="btn btn-success">Guardar Venta</button>
    </form>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(function() {
    let rowIndex = 0;

    // Búsqueda en vivo
    $('#buscar_producto').on('input', function() {
        const term = $(this).val();
        if (term.length >= 2) {
            $.getJSON('{{ route("productos.buscar") }}', { term })
                .done(function(data) {
                    let lista = '';
                    if (data.length === 0) {
                        lista = '<li class="list-group-item text-muted">No se encontraron productos</li>';
                    } else {
                        data.forEach(function(producto) {
                            lista += `<li class="list-group-item producto-item"
                                         data-id="${producto.id}"
                                         data-nombre="${producto.nombre}"
                                         data-precio="${producto.precio_venta}"
                                         data-stock="${producto.stock}">
                                        ${producto.nombre} - Stock: ${producto.stock} - $${producto.precio_venta}
                                      </li>`;
                        });
                    }
                    $('#lista_productos').html(lista).show();
                });
        } else {
            $('#lista_productos').hide();
        }
    });

    // Seleccionar producto
    $(document).on('click', '.producto-item', function() {
        $('#producto_id').val($(this).data('id'));
        $('#producto_nombre').val($(this).data('nombre'));
        $('#producto_precio').val($(this).data('precio'));
        $('#producto_stock').val($(this).data('stock'));
        $('#producto_cantidad').val(1);
        $('#lista_productos').hide();
        $('#buscar_producto').val('');
    });

    // Agregar producto
    $('#agregar_producto').click(function() {
        const id = $('#producto_id').val();
        const nombre = $('#producto_nombre').val();
        const precio = parseFloat($('#producto_precio').val());
        const stock = parseInt($('#producto_stock').val());
        const cantidad = parseInt($('#producto_cantidad').val());

        if (!id || !cantidad || cantidad <= 0) {
            alert('Seleccione un producto y cantidad válida.');
            return;
        }
        if (cantidad > stock) {
            alert('Cantidad mayor al stock disponible.');
            return;
        }

        const subtotal = precio * cantidad;

        let fila = $(`
            <tr data-row="${rowIndex}">
                <td>${nombre}
                    <input type="hidden" name="productos[${rowIndex}][id]" value="${id}">
                    <input type="hidden" name="productos[${rowIndex}][cantidad]" value="${cantidad}">
                    <input type="hidden" name="productos[${rowIndex}][precio]" value="${precio}">
                </td>
                <td>${cantidad}</td>
                <td>${precio.toFixed(2)}</td>
                <td class="subtotal">${subtotal.toFixed(2)}</td>
                <td><button type="button" class="btn btn-danger btn-sm eliminar_fila">X</button></td>
            </tr>
        `);

        $('#tabla_detalle tbody').append(fila);
        rowIndex++;
        actualizarTotal();

        // limpiar inputs
        $('#producto_id, #producto_nombre, #producto_precio, #producto_stock, #producto_cantidad').val('');
    });

    // Eliminar fila
    $(document).on('click', '.eliminar_fila', function() {
        $(this).closest('tr').remove();
        reindexRows();
        actualizarTotal();
    });

    function reindexRows() {
        $('#tabla_detalle tbody tr').each(function(i) {
            $(this).attr('data-row', i);
            $(this).find('input[name^="productos"]').each(function() {
                const field = $(this).attr('name').split('][')[1]; // cantidad], precio]...
                $(this).attr('name', `productos[${i}][${field}`);
            });
        });
        rowIndex = $('#tabla_detalle tbody tr').length;
    }

    function actualizarTotal() {
        let total = 0;
        $('#tabla_detalle tbody tr').each(function() {
            total += parseFloat($(this).find('.subtotal').text()) || 0;
        });
        $('#total_venta').text(total.toFixed(2));
    }
});
</script>
@endsection
