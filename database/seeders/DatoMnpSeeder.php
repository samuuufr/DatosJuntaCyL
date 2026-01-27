<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DatoMnp;
use App\Models\Municipio;

class DatoMnpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener algunos municipios
        $valladolid = Municipio::where('codigo_ine', '47186')->first();
        $leon = Municipio::where('codigo_ine', '24089')->first();
        $burgos = Municipio::where('codigo_ine', '09059')->first();
        $salamanca = Municipio::where('codigo_ine', '37274')->first();

        $municipios = [
            $valladolid->id => ['nacimientos' => 2500, 'defunciones' => 2800, 'matrimonios' => 800],
            $leon->id => ['nacimientos' => 1000, 'defunciones' => 1400, 'matrimonios' => 350],
            $burgos->id => ['nacimientos' => 1400, 'defunciones' => 1600, 'matrimonios' => 450],
            $salamanca->id => ['nacimientos' => 1100, 'defunciones' => 1300, 'matrimonios' => 380],
        ];

        $anos = [2020, 2021, 2022, 2023];

        foreach ($municipios as $municipio_id => $valores_base) {
            foreach ($anos as $anno) {
                // Variación aleatoria del -10% al +10%
                $factor = 1 + (rand(-10, 10) / 100);

                // Nacimientos
                DatoMnp::updateOrCreate(
                    [
                        'municipio_id' => $municipio_id,
                        'anno' => $anno,
                        'tipo_evento' => 'nacimiento',
                    ],
                    [
                        'valor' => round($valores_base['nacimientos'] * $factor),
                        'ultima_actualizacion' => now(),
                    ]
                );

                // Defunciones
                DatoMnp::updateOrCreate(
                    [
                        'municipio_id' => $municipio_id,
                        'anno' => $anno,
                        'tipo_evento' => 'defuncion',
                    ],
                    [
                        'valor' => round($valores_base['defunciones'] * $factor),
                        'ultima_actualizacion' => now(),
                    ]
                );

                // Matrimonios
                DatoMnp::updateOrCreate(
                    [
                        'municipio_id' => $municipio_id,
                        'anno' => $anno,
                        'tipo_evento' => 'matrimonio',
                    ],
                    [
                        'valor' => round($valores_base['matrimonios'] * $factor),
                        'ultima_actualizacion' => now(),
                    ]
                );
            }
        }
    }
}
