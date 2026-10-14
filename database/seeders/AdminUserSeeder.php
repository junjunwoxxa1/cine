<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Crear o recuperar el rol Administrador con nivel 4
        $roleId = DB::table('roles')->insertGetId([
            'name' => 'Administrador',
            'description' => 'Acceso total al sistema y administración de recursos',
            'level' => 4,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Crear el usuario Administrador vinculado al rol creado
        DB::table('users')->insert([
            'name' => 'Administrador',
            'email' => 'admin@cineplus.com',
            'email_verified_at' => now(),
            'phone' => '+123456789',
            'address' => 'Oficina Central',
            'profile_picture' => null,
            'password' => Hash::make('admin123'), // Cambia esta contraseña por una segura
            'saldo_puntos' => 0,
            'role_id' => $roleId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}