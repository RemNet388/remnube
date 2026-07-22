<h2>Mapear columnas</h2>

<form action="{{ route('productos.import.mapped') }}" method="POST">
    @csrf
    <input type="hidden" name="file_path" value="{{ $filePath }}">

    @foreach($headers as $header)
        <div style="margin-bottom: 10px;">
            <strong>{{ $header }}</strong>
            <select name="mapping[{{ $header }}]">
                <option value="">-- Seleccionar campo --</option>
                <option value="categoria_id">Categoría ID</option>
                <option value="nombre">Nombre</option>
                <option value="precio_compra">Precio Compra</option>
                <option value="precio_venta">Precio Venta</option>
                <option value="stock">Stock</option>
                <option value="imagen">Imagen</option>
<option value="codigo">codigo</option>
            </select>
        </div>
    @endforeach

    <button type="submit">Importar</button>
</form>
