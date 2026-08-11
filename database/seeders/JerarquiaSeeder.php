<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JerarquiaSeeder extends Seeder
{
    public function run(): void
    {
        $gerenciaId = DB::table('jerarquias')->insertGetId([
            'nombre' => 'Gerencia General',
            'descripcion' => 'Directorio y Gerencia',
            'codigo' => 'CECO-001',
            'estado' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $ventasId = DB::table('jerarquias')->insertGetId([
            'nombre' => 'Ventas',
            'descripcion' => 'Departamento de Ventas',
            'jerarquia_padre_id' => $gerenciaId,
            'codigo' => 'CECO-002',
            'estado' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('jerarquias')->insert([
            'nombre' => 'Caja',
            'descripcion' => 'Gestión de cobros',
            'jerarquia_padre_id' => $ventasId,
            'codigo' => 'CECO-003',
            'estado' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
