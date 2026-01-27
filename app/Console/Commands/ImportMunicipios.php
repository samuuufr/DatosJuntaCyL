<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Municipio;
use App\Models\Provincia;

class ImportMunicipios extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'municipios:import-jcyl';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importar catálogo oficial de municipios de JCyL desde Open Data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("📡 Conectando con Open Data JCyL...");

        // URL del CSV de Municipios (del archivo DATASET-LINKS.md)
        // Usamos use_labels=true para tener cabeceras legibles y delimiter=;
        $url = 'https://analisis.datosabiertos.jcyl.es/api/explore/v2.1/catalog/datasets/registro-de-municipios-de-castilla-y-leon/exports/csv?lang=es&timezone=Europe%2FMadrid&use_labels=true&delimiter=%3B';

        $response = Http::get($url);

        if ($response->failed()) {
            $this->error("❌ Error al descargar el dataset: " . $response->status());
            return;
        }

        $csvData = $response->body();

        // Eliminar BOM (Byte Order Mark) si existe
        $csvData = preg_replace('/^\xEF\xBB\xBF/', '', $csvData);

        // Normalizar saltos de línea
        $csvData = str_replace("\r\n", "\n", $csvData);
        $lines = explode("\n", trim($csvData));

        if (count($lines) < 2) {
            $this->error("❌ El CSV descargado parece estar vacío.");
            return;
        }

        $headers = str_getcsv(array_shift($lines), ';');

        // Mapear columnas dinámicamente
        $idxCodigoMun = -1;
        $idxNombreMun = -1;
        $idxCodigoProv = -1;
        $idxNombreProv = -1;

        foreach ($headers as $i => $header) {
            $h = mb_strtolower(trim($header));
            // Ajustar según los nombres reales del dataset de JCyL

            // Priorizar Cod_INE si existe (es el código de 5 dígitos), si no buscar Cod_Municipio
            if ($h === 'cod_ine') {
                $idxCodigoMun = $i;
            } elseif ($idxCodigoMun === -1 && (str_contains($h, 'cod_municipio') || str_contains($h, 'código municipio'))) {
                $idxCodigoMun = $i;
            }

            if ($h === 'municipio' || str_contains($h, 'nombre municipio')) {
                $idxNombreMun = $i;
            }

            if (str_contains($h, 'cod_provincia') || str_contains($h, 'código provincia')) {
                $idxCodigoProv = $i;
            }

            if ($h === 'provincia' || str_contains($h, 'nombre provincia')) {
                $idxNombreProv = $i;
            }
        }

        if ($idxCodigoMun === -1 || $idxNombreMun === -1) {
            $this->error("❌ No se pudieron identificar las columnas necesarias.");
            $this->info("Cabeceras encontradas: " . implode(' | ', $headers));
            return;
        }

        $this->info("📊 Procesando " . count($lines) . " municipios...");
        $bar = $this->output->createProgressBar(count($lines));

        foreach ($lines as $line) {
            if (empty(trim($line))) continue;

            $row = str_getcsv($line, ';');

            if (count($row) < count($headers)) continue;

            $codProv = str_pad($row[$idxCodigoProv], 2, '0', STR_PAD_LEFT);
            $nomProv = trim($row[$idxNombreProv]);

            // El dataset suele traer el código completo (ej: 05001)
            $codMun = str_pad($row[$idxCodigoMun], 5, '0', STR_PAD_LEFT);
            $nomMun = trim($row[$idxNombreMun]);

            // 1. Asegurar Provincia
            $provincia = Provincia::firstOrCreate(
                ['codigo_ine' => $codProv],
                ['nombre' => $nomProv]
            );

            // 2. Crear/Actualizar Municipio
            Municipio::updateOrCreate(['codigo_ine' => $codMun], ['nombre' => $nomMun, 'provincia_id' => $provincia->id]);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ Base de datos actualizada con el registro oficial de municipios.");
    }
}
