@extends('layouts.app')

@section('content')
<div class="container">

    <h3 class="mb-4">🧪 Pruebas AFIP</h3>

    @if(session('success'))
        <pre class="alert alert-success">{{ session('success') }}</pre>
    @endif

    @if(session('error'))
        <pre class="alert alert-danger">{{ session('error') }}</pre>
    @endif

    <form action="{{ route('afip.test.enviar') }}" method="POST">
        @csrf

        <button type="submit" class="btn btn-primary">
            Probar conexión AFIP (ServerStatus)
        </button>
    </form>

</div>
@endsection
