<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MnpApiService;
use App\Models\Municipio;
use App\Models\DatoMnp;

class ImportMnpData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mnp:import
                            {--provincia= : Código INE de provincia (opcional)}
                            {--municipio= : Código INE de municipio (opcional)}
                            {--ano-inicio=2020 : Año de inicio}
                            {--ano-fin=2023 : Año de fin}
                            {--tipo=all : Tipo de datos (nacimientos, defunciones, matrimonios, all)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importar datos del Movimiento Natural de la Población desde la API de JCyL';

    private MnpApiService $mnpService;

    /**
     * Execute the console command.
     */
    public function handle(MnpApiService $mnpService)
    {
        $this->mnpService = $mnpService;

        $provincia = $this->option('provincia');
        $municipio = $this->option('municipio');
        $anoInicio = (int) $this->option('ano-inicio');
        $anoFin = (int) $this->option('ano-fin');
        $tipo = $this->option('tipo');

        $this->info("🚀 Importando datos MNP desde {$anoInicio} hasta {$anoFin}");

        if ($municipio) {
            $this->importarMunicipio($municipio, $anoInicio, $anoFin, $tipo);
        } elseif ($provincia) {
            $this->importarProvincia($provincia, $anoInicio, $anoFin, $tipo);
        } else {
            $this->importarTodasProvincias($anoInicio, $anoFin, $tipo);
        }

        $this->info('✅ Importación completada!');
    }

    /**
     * Importar datos de un municipio específico
     */
    private function importarMunicipio(string $codigoMunicipio, int $anoInicio, int $anoFin, string $tipo)
    {
        $municipio = Municipio::where('codigo_ine', $codigoMunicipio)->first();

        if (!$municipio) {
            $this->error("❌ Municipio con código {$codigoMunicipio} no encontrado en la BD");
            return;
        }

        $this->info("📍 Importando datos de: {$municipio->nombre}");

        $anos = range($anoInicio, $anoFin);

        if ($tipo === 'all' || $tipo === 'nacimientos') {
            $this->importarDatos($municipio, 'nacimiento', $anos, null, $codigoMunicipio);
        }

        if ($tipo === 'all' || $tipo === 'defunciones') {
            $this->importarDatos($municipio, 'defuncion', $anos, null, $codigoMunicipio);
        }

        if ($tipo === 'all' || $tipo === 'matrimonios') {
            $this->importarDatos($municipio, 'matrimonio', $anos, null, $codigoMunicipio);
        }
    }

    /**
     * Importar datos de una provincia
     */
    private function importarProvincia(string $codigoProvincia, int $anoInicio, int $anoFin, string $tipo)
    {
        $this->info("📍 Importando datos de provincia: " . MnpApiService::PROVINCIAS[$codigoProvincia] ?? $codigoProvincia);

        $municipios = Municipio::whereHas('provincia', function ($query) use ($codigoProvincia) {
            $query->where('codigo_ine', $codigoProvincia);
        })->get();

        if ($municipios->isEmpty()) {
            $this->warn("⚠️  No hay municipios en la BD para la provincia {$codigoProvincia}");
            return;
        }

        $anos = range($anoInicio, $anoFin);
        $bar = $this->output->createProgressBar($municipios->count());

        foreach ($municipios as $municipio) {
            if ($tipo === 'all' || $tipo === 'nacimientos') {
                $this->importarDatos($municipio, 'nacimiento', $anos, $codigoProvincia);
            }

            if ($tipo === 'all' || $tipo === 'defunciones') {
                $this->importarDatos($municipio, 'defuncion', $anos, $codigoProvincia);
            }

            if ($tipo === 'all' || $tipo === 'matrimonios') {
                $this->importarDatos($municipio, 'matrimonio', $anos, $codigoProvincia);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }

    /**
     * Importar datos de todas las provincias
     */
    private function importarTodasProvincias(int $anoInicio, int $anoFin, string $tipo)
    {
        foreach (MnpApiService::PROVINCIAS as $codigo => $nombre) {
            $this->info("🏛️  Provincia: {$nombre}");
            $this->importarProvincia($codigo, $anoInicio, $anoFin, $tipo);
        }
    }

    /**
     * Importar datos específicos
     */
    private function importarDatos(
        Municipio $municipio,
        string $tipoEvento,
        array $anos,
        ?string $codigoProvincia = null,
        ?string $codigoMunicipio = null
    ) {
        $familiaVariable = match ($tipoEvento) {
            'nacimiento' => MnpApiService::FAMILIA_NACIMIENTOS,
            'defuncion' => MnpApiService::FAMILIA_DEFUNCIONES,
            'matrimonio' => MnpApiService::FAMILIA_MATRIMONIOS,
            default => null,
        };

        if (!$familiaVariable) {
            return;
        }

        $datos = $this->mnpService->getDatos(
            $familiaVariable,
            $anos,
            $codigoProvincia,
            $codigoMunicipio ?? $municipio->codigo_ine
        );

        foreach ($datos as $fila) {
            // La API devuelve múltiples columnas por año
            // Necesitamos iterar sobre las columnas que contienen años
            foreach ($fila as $key => $value) {
                // Si la clave es un año (formato numérico de 4 dígitos)
                if (preg_match('/^\d{4}$/', $key)) {
                    $ano = (int) $key;
                    $valor = is_numeric($value) ? (int) $value : 0;

                    if (in_array($ano, $anos)) {
                        DatoMnp::updateOrCreate(
                            [
                                'municipio_id' => $municipio->id,
                                'anno' => $ano,
                                'tipo_evento' => $tipoEvento,
                            ],
                            [
                                'valor' => $valor,
                                'ultima_actualizacion' => now(),
                            ]
                        );
                    }
                }
            }
        }
    }
}

