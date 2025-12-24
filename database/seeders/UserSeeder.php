<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = [
            [
                'name' => 'Usuario Demo',
                'email' => 'usuario@demo.com',
                'password' => Hash::make('123456'),
                'role' => 'user',
            ],
            [
                'name' => 'Administrador Demo',
                'email' => 'admin@demo.com',
                'password' => Hash::make('123456'),
                'role' => 'admin',
            ],
        ];

        foreach ($usuarios as $usuario) {
            User::create($usuario);
        }
    }
}
