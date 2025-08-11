<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesYPermisosSeeder extends Seeder
{
    public function run()
    {
        // Limpiar cache de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 📌 Lista de permisos
        $permisos = [
            'ver productos',
            'crear productos',
            'editar productos',
            'eliminar productos',

            'ver categorias',
            'crear categorias',
            'editar categorias',
            'eliminar categorias',

            'registrar ventas',
            'ver ventas',

            'registrar compras',
            'ver compras',

            'gestionar configuracion' // sección avanzada
        ];

        // Crear permisos
        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }

        // Crear roles
        $admin = Role::firstOrCreate(['name' => 'Administrador']);
        $gerente = Role::firstOrCreate(['name' => 'Gerente']);
        $empleado = Role::firstOrCreate(['name' => 'Empleado']);

        // Asignar permisos a Administrador (todos)
        $admin->syncPermissions(Permission::all());

        // Asignar permisos a Gerente
        $gerente->syncPermissions([
            'ver productos',
            'crear productos',
            'editar productos',
            'eliminar productos',

            'ver categorias',
            'crear categorias',
            'editar categorias',

            'registrar ventas',
            'ver ventas',

            'registrar compras',
            'ver compras'
            // SIN "gestionar configuracion"
        ]);

        // Asignar permisos a Empleado
        $empleado->syncPermissions([
            'ver productos',
            'ver ventas',
            'registrar ventas'
        ]);
    }
}
