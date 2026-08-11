<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('usuarios')->insert([
            'dni' => '12345678',
            'nombres' => 'Administrator',
            'apellidos' => 'Admin',
            'email' => 'admin@app.com',
            'password' => Hash::make('password'),
            'telefono' => '999888777',
            'genero' => 'M',
            'jerarquia_id' => 1, // Gerencia General
            'rango_id' => 1, // Gerente
            'estado' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
