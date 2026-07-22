<x-app-layout>
    <div class="container py-2" style="font-family: 'Roboto', sans-serif; font-size: 0.75rem;">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h2 class="fw-bold text-uppercase">Informe de Stock (Editable)</h2>
        </div>

        <!-- Formulario editable -->
        <form method="POST" action="{{ route('informes.stock_editable.update') }}">
            @csrf
            <div class="table-responsive border rounded mb-2">
                <table class="table table-sm table-bordered mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Producto</th>
                            <th>Stock</th>
                            <th>Precio Compra</th>
                            <th>Precio Venta</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($productos as $prod)
                            <tr>
                                <td>{{ $prod->nombre }}</td>
                                <td>
                                    <input type="number" name="productos[{{ $prod->id }}][stock]" 
                                           value="{{ $prod->stock }}" 
                                           class="form-control form-control-sm text-center" style="padding: 0.25rem;">
                                </td>
                                <td>
                                    <input type="number" step="0.01" 
                                           name="productos[{{ $prod->id }}][precio_compra]" 
                                           value="{{ $prod->precio_compra }}" 
                                           class="form-control form-control-sm text-end" style="padding: 0.25rem;">
                                </td>
                                <td>
                                    <input type="number" step="0.01" 
                                           name="productos[{{ $prod->id }}][precio_venta]" 
                                           value="{{ $prod->precio_venta }}" 
                                           class="form-control form-control-sm text-end" style="padding: 0.25rem;">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
    <!-- Paginación -->
<div class="d-flex justify-content-center">
    {{ $productos->links() }}
</div>
            <!-- Botones Guardar e Imprimir -->
            <div class="d-flex justify-end gap-1">
                <button type="submit" class="btn btn-success btn-sm" style="border-radius: 0; font-family: 'Roboto', sans-serif;">
                    💾 Guardar
                </button>
                <a href="{{ route('informes.stock.imprimir') }}" 
                   class="btn btn-danger btn-sm" style="border-radius: 0; font-family: 'Roboto', sans-serif;">
                    🖨️ Imprimir
                </a>
            </div>
        </form>
    </div>
</x-app-layout>

