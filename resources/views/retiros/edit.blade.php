@extends('layouts.app')

@section('content')
<h1>Editar Usuario</h1>

<form action="{{ route('users.update', $user) }}" method="POST">
    @csrf
    @method('PUT')

    <div>
        <label for="name">Nombre</label>
        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}">
    </div>

    <div>
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}">
    </div>

    <div>
        <label for="password">Contraseña (dejar en blanco si no se cambia)</label>
        <input type="password" id="password" name="password">
    </div>

    <div>
        <label for="role">Rol</label>
        <select id="role" name="role">
            <option value="vendedor" {{ old('role', $user->role) == 'vendedor' ? 'selected' : '' }}>Vendedor</option>
            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrador</option>
        </select>
    </div>

    <button type="submit">Actualizar Usuario</button>
</form>
@endsection
