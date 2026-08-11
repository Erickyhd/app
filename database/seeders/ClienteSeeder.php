<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('clientes')->insert([
            [
                'nombre' => 'Juan Perez',
                'email' => 'juan@ejemplo.com',
                'telefono' => '987654321',
                'direccion' => 'Calle Falsa 123',
                'estado' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Empresa S.A.C.',
                'email' => 'ventas@empresa.com',
                'telefono' => '01-1234567',
                'direccion' => 'Av. Industrial 456',
                'estado' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
