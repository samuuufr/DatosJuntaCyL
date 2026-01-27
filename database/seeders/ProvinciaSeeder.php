<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Provincia;

class ProvinciaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $provincias = [
            ['codigo_ine' => '05', 'nombre' => 'Ávila'],
            ['codigo_ine' => '09', 'nombre' => 'Burgos'],
            ['codigo_ine' => '24', 'nombre' => 'León'],
            ['codigo_ine' => '34', 'nombre' => 'Palencia'],
            ['codigo_ine' => '37', 'nombre' => 'Salamanca'],
            ['codigo_ine' => '40', 'nombre' => 'Segovia'],
            ['codigo_ine' => '42', 'nombre' => 'Soria'],
            ['codigo_ine' => '47', 'nombre' => 'Valladolid'],
            ['codigo_ine' => '49', 'nombre' => 'Zamora'],
        ];

        foreach ($provincias as $provincia) {
            Provincia::create($provincia);
        }
    }
}
