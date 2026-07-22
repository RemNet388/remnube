@extends('layouts.app')

@section('content')
<div class="row min-vh-100 g-0">
    <!-- Columna Izquierda - Login -->
    <div class="col-md-6 d-flex align-items-center justify-content-center bg-light p-5">
        <div class="w-100" style="max-width: 400px;">
            <h2 class="mb-4 text-center">Iniciar Sesión</h2>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required autofocus>
                </div>
                <div class="mb-3">
                    <label>Contraseña</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input type="checkbox" name="remember" class="form-check-input" id="remember">
                        <label class="form-check-label" for="remember">Recordarme</label>
                    </div>
                    <a href="{{ route('password.request') }}">Olvidé mi contraseña</a>
                </div>
                <button type="submit" class="btn btn-primary w-100">Ingresar</button>
            </form>
        </div>
    </div>

    <!-- Columna Derecha - Imagen -->
    <div class="col-md-6 d-none d-md-block">
        <img src="{{ asset('images/login-side.jpg') }}" alt="Imagen" class="w-100 h-100" style="object-fit: cover;">
    </div>
</div>
@endsection
