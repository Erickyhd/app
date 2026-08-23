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
            'codigo' => '001',
            'estado' => 1,
            'usuario_creacion_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $subGerenciaId = DB::table('jerarquias')->insertGetId([
            'nombre' => 'Sub Gerencia',
            'descripcion' => 'Subgerencia General',
            'jerarquia_padre_id' => $gerenciaId,
            'codigo' => '002',
            'estado' => 1,
            'usuario_creacion_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $jefaturaId = DB::table('jerarquias')->insertGetId([
            'nombre' => 'Jefatura de Área',
            'descripcion' => 'Jefes de distintas áreas',
            'jerarquia_padre_id' => $subGerenciaId,
            'codigo' => '003',
            'estado' => 1,
            'usuario_creacion_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $colaboradoresId = DB::table('jerarquias')->insertGetId([
            'nombre' => 'Colaboradores',
            'descripcion' => 'Personal operativo',
            'jerarquia_padre_id' => $jefaturaId,
            'codigo' => '004',
            'estado' => 1,
            'usuario_creacion_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $asistentesId = DB::table('jerarquias')->insertGetId([
            'nombre' => 'Asistentes',
            'descripcion' => 'Personal de apoyo',
            'jerarquia_padre_id' => $colaboradoresId,
            'codigo' => '005',
            'estado' => 1,
            'usuario_creacion_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('jerarquias')->insert([
            'nombre' => 'Practicantes',
            'descripcion' => 'Personal en formación',
            'jerarquia_padre_id' => $asistentesId,
            'codigo' => '006',
            'estado' => 1,
            'usuario_creacion_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
