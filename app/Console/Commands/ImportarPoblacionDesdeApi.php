<?php

namespace App\Console\Commands;

use App\Models\Municipio;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImportarPoblacionDesdeApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'poblacion:importar-api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa la población de todos los municipios desde la API de datos abiertos de la Junta de Castilla y León';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🌐 Conectando con la API de datos abiertos de la Junta de Castilla y León...');

        $baseUrl = 'https://analisis.datosabiertos.jcyl.es/api/explore/v2.1/catalog/datasets/registro-de-municipios-de-castilla-y-leon/records';
        $limit = 100; // Máximo por petición según docs de OpenDataSoft
        $offset = 0;
        $totalActualizados = 0;
        $totalNoEncontrados = 0;
        $municipiosNoEncontrados = [];

        try {
            // Primera petición para obtener el total
            $response = Http::timeout(30)->get($baseUrl, [
                'limit' => 1,
                'offset' => 0
            ]);

            if (!$response->successful()) {
                $this->error('❌ Error al conectar con la API: ' . $response->status());
                return 1;
            }

            $data = $response->json();
            $totalMunicipios = $data['total_count'] ?? 0;

            $this->info("📊 Total de municipios en la API: {$totalMunicipios}");
            $this->newLine();

            $progressBar = $this->output->createProgressBar($totalMunicipios);
            $progressBar->start();

            // Procesar en lotes
            while ($offset < $totalMunicipios) {
                $response = Http::timeout(30)->get($baseUrl, [
                    'limit' => $limit,
                    'offset' => $offset
                ]);

                if (!$response->successful()) {
                    $this->error("\n❌ Error en la petición (offset: {$offset}): " . $response->status());
                    break;
                }

                $data = $response->json();
                $municipiosApi = $data['results'] ?? [];

                foreach ($municipiosApi as $municipioData) {
                    $codigoIne = (string) $municipioData['cod_ine'];
                    $poblacion = $municipioData['poblacion'] ?? null;

                    if ($poblacion !== null) {
                        $municipio = Municipio::where('codigo_ine', $codigoIne)->first();

                        if ($municipio) {
                            $municipio->poblacion = $poblacion;
                            $municipio->save();
                            $totalActualizados++;
                        } else {
                            $totalNoEncontrados++;
                            $municipiosNoEncontrados[] = [
                                'nombre' => $municipioData['municipio'] ?? 'Desconocido',
                                'codigo_ine' => $codigoIne
                            ];
                        }
                    }

                    $progressBar->advance();
                }

                $offset += $limit;

                // Pequeña pausa para no saturar la API
                usleep(100000); // 0.1 segundos
            }

            $progressBar->finish();
            $this->newLine(2);

            // Resumen
            $this->info("✅ Importación completada:");
            $this->table(
                ['Métrica', 'Cantidad'],
                [
                    ['Municipios actualizados', number_format($totalActualizados, 0, ',', '.')],
                    ['No encontrados en BD', number_format($totalNoEncontrados, 0, ',', '.')],
                ]
            );

            if ($totalNoEncontrados > 0 && count($municipiosNoEncontrados) <= 10) {
                $this->newLine();
                $this->warn('⚠ Municipios no encontrados en la base de datos:');
                foreach ($municipiosNoEncontrados as $mun) {
                    $this->line("   - {$mun['nombre']} (INE: {$mun['codigo_ine']})");
                }
            }

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Error durante la importación: ' . $e->getMessage());
            return 1;
        }
    }
}
