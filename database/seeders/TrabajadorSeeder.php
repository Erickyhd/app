<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrabajadorSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('trabajadores')->insert([
            'usuario_id' => 1,
            'tipo_documento' => 'DNI',
            'numero_documento' => '12345678',
            'nombres' => 'Super',
            'apellidos' => 'Admin',
            'fecha_nacimiento' => '1990-01-01',
            'genero' => 'M',
            'telefono_principal' => '999888777',
            'direccion' => 'Av. Principal 123',
            'fecha_contratacion' => '2026-01-01',
            'estado' => 1,
            'usuario_creacion_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
