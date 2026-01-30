<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Orden de ejecución respetando dependencias:
        // 1. Provincias (no tiene dependencias)
        // 2. Municipios (depende de provincias)
        // 3. Usuarios (independiente)
        // 4. Datos MNP (depende de municipios)

        $this->call([
            ProvinciaSeeder::class,
            //MunicipioSeeder::class,
            UsuarioSeeder::class,
            DatoMnpSeeder::class,
        ]);
    }
}
