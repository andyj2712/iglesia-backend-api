<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // 1. Crear los Roles obligatorios
        $adminRole = Role::create(['name' => 'Admin']);
        $liderRole = Role::create(['name' => 'Lider']);
        $servidorRole = Role::create(['name' => 'Servidor']);

        // 2. Crear el Usuario Administrador Inicial para pruebas
        User::create([
            'name' => 'Administrador Iglesia',
            'email' => 'admin@iglesia.com',
            'password' => Hash::make('admin1234'), // Contraseña temporal
            'role_id' => $adminRole->id,
        ]);
    }
}