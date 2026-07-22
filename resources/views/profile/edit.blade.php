<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-bold text-uppercase" style="font-family: 'Roboto', sans-serif; font-size: 18px;">
            Perfil de Usuario
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            <!-- Información del perfil -->
            <div class="p-4 bg-white shadow border rounded" style="font-family: 'Roboto', sans-serif;">
                <h3 class="fw-semibold mb-3 text-uppercase">Información del Perfil</h3>
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Cambio de contraseña -->
            <div class="p-4 bg-white shadow border rounded" style="font-family: 'Roboto', sans-serif;">
                <h3 class="fw-semibold mb-3 text-uppercase">Cambiar Contraseña</h3>
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Eliminar cuenta -->
            <div class="p-4 bg-white shadow border rounded" style="font-family: 'Roboto', sans-serif;">
                <h3 class="fw-semibold mb-3 text-uppercase text-danger">Eliminar Cuenta</h3>
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
