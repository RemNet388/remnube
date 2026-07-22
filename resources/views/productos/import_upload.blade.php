@if(session('success'))
    <div style="color: green">{{ session('success') }}</div>
@endif

@if($errors->any())
    <div style="color: red">
        @foreach($errors->all() as $error)
            {{ $error }}<br>
        @endforeach
    </div>
@endif

<form action="{{ route('productos.import.preview') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <label>Seleccionar archivo Excel o CSV:</label>
    <input type="file" name="file" required>
    <button type="submit">Previsualizar</button>
</form>
