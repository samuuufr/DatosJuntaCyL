<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Municipio;
use App\Models\Provincia;

class MunicipioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener IDs de provincias por código INE
        $avila = Provincia::where('codigo_ine', '05')->first()->id;
        $burgos = Provincia::where('codigo_ine', '09')->first()->id;
        $leon = Provincia::where('codigo_ine', '24')->first()->id;
        $palencia = Provincia::where('codigo_ine', '34')->first()->id;
        $salamanca = Provincia::where('codigo_ine', '37')->first()->id;
        $segovia = Provincia::where('codigo_ine', '40')->first()->id;
        $soria = Provincia::where('codigo_ine', '42')->first()->id;
        $valladolid = Provincia::where('codigo_ine', '47')->first()->id;
        $zamora = Provincia::where('codigo_ine', '49')->first()->id;

        $municipios = [
            // Ávila
            ['provincia_id' => $avila, 'codigo_ine' => '05019', 'nombre' => 'Ávila'],
            ['provincia_id' => $avila, 'codigo_ine' => '05015', 'nombre' => 'Arévalo'],
            ['provincia_id' => $avila, 'codigo_ine' => '05025', 'nombre' => 'Arenas de San Pedro'],

            // Burgos
            ['provincia_id' => $burgos, 'codigo_ine' => '09059', 'nombre' => 'Burgos'],
            ['provincia_id' => $burgos, 'codigo_ine' => '09019', 'nombre' => 'Aranda de Duero'],
            ['provincia_id' => $burgos, 'codigo_ine' => '09261', 'nombre' => 'Miranda de Ebro'],

            // León
            ['provincia_id' => $leon, 'codigo_ine' => '24089', 'nombre' => 'León'],
            ['provincia_id' => $leon, 'codigo_ine' => '24155', 'nombre' => 'Ponferrada'],
            ['provincia_id' => $leon, 'codigo_ine' => '24007', 'nombre' => 'Astorga'],

            // Palencia
            ['provincia_id' => $palencia, 'codigo_ine' => '34120', 'nombre' => 'Palencia'],
            ['provincia_id' => $palencia, 'codigo_ine' => '34023', 'nombre' => 'Aguilar de Campoo'],
            ['provincia_id' => $palencia, 'codigo_ine' => '34049', 'nombre' => 'Carrión de los Condes'],

            // Salamanca
            ['provincia_id' => $salamanca, 'codigo_ine' => '37274', 'nombre' => 'Salamanca'],
            ['provincia_id' => $salamanca, 'codigo_ine' => '37046', 'nombre' => 'Béjar'],
            ['provincia_id' => $salamanca, 'codigo_ine' => '37085', 'nombre' => 'Ciudad Rodrigo'],

            // Segovia
            ['provincia_id' => $segovia, 'codigo_ine' => '40194', 'nombre' => 'Segovia'],
            ['provincia_id' => $segovia, 'codigo_ine' => '40074', 'nombre' => 'Cuéllar'],
            ['provincia_id' => $segovia, 'codigo_ine' => '40903', 'nombre' => 'San Ildefonso o La Granja'],

            // Soria
            ['provincia_id' => $soria, 'codigo_ine' => '42173', 'nombre' => 'Soria'],
            ['provincia_id' => $soria, 'codigo_ine' => '42003', 'nombre' => 'Ágreda'],
            ['provincia_id' => $soria, 'codigo_ine' => '42035', 'nombre' => 'Burgo de Osma-Ciudad de Osma'],

            // Valladolid
            ['provincia_id' => $valladolid, 'codigo_ine' => '47186', 'nombre' => 'Valladolid'],
            ['provincia_id' => $valladolid, 'codigo_ine' => '47102', 'nombre' => 'Medina del Campo'],
            ['provincia_id' => $valladolid, 'codigo_ine' => '47085', 'nombre' => 'Laguna de Duero'],

            // Zamora
            ['provincia_id' => $zamora, 'codigo_ine' => '49275', 'nombre' => 'Zamora'],
            ['provincia_id' => $zamora, 'codigo_ine' => '49028', 'nombre' => 'Benavente'],
            ['provincia_id' => $zamora, 'codigo_ine' => '49217', 'nombre' => 'Toro'],
        ];

        foreach ($municipios as $municipio) {
            Municipio::updateOrCreate(
                ['codigo_ine' => $municipio['codigo_ine']],
                [
                    'provincia_id' => $municipio['provincia_id'],
                    'nombre' => $municipio['nombre'],
                ]
            );
        }
    }
}
