<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar cache de spatie
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Roles jerárquicos (1 = Máxima Jerarquía)
        Role::create(['name' => 'Gerente General', 'jerarquia' => 1]); 
        Role::create(['name' => 'Admin de Tienda', 'jerarquia' => 2]); 
        Role::create(['name' => 'Cajero', 'jerarquia' => 3]);
    }
}
