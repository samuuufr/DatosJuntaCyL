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

        // Variables en filas: municipios, código y AÑO.
        // Esto devuelve un formato "largo" (una fila por municipio/año), que es más robusto.
        $params['D'] = ['NOM_MUNICIPIO', 'COD_MUNICIPIO', 'ANNO'];

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
        // Eliminar BOM (Byte Order Mark) si existe, para evitar problemas con el primer encabezado
        $csvContent = preg_replace('/^\xEF\xBB\xBF/', '', $csvContent);

        // Convertir de ISO-8859-1 a UTF-8
        $csvContent = mb_convert_encoding($csvContent, 'UTF-8', 'ISO-8859-1');

        $lines = explode("\n", trim($csvContent));
        $data = [];

        if (\count($lines) < 5) {
            return [];
        }

        // Buscar la línea de encabezados de forma más robusta
        $headerIndex = -1;
        // Buscamos solo en las primeras 20 líneas para no perder tiempo
        for ($i = 0; $i < min(20, \count($lines)); $i++) {
            $trimmed = trim($lines[$i]);
            // Buscamos palabras clave sin importar mayúsculas/minúsculas ni comillas iniciales
            if (stripos($trimmed, 'Municipio') !== false && (stripos($trimmed, 'Familia') !== false || stripos($trimmed, 'Variable') !== false)) {
                $headerIndex = $i;
                break;
            }
        }

        if ($headerIndex === -1) {
            return [];
        }

        // Parsear encabezados
        // Detectar delimitador contando ocurrencias en la línea de cabecera
        $lineaCabecera = $lines[$headerIndex];
        $delimiter = (substr_count($lineaCabecera, ';') > substr_count($lineaCabecera, ',')) ? ';' : ',';

        $headers = str_getcsv($lineaCabecera, $delimiter, '"', '');

        $headers = array_map('trim', $headers);

        // Mapear índices de columnas dinámicamente
        $indices = ['familia' => -1, 'variable' => -1, 'municipio' => -1, 'codigo' => -1, 'anno' => -1, 'valor' => -1];

        foreach ($headers as $idx => $header) {
            $h = mb_strtolower($header, 'UTF-8');
            if (str_contains($h, 'familia')) $indices['familia'] = $idx;
            elseif (str_contains($h, 'variable')) $indices['variable'] = $idx;
            elseif (str_contains($h, 'municipio') && !str_contains($h, 'cod') && !str_contains($h, 'código')) $indices['municipio'] = $idx;
            elseif ((str_contains($h, 'código') || str_contains($h, 'cod')) && str_contains($h, 'municipio')) $indices['codigo'] = $idx;
            elseif ($h === 'año' || $h === 'anno') $indices['anno'] = $idx;
            elseif ($h === 'valor_variable' || str_contains($h, 'valor')) $indices['valor'] = $idx;
        }

        // Valores para reutilizar cuando hay columnas vacías
        $lastFamilia = '';
        $lastVariable = '';

        // Procesar datos
        for ($i = $headerIndex + 1; $i < \count($lines); $i++) {
            $line = trim($lines[$i]);

            if (empty($line)) {
                continue;
            }

            $row = str_getcsv($line, $delimiter, '"', '');
            $row = array_map('trim', $row);

            // Extraer valores
            if ($indices['familia'] >= 0 && !empty($row[$indices['familia']])) $lastFamilia = $row[$indices['familia']];
            if ($indices['variable'] >= 0 && !empty($row[$indices['variable']])) $lastVariable = $row[$indices['variable']];

            $item = [];
            if ($indices['familia'] >= 0) $item['Familia'] = $row[$indices['familia']] ?: $lastFamilia;
            if ($indices['variable'] >= 0) $item['Variable'] = $row[$indices['variable']] ?: $lastVariable;
            if ($indices['municipio'] >= 0) $item['Municipio'] = $row[$indices['municipio']] ?? '';
            if ($indices['codigo'] >= 0) $item['Codigo'] = $row[$indices['codigo']] ?? '';
            if ($indices['anno'] >= 0) $item['Anno'] = $row[$indices['anno']] ?? '';
            if ($indices['valor'] >= 0 && isset($row[$indices['valor']])) {
                $val = $row[$indices['valor']];
                // Convertir a entero, tratando vacíos o puntos como 0
                $item['Valor'] = (is_numeric($val)) ? (int)$val : 0;
            }

            // Solo agregar si tiene datos de municipio
            if ((!empty($item['Municipio']) || !empty($item['Codigo'])) && isset($item['Anno'])) {
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
