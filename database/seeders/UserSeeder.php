<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user = \App\Modules\Users\Domain\Models\User::create([
            'email' => 'admin@app.com',
            'password' => Hash::make('password'),
            'jerarquia_id' => 1, // Gerencia General
            'rango_id' => 1, // Gerente
            'estado' => 1,
            'usuario_creacion_id' => 1,
        ]);

        // Asignar el rol de Gerente General (que tiene todos los permisos)
        $user->assignRole('Gerente General');
    }
}
