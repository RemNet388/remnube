<x-app-layout>
    <h1 class="text-2xl font-bold mb-4">Editar Usuario</h1>

    <form action="{{ route('users.update', $usuario) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block">Nombre</label>
            <input type="text" name="nombre" value="{{ old('nombre', $users->nombre) }}" class="border rounded w-full p-2">
            @error('nombre')
                <p class="text-red-500 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block">Email</label>
            <input type="email" name="email" value="{{ old('email', $users->email) }}" class="border rounded w-full p-2">
            @error('email')
                <p class="text-red-500 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block">Password (dejar vacío si no cambia)</label>
            <input type="password" name="password" class="border rounded w-full p-2">
            @error('password')
                <p class="text-red-500 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">💾 Guardar cambios</button>
        <a href="{{ route('users.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">⬅️ Volver</a>
    </form>
</x-app-layout>
