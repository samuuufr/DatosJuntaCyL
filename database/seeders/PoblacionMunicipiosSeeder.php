<?php

namespace Database\Seeders;

use App\Models\Municipio;
use Illuminate\Database\Seeder;

class PoblacionMunicipiosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Datos de población aproximados (2023) para municipios principales de Castilla y León
        $poblaciones = [
            // Valladolid
            '47186' => 295000, // Valladolid
            '47116' => 21000,  // Medina del Campo
            '47095' => 8000,   // Laguna de Duero

            // Burgos
            '09059' => 175000, // Burgos
            '09002' => 6000,   // Aranda de Duero
            '09256' => 9000,   // Miranda de Ebro

            // Salamanca
            '37274' => 144000, // Salamanca
            '37026' => 4000,   // Béjar
            '37070' => 14000,  // Ciudad Rodrigo

            // León
            '24089' => 124000, // León
            '24149' => 37000,  // Ponferrada
            '24089' => 124000, // León

            // Palencia
            '34120' => 77000,  // Palencia

            // Zamora
            '49275' => 60000,  // Zamora
            '49019' => 10000,  // Benavente

            // Ávila
            '05019' => 58000,  // Ávila
            '05012' => 5000,   // Arenas de San Pedro

            // Segovia
            '40194' => 51000,  // Segovia
            '40048' => 6000,   // Cuéllar

            // Soria
            '42173' => 39000,  // Soria
        ];

        $actualizados = 0;
        $noEncontrados = [];

        foreach ($poblaciones as $codigoIne => $poblacion) {
            $municipio = Municipio::where('codigo_ine', $codigoIne)->first();

            if ($municipio) {
                $municipio->poblacion = $poblacion;
                $municipio->save();
                $actualizados++;
                $this->command->info("✓ {$municipio->nombre}: " . number_format($poblacion, 0, ',', '.') . " hab.");
            } else {
                $noEncontrados[] = $codigoIne;
            }
        }

        $this->command->info("\n✓ Total municipios actualizados: {$actualizados}");

        if (count($noEncontrados) > 0) {
            $this->command->warn("⚠ Códigos INE no encontrados: " . implode(', ', $noEncontrados));
        }
    }
}
