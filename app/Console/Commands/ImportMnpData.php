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
        $codigoProvincia = str_pad($codigoProvincia, 2, '0', STR_PAD_LEFT);

        $this->info("📍 Importando datos de provincia: " . MnpApiService::PROVINCIAS[$codigoProvincia] ?? $codigoProvincia);

        // Obtener todos los municipios de la provincia
        // FIX: Buscar por código con y sin ceros (ej: '05' y '5') para asegurar compatibilidad
        $municipios = Municipio::whereHas('provincia', function ($query) use ($codigoProvincia) {
            $query->where('codigo_ine', $codigoProvincia)
                  ->orWhere('codigo_ine', (string)(int)$codigoProvincia);
        })->get();

        if ($municipios->isEmpty()) {
            $this->warn("⚠️  No hay municipios en la BD para la provincia {$codigoProvincia}");
            return;
        }

        // DEBUG: Mostrar información de lo que se ha cargado de la BD
        $this->info("   📊 Municipios cargados de BD: " . $municipios->count());
        $this->info("   🔍 Ejemplo (primeros 3): " .
            $municipios->take(3)->map(fn($m) => "[{$m->codigo_ine}] {$m->nombre}")->implode(', ')
        );

        // Construir mapas de búsqueda robustos
        $municipiosMap = [];
        $municipiosNameMapNormalized = [];

        foreach ($municipios as $mun) {
            // 1. Mapa por Código INE tal cual está en BD
            $municipiosMap[trim($mun->codigo_ine)] = $mun->id;

            // 2. Mapa por Código INE normalizado a 5 dígitos (05001)
            $cleanCode = (string)$mun->codigo_ine;
            $fullCode = $cleanCode;

            if (strlen($cleanCode) <= 3) {
                // Si es código corto (ej: 1), añadir provincia (ej: 05001)
                $fullCode = $codigoProvincia . str_pad($cleanCode, 3, '0', STR_PAD_LEFT);
            } else {
                // Si es largo (ej: 5001), asegurar padding (ej: 05001)
                $fullCode = str_pad($cleanCode, 5, '0', STR_PAD_LEFT);
            }
            $municipiosMap[$fullCode] = $mun->id;

            // 3. Mapa por entero (5001)
            $municipiosMap[(int)$fullCode] = $mun->id;

            // 4. Mapa por nombre normalizado
            $municipiosNameMapNormalized[$this->normalizeString($mun->nombre)] = $mun->id;
        }

        $anos = range($anoInicio, $anoFin);

        // Definir tipos a importar
        $tipos = [];
        if ($tipo === 'all' || $tipo === 'nacimientos') $tipos['nacimiento'] = MnpApiService::FAMILIA_NACIMIENTOS;
        if ($tipo === 'all' || $tipo === 'defunciones') $tipos['defuncion'] = MnpApiService::FAMILIA_DEFUNCIONES;
        if ($tipo === 'all' || $tipo === 'matrimonios') $tipos['matrimonio'] = MnpApiService::FAMILIA_MATRIMONIOS;

        foreach ($tipos as $tipoEvento => $familiaId) {
            $this->info("  📥 Descargando {$tipoEvento}s (Carga masiva)...");

            // Descargar datos de TODA la provincia de una vez
            $datos = $this->mnpService->getDatos(
                $familiaId,
                $anos,
                $codigoProvincia,
                null // Sin filtro de municipio = todos
            );

            if (empty($datos)) {
                $this->warn("  ⚠️ No se obtuvieron datos para {$tipoEvento}");
                continue;
            }

            $bar = $this->output->createProgressBar(count($datos));
$procesados = 0;
            $firstFailure = null;

            foreach ($datos as $fila) {
                // Usar el código devuelto por la API para encontrar el municipio
                $codigoIneApi = $fila['Codigo'] ?? null;
                $municipioId = null;
                $apiCodeNormalized = 'N/A';

                // 1. Intentar buscar por CÓDIGO
                if ($codigoIneApi && is_numeric($codigoIneApi)) {
                    // Intentar match directo
                    if (isset($municipiosMap[(string)$codigoIneApi])) {
                        $municipioId = $municipiosMap[$codigoIneApi];
                    }
                    // Intentar match con padding 5 dígitos
                    else {
                        $apiCodeNormalized = str_pad($codigoIneApi, 5, '0', STR_PAD_LEFT);
                        if (isset($municipiosMap[$apiCodeNormalized])) {
                            $municipioId = $municipiosMap[$apiCodeNormalized];
                        }
                        // Intentar match como entero
                        elseif (isset($municipiosMap[(int)$codigoIneApi])) {
                            $municipioId = $municipiosMap[(int)$codigoIneApi];
                        }
                    }
                }

                // 2. Fallback: Intentar buscar por NOMBRE si falla el código
                if (!$municipioId && !empty($fila['Municipio'])) {
                    // Limpiar nombre: quitar código inicial y separadores (ej: "05001 ADANERO" -> "ADANERO")
                    $nombreLimpio = preg_replace('/^[\d\s\.\-]+/', '', $fila['Municipio']);
                    $nombreApi = $this->normalizeString($nombreLimpio);
                    if (isset($municipiosNameMapNormalized[$nombreApi])) {
                        $municipioId = $municipiosNameMapNormalized[$nombreApi];
                    }
                }

                if ($municipioId) {
                    $procesados++;
                    foreach ($fila as $key => $value) {
                        if (preg_match('/^\d{4}$/', $key)) {
                            $ano = (int) $key;
                            if (in_array($ano, $anos)) {
                                DatoMnp::updateOrCreate(
                                    [
                                        'municipio_id' => $municipioId,
                                        'anno' => $ano,
                                        'tipo_evento' => $tipoEvento,
                                    ],
                                    [
                                        'valor' => (int) $value,
                                        'ultima_actualizacion' => now(),
                                    ]
                                );
                            }
                        }
                    }
                } else {
                    // Guardar primer fallo para depuración
                    if (!$firstFailure) {
                        $firstFailure = [
                            'code' => $fila['Codigo'] ?? 'N/A',
                            'name' => $fila['Municipio'] ?? 'N/A',
                            'normalized' => $apiCodeNormalized ?? 'N/A'
                        ];
                    }
                }
                $bar->advance();
            }
            $bar->finish();

            $this->info("    ✅ Procesados: {$procesados} / " . count($datos));

            if ($procesados < count($datos) && $firstFailure) {
                $this->warn("    ⚠️  Ejemplo de fallo: API Code '{$firstFailure['code']}' -> '{$firstFailure['normalized']}', Name '{$firstFailure['name']}'");
            }

            if ($procesados === 0) {
                $this->warn("\n  ⚠️  No se procesó ningún registro. Verifica los códigos INE.");
            }

            $this->newLine();
        }
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

    /**
     * Normalizar cadena para comparación (minúsculas, sin tildes)
     */
    private function normalizeString(string $str): string
    {
        $str = mb_strtolower(trim($str));
        return str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ü', 'ñ', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ü', 'Ñ'],
            ['a', 'e', 'i', 'o', 'u', 'u', 'n', 'a', 'e', 'i', 'o', 'u', 'u', 'n'],
            $str
        );
    }
}
