<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RangoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('rangos')->insert([
            ['nombre' => 'Gerente', 'nivel' => 100, 'descripcion' => 'Máxima autoridad', 'estado' => 1, 'usuario_creacion_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Administrador', 'nivel' => 80, 'descripcion' => 'Administrador de tienda', 'estado' => 1, 'usuario_creacion_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Senior', 'nivel' => 50, 'descripcion' => 'Empleado experimentado', 'estado' => 1, 'usuario_creacion_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Junior', 'nivel' => 20, 'descripcion' => 'Empleado nuevo', 'estado' => 1, 'usuario_creacion_id' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
