<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usuarios = [
            [
                'email' => 'admin@datosjcyl.es',
                'password' => Hash::make('admin123'),
                'nombre' => 'Administrador',
                'rol' => 'admin',
            ],
            [
                'email' => 'usuario1@ejemplo.com',
                'password' => Hash::make('password123'),
                'nombre' => 'María García',
                'rol' => 'usuario',
            ],
            [
                'email' => 'usuario2@ejemplo.com',
                'password' => Hash::make('password123'),
                'nombre' => 'Juan Pérez',
                'rol' => 'usuario',
            ],
            [
                'email' => 'usuario3@ejemplo.com',
                'password' => Hash::make('password123'),
                'nombre' => 'Ana Martínez',
                'rol' => 'usuario',
            ],
        ];

        foreach ($usuarios as $usuario) {
            Usuario::updateOrCreate(
                ['email' => $usuario['email']],
                [
                    'password' => $usuario['password'],
                    'nombre' => $usuario['nombre'],
                    'rol' => $usuario['rol'],
                ]
            );
        }
    }
}
