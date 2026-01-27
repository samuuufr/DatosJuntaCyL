<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Servicio para consumir la API del Movimiento Natural de la Población de Castilla y León
 *
 * Documentación: docs/Manual+de+consultas_MovimientoNaturalPoblacion,0.pdf
 */
class MnpApiService
{
    /**
     * URI base de la API MNP
     */
    private const BASE_URI = 'http://www.jcyl.es/sie/sas/broker';

    /**
     * Parámetros fijos de la API
     */
    private const BASE_PARAMS = [
        '_PROGRAM' => 'sashelp.webeis.oprpt.scl',
        '_SERVICE' => 'saswebl',
        'CLASS' => 'mddbpgm.jcyl.custom_webeis2.class',
        'METABASE' => 'RPOSWEB',
        'ST' => '1',
        'FS' => 'SUM',
        'SPDSHT' => 'X',
        'MDDB' => 'MNP.M_MNP',
        'A' => 'VALOR_VARIABLE',
    ];

    /**
     * Códigos de familia de variables (tipos de datos)
     */
    public const FAMILIA_NACIMIENTOS = 10;
    public const FAMILIA_NACIMIENTOS_SEXO = 12;
    public const FAMILIA_NACIMIENTOS_MULTIPLICIDAD = 14;
    public const FAMILIA_NACIMIENTOS_EDAD_MADRE = 16;
    public const FAMILIA_NACIMIENTOS_NUM_HIJOS = 18;
    public const FAMILIA_MATRIMONIOS = 20;
    public const FAMILIA_MATRIMONIOS_TIPO_CELEBRACION = 21;
    public const FAMILIA_MATRIMONIOS_ESTADO_CIVIL_VARON = 23;
    public const FAMILIA_MATRIMONIOS_ESTADO_CIVIL_MUJER = 24;
    public const FAMILIA_MATRIMONIOS_EDAD_VARON = 27;
    public const FAMILIA_MATRIMONIOS_EDAD_MUJER = 28;
    public const FAMILIA_MATRIMONIOS_MISMO_SEXO = 29;
    public const FAMILIA_DEFUNCIONES = 30;
    public const FAMILIA_DEFUNCIONES_SEXO = 32;
    public const FAMILIA_DEFUNCIONES_ESTADO_CIVIL = 34;
    public const FAMILIA_DEFUNCIONES_EDAD = 36;

    /**
     * Códigos INE de provincias de Castilla y León
     */
    public const PROVINCIAS = [
        '05' => 'Ávila',
        '09' => 'Burgos',
        '24' => 'León',
        '34' => 'Palencia',
        '37' => 'Salamanca',
        '40' => 'Segovia',
        '42' => 'Soria',
        '47' => 'Valladolid',
        '49' => 'Zamora',
    ];

    /**
     * Construir URL completa para consulta a la API
     *
     * @param array $params Parámetros adicionales
     * @return string URL completa
     */
    public function buildUrl(array $params): string
    {
        $query = http_build_query(self::BASE_PARAMS, '', '&', PHP_QUERY_RFC3986);

        // Añadir D para DESC_FAMILIA_VARIABLES y DESC_VARIABLE
        $query .= '&D=DESC_FAMILIA_VARIABLES&D=DESC_VARIABLE';

        // Añadir parámetros SL (filtros) y otros
        foreach ($params as $key => $value) {
            if (\is_array($value)) {
                foreach ($value as $v) {
                    $query .= '&' . $key . '=' . urlencode($v);
                }
            } else {
                $query .= '&' . $key . '=' . urlencode($value);
            }
        }

        return self::BASE_URI . '?' . $query;
    }

    /**
     * Obtener nacimientos por año y provincia
     *
     * @param int|array $anos Año o array de años
     * @param string|null $codigoProvincia Código INE de provincia (opcional)
     * @param string|null $codigoMunicipio Código INE de municipio (opcional)
     * @return array Datos en formato array
     */
    public function getNacimientos($anos, ?string $codigoProvincia = null, ?string $codigoMunicipio = null): array
    {
        return $this->getDatos(self::FAMILIA_NACIMIENTOS, $anos, $codigoProvincia, $codigoMunicipio);
    }

    /**
     * Obtener defunciones por año y provincia
     *
     * @param int|array $anos Año o array de años
     * @param string|null $codigoProvincia Código INE de provincia (opcional)
     * @param string|null $codigoMunicipio Código INE de municipio (opcional)
     * @return array Datos en formato array
     */
    public function getDefunciones($anos, ?string $codigoProvincia = null, ?string $codigoMunicipio = null): array
    {
        return $this->getDatos(self::FAMILIA_DEFUNCIONES, $anos, $codigoProvincia, $codigoMunicipio);
    }

    /**
     * Obtener matrimonios por año y provincia
     *
     * @param int|array $anos Año o array de años
     * @param string|null $codigoProvincia Código INE de provincia (opcional)
     * @param string|null $codigoMunicipio Código INE de municipio (opcional)
     * @return array Datos en formato array
     */
    public function getMatrimonios($anos, ?string $codigoProvincia = null, ?string $codigoMunicipio = null): array
    {
        return $this->getDatos(self::FAMILIA_MATRIMONIOS, $anos, $codigoProvincia, $codigoMunicipio);
    }

    /**
     * Obtener datos genéricos de la API
     *
     * @param int $familiaVariable Código de familia de variables
     * @param int|array $anos Año o array de años
     * @param string|null $codigoProvincia Código INE de provincia (opcional)
     * @param string|null $codigoMunicipio Código INE de municipio (opcional)
     * @return array Datos parseados
     */
    public function getDatos(
        int $familiaVariable,
        $anos,
        ?string $codigoProvincia = null,
        ?string $codigoMunicipio = null
    ): array {
        $params = [];

        // Familia de variables (tipo de dato)
        $params['SL'][] = "COD_FAMILIA_VARIABLES:{$familiaVariable}";

        // Años
        if (is_array($anos)) {
            foreach ($anos as $ano) {
                $params['SL'][] = "ANNO:{$ano}";
            }
        } else {
            $params['SL'][] = "ANNO:{$anos}";
        }

        // Provincia (opcional)
        if ($codigoProvincia) {
            $params['SL'][] = "COD_PROVINCIA:{$codigoProvincia}";
        }

        // Municipio (opcional)
        if ($codigoMunicipio) {
            $params['SL'][] = "COD_MUNICIPIO:{$codigoMunicipio}";
        }

        // Variables en filas: municipios
        $params['D'] = 'NOM_MUNICIPIO';

        // Años en columnas
        $params['AC'] = 'ANNO';

        $url = $this->buildUrl($params);

        Log::info('API MNP Request', ['url' => $url]);

        try {
            $response = Http::timeout(30)->get($url);

            if ($response->successful()) {
                return $this->parseCsvResponse($response->body());
            }

            Log::error('API MNP Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error('API MNP Exception', [
                'message' => $e->getMessage(),
                'url' => $url,
            ]);

            return [];
        }
    }

    /**
     * Obtener datos de todos los municipios de una provincia para un rango de años
     *
     * @param string $codigoProvincia Código INE de provincia
     * @param int $anoInicio Año inicial
     * @param int $anoFin Año final
     * @return array Datos agrupados por tipo de evento
     */
    public function getDatosProvincia(string $codigoProvincia, int $anoInicio, int $anoFin): array
    {
        $anos = range($anoInicio, $anoFin);

        return [
            'nacimientos' => $this->getNacimientos($anos, $codigoProvincia),
            'defunciones' => $this->getDefunciones($anos, $codigoProvincia),
            'matrimonios' => $this->getMatrimonios($anos, $codigoProvincia),
        ];
    }

    /**
     * Obtener datos de todas las provincias para un rango de años
     *
     * @param int $anoInicio Año inicial
     * @param int $anoFin Año final
     * @return array Datos agrupados por provincia
     */
    public function getDatosCastillaLeon(int $anoInicio, int $anoFin): array
    {
        $anos = range($anoInicio, $anoFin);
        $datos = [];

        foreach (self::PROVINCIAS as $codigo => $nombre) {
            $datos[$codigo] = [
                'nombre' => $nombre,
                'nacimientos' => $this->getNacimientos($anos, $codigo),
                'defunciones' => $this->getDefunciones($anos, $codigo),
                'matrimonios' => $this->getMatrimonios($anos, $codigo),
            ];
        }

        return $datos;
    }

    /**
     * Parsear respuesta CSV de la API
     *
     * @param string $csvContent Contenido CSV
     * @return array Datos parseados
     */
    private function parseCsvResponse(string $csvContent): array
    {
        // Convertir de ISO-8859-1 a UTF-8
        $csvContent = mb_convert_encoding($csvContent, 'UTF-8', 'ISO-8859-1');

        $lines = explode("\n", trim($csvContent));
        $data = [];

        if (\count($lines) < 5) {
            return [];
        }

        // Buscar la línea de encabezados (la que NO empieza con comillas y contiene "Familia")
        $headerIndex = -1;
        for ($i = 0; $i < \count($lines); $i++) {
            $trimmed = trim($lines[$i]);
            if (!empty($trimmed) && $trimmed[0] !== '"' &&
                strpos($trimmed, 'Familia') !== false &&
                strpos($trimmed, 'Variable') !== false) {
                $headerIndex = $i;
                break;
            }
        }

        if ($headerIndex === -1) {
            return [];
        }

        // Parsear encabezados
        $headers = str_getcsv($lines[$headerIndex], ',', '"', '');
        $headers = array_map('trim', $headers);
        $headers = array_filter($headers);
        $headers = array_values($headers); // Reindexar

        // Valores para reutilizar cuando hay columnas vacías
        $lastFamilia = '';
        $lastVariable = '';

        // Procesar datos
        for ($i = $headerIndex + 1; $i < \count($lines); $i++) {
            $line = trim($lines[$i]);

            if (empty($line)) {
                continue;
            }

            $row = str_getcsv($line, ',', '"', '');
            $row = array_map('trim', $row);

            // Debe tener al menos municipio y valor
            if (\count($row) < 3) {
                continue;
            }

            // Extraer valores
            $familia = !empty($row[0]) ? $row[0] : $lastFamilia;
            $variable = !empty($row[1]) ? $row[1] : $lastVariable;
            $municipio = $row[2] ?? '';
            $valor = $row[3] ?? '';

            // Actualizar últimos valores
            if (!empty($row[0])) {
                $lastFamilia = $row[0];
            }
            if (!empty($row[1])) {
                $lastVariable = $row[1];
            }

            // Solo agregar si tiene municipio y valor
            if (!empty($municipio) && $valor !== '') {
                $item = [
                    'Familia' => $familia,
                    'Variable' => $variable,
                    'Municipio' => $municipio,
                    'Valor' => is_numeric($valor) ? (int) $valor : $valor,
                ];

                $data[] = $item;
            }
        }

        return $data;
    }

    /**
     * Obtener nombre de familia de variables
     *
     * @param int $codigo Código de familia
     * @return string Nombre legible
     */
    public static function getNombreFamilia(int $codigo): string
    {
        $familias = [
            self::FAMILIA_NACIMIENTOS => 'Nacimientos',
            self::FAMILIA_NACIMIENTOS_SEXO => 'Nacimientos por sexo',
            self::FAMILIA_NACIMIENTOS_MULTIPLICIDAD => 'Nacimientos según multiplicidad',
            self::FAMILIA_NACIMIENTOS_EDAD_MADRE => 'Nacimientos según edad de la madre',
            self::FAMILIA_NACIMIENTOS_NUM_HIJOS => 'Nacimientos por número de hijos',
            self::FAMILIA_MATRIMONIOS => 'Matrimonios de distinto sexo',
            self::FAMILIA_MATRIMONIOS_TIPO_CELEBRACION => 'Matrimonios según tipo de celebración',
            self::FAMILIA_MATRIMONIOS_ESTADO_CIVIL_VARON => 'Matrimonios según estado civil del varón',
            self::FAMILIA_MATRIMONIOS_ESTADO_CIVIL_MUJER => 'Matrimonios según estado civil de la mujer',
            self::FAMILIA_MATRIMONIOS_EDAD_VARON => 'Matrimonios por edad del varón',
            self::FAMILIA_MATRIMONIOS_EDAD_MUJER => 'Matrimonios por edad de la mujer',
            self::FAMILIA_MATRIMONIOS_MISMO_SEXO => 'Matrimonios del mismo sexo',
            self::FAMILIA_DEFUNCIONES => 'Defunciones',
            self::FAMILIA_DEFUNCIONES_SEXO => 'Defunciones por sexo',
            self::FAMILIA_DEFUNCIONES_ESTADO_CIVIL => 'Defunciones según estado civil',
            self::FAMILIA_DEFUNCIONES_EDAD => 'Defunciones por edad',
        ];

        return $familias[$codigo] ?? "Familia {$codigo}";
    }
}
