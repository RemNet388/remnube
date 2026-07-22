@extends('layouts.app')

@section('content')
<div class="container my-4">
    <h3 style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">Informe de Ganancias</h3>

    <form method="GET" class="mb-3">
        <div class="row g-2">
            <div class="col-md-3">
                <input type="date" name="fecha_desde" class="form-control form-control-sm" value="{{ request('fecha_desde') }}">
            </div>
            <div class="col-md-3">
                <input type="date" name="fecha_hasta" class="form-control form-control-sm" value="{{ request('fecha_hasta') }}">
            </div>
            <div class="col-md-3">
                <select name="producto_id" class="form-control form-control-sm">
                    <option value="">-- Todos los productos --</option>
                    @foreach($productos as $producto)
                        <option value="{{ $producto->id }}" {{ request('producto_id') == $producto->id ? 'selected' : '' }}>
                            {{ $producto->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary btn-sm w-100">Filtrar</button>
            </div>
        </div>
    </form>

    <table class="table table-bordered table-striped table-sm" style="border-radius:0; font-size:0.85rem; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
        <thead class="table-light">
            <tr>
                <th>Fecha</th>
                <th>Nº</th>
                <th>Producto</th>
                <th>Costo</th>
                <th>Precio</th>
                <th>Un.</th>
                <th>Ganancia</th>
            </tr>
        </thead>
        <tbody>
            @foreach($detalles as $detalle)
            <tr>
                <td>{{ $detalle->venta->fecha ?? '' }}</td>
                <td>
                    <a href="#" class="text-primary" data-bs-toggle="modal" data-bs-target="#detalleModal{{ $detalle->venta->id }}">
                        #{{ $detalle->venta->id }}
                    </a>

                    <!-- Modal detalle venta -->
                    <div class="modal fade" id="detalleModal{{ $detalle->venta->id }}" tabindex="-1" aria-labelledby="detalleModalLabel{{ $detalle->venta->id }}" aria-hidden="true">
                      <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">Detalle Venta #{{ $detalle->venta->id }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                          </div>
                          <div class="modal-body">
                            <table class="table table-bordered table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Precio Unitario</th>
                                        <th>Cantidad</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($detalle->venta->detalles as $d)
                                    <tr>
                                        <td>{{ $d->producto->nombre ?? 'Sin Producto' }}</td>
                                        <td>${{ number_format($d->precio_unitario,2) }}</td>
                                        <td>{{ $d->cantidad }}</td>
                                        <td>${{ number_format($d->subtotal,2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
                          </div>
                        </div>
                      </div>
                    </div>
                </td>
                <td>
                    @if($detalle->producto)
                        <a href="#" class="text-primary" data-bs-toggle="modal" data-bs-target="#productoModal{{ $detalle->producto->id }}">
                            {{ $detalle->producto->nombre }}
                        </a>

                        <!-- Modal producto -->
                        <div class="modal fade" id="productoModal{{ $detalle->producto->id }}" tabindex="-1" aria-labelledby="productoModalLabel{{ $detalle->producto->id }}" aria-hidden="true">
                          <div class="modal-dialog">
                            <div class="modal-content">
                              <form method="POST" action="{{ route('productos.update', $detalle->producto->id) }}">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                  <h5 class="modal-title">Editar Producto</h5>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                </div>
                                <div class="modal-body">
                                  <div class="mb-2">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" name="nombre" class="form-control form-control-sm" value="{{ $detalle->producto->nombre }}">
                                  </div>
                                  <div class="mb-2">
                                    <label class="form-label">Precio Compra</label>
                                    <input type="number" step="0.01" name="precio_compra" class="form-control form-control-sm" value="{{ $detalle->producto->precio_compra }}">
                                  </div>
                                  <div class="mb-2">
                                    <label class="form-label">Precio Venta</label>
                                    <input type="number" step="0.01" name="precio_venta" class="form-control form-control-sm" value="{{ $detalle->producto->precio_venta }}">
                                  </div>
                                  <div class="mb-2">
                                    <label class="form-label">Stock</label>
                                    <input type="number" name="stock" class="form-control form-control-sm" value="{{ $detalle->producto->stock }}">
                                  </div>
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                                  <button type="submit" class="btn btn-success btn-sm">Guardar</button>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                    @else
                        Sin Producto
                    @endif
                </td>
                <td>${{ number_format($detalle->producto->precio_compra ?? 0, 2) }}</td>
                <td>${{ number_format($detalle->precio_unitario, 2) }}</td>
                <td>{{ $detalle->cantidad }}</td>
                <td>${{ number_format($detalle->ganancia, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="6">Total Ganancia</th>
                <th>${{ number_format($totalGanancia,2) }}</th>
            </tr>
        </tfoot>
    </table>
</div>
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
