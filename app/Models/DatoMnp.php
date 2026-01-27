<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DatoMnp extends Model
{
    protected $table = 'datos_mnp';

    protected $fillable = [
        'municipio_id',
        'anno',
        'tipo_evento',
        'valor',
        'ultima_actualizacion',
    ];

    protected $casts = [
        'anno' => 'integer',
        'valor' => 'integer',
        'ultima_actualizacion' => 'datetime',
    ];

    /**
     * Un dato MNP pertenece a un municipio
     */
    public function municipio(): BelongsTo
    {
        return $this->belongsTo(Municipio::class);
    }

    /**
     * Scope para filtrar por tipo de evento
     */
    public function scopeTipoEvento($query, string $tipo)
    {
        return $query->where('tipo_evento', $tipo);
    }

    /**
     * Scope para filtrar por año
     */
    public function scopeAnno($query, int $anno)
    {
        return $query->where('anno', $anno);
    }

    /**
     * Scope para nacimientos
     */
    public function scopeNacimientos($query)
    {
        return $query->where('tipo_evento', 'nacimiento');
    }

    /**
     * Scope para defunciones
     */
    public function scopeDefunciones($query)
    {
        return $query->where('tipo_evento', 'defuncion');
    }

    /**
     * Scope para matrimonios
     */
    public function scopeMatrimonios($query)
    {
        return $query->where('tipo_evento', 'matrimonio');
    }

    /**
     * Obtener estadísticas agregadas por provincia para un año
     * Usa Query Builder sobre PDO
     *
     * @param int $ano Año a consultar
     * @return \Illuminate\Support\Collection
     */
    public static function getEstadisticasPorProvincia(int $ano)
    {
        return \DB::table('datos_mnp as d')
            ->join('municipios as m', 'd.municipio_id', '=', 'm.id')
            ->join('provincias as p', 'm.provincia_id', '=', 'p.id')
            ->where('d.anno', $ano)
            ->select([
                'p.codigo_ine',
                'p.nombre as provincia',
                'd.tipo_evento',
                \DB::raw('SUM(d.valor) as total'),
                \DB::raw('COUNT(DISTINCT m.id) as num_municipios'),
                \DB::raw('AVG(d.valor) as promedio'),
            ])
            ->groupBy('p.id', 'p.codigo_ine', 'p.nombre', 'd.tipo_evento')
            ->orderBy('p.nombre')
            ->orderBy('d.tipo_evento')
            ->get();
    }

    /**
     * Obtener evolución temporal de un municipio
     * Usa Query Builder sobre PDO
     *
     * @param int $municipioId ID del municipio
     * @param int $anoInicio Año inicial
     * @param int $anoFin Año final
     * @return \Illuminate\Support\Collection
     */
    public static function getEvolucionMunicipio(int $municipioId, int $anoInicio, int $anoFin)
    {
        return \DB::table('datos_mnp')
            ->where('municipio_id', $municipioId)
            ->whereBetween('anno', [$anoInicio, $anoFin])
            ->select([
                'anno',
                'tipo_evento',
                'valor',
                'ultima_actualizacion',
            ])
            ->orderBy('anno')
            ->orderBy('tipo_evento')
            ->get();
    }

    /**
     * Calcular crecimiento vegetativo por provincia y año
     * Usa Query Builder con subconsultas
     *
     * @param string $codigoProvincia Código INE de provincia
     * @param int $ano Año a consultar
     * @return array|null
     */
    public static function getCrecimientoVegetativo(string $codigoProvincia, int $ano): ?array
    {
        $result = \DB::table('datos_mnp as d')
            ->join('municipios as m', 'd.municipio_id', '=', 'm.id')
            ->join('provincias as p', 'm.provincia_id', '=', 'p.id')
            ->where('p.codigo_ine', $codigoProvincia)
            ->where('d.anno', $ano)
            ->select([
                'p.nombre as provincia',
                \DB::raw("SUM(CASE WHEN d.tipo_evento = 'nacimiento' THEN d.valor ELSE 0 END) as nacimientos"),
                \DB::raw("SUM(CASE WHEN d.tipo_evento = 'defuncion' THEN d.valor ELSE 0 END) as defunciones"),
                \DB::raw("SUM(CASE WHEN d.tipo_evento = 'matrimonio' THEN d.valor ELSE 0 END) as matrimonios"),
            ])
            ->groupBy('p.id', 'p.nombre')
            ->first();

        if (!$result) {
            return null;
        }

        // Calcular crecimiento vegetativo
        $crecimiento = $result->nacimientos - $result->defunciones;

        return [
            'provincia' => $result->provincia,
            'ano' => $ano,
            'nacimientos' => $result->nacimientos,
            'defunciones' => $result->defunciones,
            'matrimonios' => $result->matrimonios,
            'crecimiento_vegetativo' => $crecimiento,
        ];
    }

    /**
     * Obtener ranking de municipios por tipo de evento
     * Usa Query Builder sobre PDO
     *
     * @param string $tipoEvento nacimiento, defuncion, matrimonio
     * @param int $ano Año a consultar
     * @param int $limite Número de resultados
     * @return \Illuminate\Support\Collection
     */
    public static function getRankingMunicipios(string $tipoEvento, int $ano, int $limite = 10)
    {
        return \DB::table('datos_mnp as d')
            ->join('municipios as m', 'd.municipio_id', '=', 'm.id')
            ->join('provincias as p', 'm.provincia_id', '=', 'p.id')
            ->where('d.tipo_evento', $tipoEvento)
            ->where('d.anno', $ano)
            ->select([
                'm.nombre as municipio',
                'p.nombre as provincia',
                'd.valor',
            ])
            ->orderBy('d.valor', 'desc')
            ->limit($limite)
            ->get();
    }

    // ========================================================================
    // MÉTODOS CON PDO PURO (Acceso directo a PDO)
    // ========================================================================

    /**
     * Obtener estadísticas por provincia usando PDO PURO
     *
     * @param int $ano Año a consultar
     * @return array
     */
    public static function getEstadisticasPorProvinciaPDO(int $ano): array
    {
        $pdo = \DB::connection()->getPdo();

        $sql = "
            SELECT
                p.codigo_ine,
                p.nombre as provincia,
                d.tipo_evento,
                SUM(d.valor) as total,
                COUNT(DISTINCT m.id) as num_municipios,
                AVG(d.valor) as promedio
            FROM datos_mnp d
            INNER JOIN municipios m ON d.municipio_id = m.id
            INNER JOIN provincias p ON m.provincia_id = p.id
            WHERE d.anno = :ano
            GROUP BY p.id, p.codigo_ine, p.nombre, d.tipo_evento
            ORDER BY p.nombre, d.tipo_evento
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':ano', $ano, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Obtener evolución temporal usando PDO PURO
     *
     * @param int $municipioId ID del municipio
     * @param int $anoInicio Año inicial
     * @param int $anoFin Año final
     * @return array
     */
    public static function getEvolucionMunicipioPDO(int $municipioId, int $anoInicio, int $anoFin): array
    {
        $pdo = \DB::connection()->getPdo();

        $sql = "
            SELECT
                anno,
                tipo_evento,
                valor,
                ultima_actualizacion
            FROM datos_mnp
            WHERE municipio_id = :municipio_id
              AND anno BETWEEN :ano_inicio AND :ano_fin
            ORDER BY anno, tipo_evento
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':municipio_id', $municipioId, \PDO::PARAM_INT);
        $stmt->bindParam(':ano_inicio', $anoInicio, \PDO::PARAM_INT);
        $stmt->bindParam(':ano_fin', $anoFin, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Calcular crecimiento vegetativo usando PDO PURO
     *
     * @param string $codigoProvincia Código INE provincia
     * @param int $ano Año a consultar
     * @return array|null
     */
    public static function getCrecimientoVegetativoPDO(string $codigoProvincia, int $ano): ?array
    {
        $pdo = \DB::connection()->getPdo();

        $sql = "
            SELECT
                p.nombre as provincia,
                SUM(CASE WHEN d.tipo_evento = 'nacimiento' THEN d.valor ELSE 0 END) as nacimientos,
                SUM(CASE WHEN d.tipo_evento = 'defuncion' THEN d.valor ELSE 0 END) as defunciones,
                SUM(CASE WHEN d.tipo_evento = 'matrimonio' THEN d.valor ELSE 0 END) as matrimonios
            FROM datos_mnp d
            INNER JOIN municipios m ON d.municipio_id = m.id
            INNER JOIN provincias p ON m.provincia_id = p.id
            WHERE p.codigo_ine = :codigo_provincia
              AND d.anno = :ano
            GROUP BY p.id, p.nombre
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':codigo_provincia', $codigoProvincia, \PDO::PARAM_STR);
        $stmt->bindParam(':ano', $ano, \PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$result) {
            return null;
        }

        $result['ano'] = $ano;
        $result['crecimiento_vegetativo'] = $result['nacimientos'] - $result['defunciones'];

        return $result;
    }

    /**
     * Obtener ranking de municipios usando PDO PURO
     *
     * @param string $tipoEvento nacimiento, defuncion, matrimonio
     * @param int $ano Año a consultar
     * @param int $limite Número de resultados
     * @return array
     */
    public static function getRankingMunicipiosPDO(string $tipoEvento, int $ano, int $limite = 10): array
    {
        $pdo = \DB::connection()->getPdo();

        $sql = "
            SELECT
                m.nombre as municipio,
                p.nombre as provincia,
                d.valor
            FROM datos_mnp d
            INNER JOIN municipios m ON d.municipio_id = m.id
            INNER JOIN provincias p ON m.provincia_id = p.id
            WHERE d.tipo_evento = :tipo_evento
              AND d.anno = :ano
            ORDER BY d.valor DESC
            LIMIT :limite
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':tipo_evento', $tipoEvento, \PDO::PARAM_STR);
        $stmt->bindParam(':ano', $ano, \PDO::PARAM_INT);
        $stmt->bindParam(':limite', $limite, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Comparativa entre provincias usando PDO PURO con transacciones
     *
     * @param int $ano Año a consultar
     * @return array
     */
    public static function getComparativaProvinciasPDO(int $ano): array
    {
        $pdo = \DB::connection()->getPdo();

        // Iniciar transacción para garantizar consistencia
        $pdo->beginTransaction();

        try {
            $sql = "
                SELECT
                    p.codigo_ine,
                    p.nombre as provincia,
                    SUM(CASE WHEN d.tipo_evento = 'nacimiento' THEN d.valor ELSE 0 END) as nacimientos,
                    SUM(CASE WHEN d.tipo_evento = 'defuncion' THEN d.valor ELSE 0 END) as defunciones,
                    SUM(CASE WHEN d.tipo_evento = 'matrimonio' THEN d.valor ELSE 0 END) as matrimonios,
                    SUM(CASE WHEN d.tipo_evento = 'nacimiento' THEN d.valor ELSE 0 END) -
                    SUM(CASE WHEN d.tipo_evento = 'defuncion' THEN d.valor ELSE 0 END) as crecimiento_vegetativo,
                    COUNT(DISTINCT m.id) as num_municipios
                FROM datos_mnp d
                INNER JOIN municipios m ON d.municipio_id = m.id
                INNER JOIN provincias p ON m.provincia_id = p.id
                WHERE d.anno = :ano
                GROUP BY p.id, p.codigo_ine, p.nombre
                ORDER BY nacimientos DESC
            ";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':ano', $ano, \PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Commit transacción
            $pdo->commit();

            return $result;

        } catch (\PDOException $e) {
            // Rollback en caso de error
            $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Inserción masiva usando PDO PURO con transacción
     *
     * @param array $datos Array de datos a insertar
     * @return int Número de registros insertados
     */
    public static function insertarMasivoPDO(array $datos): int
    {
        $pdo = \DB::connection()->getPdo();

        $pdo->beginTransaction();

        try {
            $sql = "
                INSERT INTO datos_mnp (municipio_id, anno, tipo_evento, valor, ultima_actualizacion, created_at, updated_at)
                VALUES (:municipio_id, :anno, :tipo_evento, :valor, :ultima_actualizacion, NOW(), NOW())
                ON DUPLICATE KEY UPDATE
                    valor = VALUES(valor),
                    ultima_actualizacion = VALUES(ultima_actualizacion),
                    updated_at = NOW()
            ";

            $stmt = $pdo->prepare($sql);

            $insertados = 0;
            foreach ($datos as $dato) {
                $stmt->bindParam(':municipio_id', $dato['municipio_id'], \PDO::PARAM_INT);
                $stmt->bindParam(':anno', $dato['anno'], \PDO::PARAM_INT);
                $stmt->bindParam(':tipo_evento', $dato['tipo_evento'], \PDO::PARAM_STR);
                $stmt->bindParam(':valor', $dato['valor'], \PDO::PARAM_INT);
                $stmt->bindValue(':ultima_actualizacion', $dato['ultima_actualizacion'] ?? date('Y-m-d H:i:s'), \PDO::PARAM_STR);

                $stmt->execute();
                $insertados += $stmt->rowCount();
            }

            $pdo->commit();

            return $insertados;

        } catch (\PDOException $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
