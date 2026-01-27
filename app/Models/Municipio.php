<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Municipio extends Model
{
    protected $fillable = [
        'provincia_id',
        'codigo_ine',
        'nombre',
    ];

    /**
     * Un municipio pertenece a una provincia
     */
    public function provincia(): BelongsTo
    {
        return $this->belongsTo(Provincia::class);
    }

    /**
     * Un municipio tiene muchos datos MNP
     */
    public function datosMnp(): HasMany
    {
        return $this->hasMany(DatoMnp::class);
    }

    /**
     * Un municipio puede estar en muchos favoritos
     */
    public function favoritos(): HasMany
    {
        return $this->hasMany(Favorito::class);
    }

    /**
     * Relación muchos a muchos con usuarios a través de favoritos
     */
    public function usuariosFavoritos(): BelongsToMany
    {
        return $this->belongsToMany(Usuario::class, 'favoritos');
    }

    /**
     * Obtener nacimientos de un municipio
     */
    public function nacimientos(): HasMany
    {
        return $this->hasMany(DatoMnp::class)->where('tipo_evento', 'nacimiento');
    }

    /**
     * Obtener defunciones de un municipio
     */
    public function defunciones(): HasMany
    {
        return $this->hasMany(DatoMnp::class)->where('tipo_evento', 'defuncion');
    }

    /**
     * Obtener matrimonios de un municipio
     */
    public function matrimonios(): HasMany
    {
        return $this->hasMany(DatoMnp::class)->where('tipo_evento', 'matrimonio');
    }

    /**
     * Obtener resumen de datos para un año específico
     * Usa Query Builder sobre PDO
     *
     * @param int $ano Año a consultar
     * @return array
     */
    public function getResumenAno(int $ano)
    {
        $datos = \DB::table('datos_mnp')
            ->where('municipio_id', $this->id)
            ->where('anno', $ano)
            ->select('tipo_evento', 'valor')
            ->get();

        $resumen = [
            'municipio' => $this->nombre,
            'provincia' => $this->provincia->nombre,
            'ano' => $ano,
            'nacimientos' => 0,
            'defunciones' => 0,
            'matrimonios' => 0,
        ];

        foreach ($datos as $dato) {
            if ($dato->tipo_evento === 'nacimiento') {
                $resumen['nacimientos'] = $dato->valor;
            } elseif ($dato->tipo_evento === 'defuncion') {
                $resumen['defunciones'] = $dato->valor;
            } elseif ($dato->tipo_evento === 'matrimonio') {
                $resumen['matrimonios'] = $dato->valor;
            }
        }

        $resumen['crecimiento_vegetativo'] = $resumen['nacimientos'] - $resumen['defunciones'];

        return $resumen;
    }

    /**
     * Obtener todos los municipios con sus datos para un año
     * Método estático que usa Query Builder
     *
     * @param int $ano Año a consultar
     * @param string|null $codigoProvincia Filtrar por provincia (opcional)
     * @return \Illuminate\Support\Collection
     */
    public static function conDatosAno(int $ano, ?string $codigoProvincia = null)
    {
        $query = \DB::table('municipios as m')
            ->join('provincias as p', 'm.provincia_id', '=', 'p.id')
            ->leftJoin('datos_mnp as d', function($join) use ($ano) {
                $join->on('m.id', '=', 'd.municipio_id')
                     ->where('d.anno', '=', $ano);
            })
            ->select([
                'm.id',
                'm.codigo_ine',
                'm.nombre as municipio',
                'p.nombre as provincia',
                'd.tipo_evento',
                'd.valor',
            ]);

        if ($codigoProvincia) {
            $query->where('p.codigo_ine', $codigoProvincia);
        }

        return $query->orderBy('m.nombre')->get();
    }

    /**
     * Buscar municipios por nombre (búsqueda parcial)
     * Usa Query Builder sobre PDO
     *
     * @param string $termino Término de búsqueda
     * @return \Illuminate\Support\Collection
     */
    public static function buscarPorNombre(string $termino)
    {
        return \DB::table('municipios as m')
            ->join('provincias as p', 'm.provincia_id', '=', 'p.id')
            ->where('m.nombre', 'LIKE', "%{$termino}%")
            ->select([
                'm.id',
                'm.codigo_ine',
                'm.nombre as municipio',
                'p.nombre as provincia',
            ])
            ->orderBy('m.nombre')
            ->get();
    }

    // ========================================================================
    // MÉTODOS CON PDO PURO (Acceso directo a PDO)
    // ========================================================================

    /**
     * Obtener resumen de un municipio usando PDO PURO
     *
     * @param int $municipioId ID del municipio
     * @param int $ano Año a consultar
     * @return array|null
     */
    public static function getResumenAnoPDO(int $municipioId, int $ano): ?array
    {
        $pdo = \DB::connection()->getPdo();

        $sql = "
            SELECT
                m.nombre as municipio,
                p.nombre as provincia,
                :ano as ano,
                SUM(CASE WHEN d.tipo_evento = 'nacimiento' THEN d.valor ELSE 0 END) as nacimientos,
                SUM(CASE WHEN d.tipo_evento = 'defuncion' THEN d.valor ELSE 0 END) as defunciones,
                SUM(CASE WHEN d.tipo_evento = 'matrimonio' THEN d.valor ELSE 0 END) as matrimonios,
                SUM(CASE WHEN d.tipo_evento = 'nacimiento' THEN d.valor ELSE 0 END) -
                SUM(CASE WHEN d.tipo_evento = 'defuncion' THEN d.valor ELSE 0 END) as crecimiento_vegetativo
            FROM municipios m
            INNER JOIN provincias p ON m.provincia_id = p.id
            LEFT JOIN datos_mnp d ON m.id = d.municipio_id AND d.anno = :ano2
            WHERE m.id = :municipio_id
            GROUP BY m.id, m.nombre, p.nombre
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':municipio_id', $municipioId, \PDO::PARAM_INT);
        $stmt->bindParam(':ano', $ano, \PDO::PARAM_INT);
        $stmt->bindParam(':ano2', $ano, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Buscar municipios por nombre usando PDO PURO
     *
     * @param string $termino Término de búsqueda
     * @return array
     */
    public static function buscarPorNombrePDO(string $termino): array
    {
        $pdo = \DB::connection()->getPdo();

        $sql = "
            SELECT
                m.id,
                m.codigo_ine,
                m.nombre as municipio,
                p.nombre as provincia
            FROM municipios m
            INNER JOIN provincias p ON m.provincia_id = p.id
            WHERE m.nombre LIKE :termino
            ORDER BY m.nombre
        ";

        $stmt = $pdo->prepare($sql);
        $terminoBusqueda = "%{$termino}%";
        $stmt->bindParam(':termino', $terminoBusqueda, \PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Obtener municipios con más población (basado en nacimientos) usando PDO PURO
     *
     * @param int $ano Año a consultar
     * @param int $limite Número de resultados
     * @return array
     */
    public static function getMunicipiosMayoresPDO(int $ano, int $limite = 10): array
    {
        $pdo = \DB::connection()->getPdo();

        $sql = "
            SELECT
                m.id,
                m.codigo_ine,
                m.nombre as municipio,
                p.nombre as provincia,
                SUM(d.valor) as total_eventos
            FROM municipios m
            INNER JOIN provincias p ON m.provincia_id = p.id
            INNER JOIN datos_mnp d ON m.id = d.municipio_id
            WHERE d.anno = :ano
            GROUP BY m.id, m.codigo_ine, m.nombre, p.nombre
            ORDER BY total_eventos DESC
            LIMIT :limite
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':ano', $ano, \PDO::PARAM_INT);
        $stmt->bindParam(':limite', $limite, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Obtener todos los municipios de una provincia usando PDO PURO
     *
     * @param string $codigoProvincia Código INE provincia
     * @return array
     */
    public static function getMunicipiosProvinciaPDO(string $codigoProvincia): array
    {
        $pdo = \DB::connection()->getPdo();

        $sql = "
            SELECT
                m.id,
                m.codigo_ine,
                m.nombre as municipio,
                p.nombre as provincia
            FROM municipios m
            INNER JOIN provincias p ON m.provincia_id = p.id
            WHERE p.codigo_ine = :codigo_provincia
            ORDER BY m.nombre
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':codigo_provincia', $codigoProvincia, \PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Actualizar múltiples municipios usando PDO PURO con transacción
     *
     * @param array $municipios Array de municipios a actualizar
     * @return int Número de registros actualizados
     */
    public static function actualizarMasivosPDO(array $municipios): int
    {
        $pdo = \DB::connection()->getPdo();

        $pdo->beginTransaction();

        try {
            $sql = "
                INSERT INTO municipios (provincia_id, codigo_ine, nombre, created_at, updated_at)
                VALUES (:provincia_id, :codigo_ine, :nombre, NOW(), NOW())
                ON DUPLICATE KEY UPDATE
                    nombre = VALUES(nombre),
                    updated_at = NOW()
            ";

            $stmt = $pdo->prepare($sql);

            $actualizados = 0;
            foreach ($municipios as $municipio) {
                $stmt->bindParam(':provincia_id', $municipio['provincia_id'], \PDO::PARAM_INT);
                $stmt->bindParam(':codigo_ine', $municipio['codigo_ine'], \PDO::PARAM_STR);
                $stmt->bindParam(':nombre', $municipio['nombre'], \PDO::PARAM_STR);

                $stmt->execute();
                $actualizados += $stmt->rowCount();
            }

            $pdo->commit();

            return $actualizados;

        } catch (\PDOException $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Obtener estadísticas completas de municipio usando PDO PURO
     *
     * @param string $codigoIne Código INE del municipio
     * @param int $anoInicio Año inicial
     * @param int $anoFin Año final
     * @return array|null
     */
    public static function getEstadisticasCompletasPDO(string $codigoIne, int $anoInicio, int $anoFin): ?array
    {
        $pdo = \DB::connection()->getPdo();

        $sql = "
            SELECT
                m.codigo_ine,
                m.nombre as municipio,
                p.nombre as provincia,
                d.anno,
                d.tipo_evento,
                d.valor
            FROM municipios m
            INNER JOIN provincias p ON m.provincia_id = p.id
            LEFT JOIN datos_mnp d ON m.id = d.municipio_id
                AND d.anno BETWEEN :ano_inicio AND :ano_fin
            WHERE m.codigo_ine = :codigo_ine
            ORDER BY d.anno, d.tipo_evento
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':codigo_ine', $codigoIne, \PDO::PARAM_STR);
        $stmt->bindParam(':ano_inicio', $anoInicio, \PDO::PARAM_INT);
        $stmt->bindParam(':ano_fin', $anoFin, \PDO::PARAM_INT);
        $stmt->execute();

        $resultados = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($resultados)) {
            return null;
        }

        // Organizar datos por año
        $estadisticas = [
            'municipio' => $resultados[0]['municipio'],
            'provincia' => $resultados[0]['provincia'],
            'codigo_ine' => $resultados[0]['codigo_ine'],
            'datos_por_ano' => [],
        ];

        foreach ($resultados as $fila) {
            if ($fila['anno']) {
                $ano = $fila['anno'];
                if (!isset($estadisticas['datos_por_ano'][$ano])) {
                    $estadisticas['datos_por_ano'][$ano] = [
                        'nacimientos' => 0,
                        'defunciones' => 0,
                        'matrimonios' => 0,
                    ];
                }
                $estadisticas['datos_por_ano'][$ano][$fila['tipo_evento'] . 's'] = $fila['valor'];
            }
        }

        return $estadisticas;
    }
}
