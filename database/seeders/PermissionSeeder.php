<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar cache de spatie
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $modules = [
            'usuarios',
            'jerarquias',
            'rangos',
            'trabajadores',
            'roles',
            'clientes',
            'reportes',
            'configuracion'
        ];

        $actions = ['ver', 'crear', 'editar', 'eliminar'];

        // Crear permisos
        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => "{$module}.{$action}"]);
            }
        }

        // Obtener el rol de Gerente General y asignarle TODOS los permisos
        $gerente = Role::where('name', 'Gerente General')->first();
        if ($gerente) {
            $gerente->syncPermissions(Permission::all());
        }

        // Asignar permisos básicos a Admin de Tienda
        $admin = Role::where('name', 'Admin de Tienda')->first();
        if ($admin) {
            $admin->syncPermissions([
                'clientes.ver', 'clientes.crear', 'clientes.editar',
                'reportes.ver',
                'usuarios.ver'
            ]);
        }

        // Asignar permisos básicos a Cajero
        $cajero = Role::where('name', 'Cajero')->first();
        if ($cajero) {
            $cajero->syncPermissions([
                'clientes.ver', 'clientes.crear'
            ]);
        }
    }
}
