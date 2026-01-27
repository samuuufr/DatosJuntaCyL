<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

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
     * Usa Eloquent ORM
     *
     * @param int $ano Año a consultar
     * @return \Illuminate\Support\Collection
     */
    public static function getEstadisticasPorProvincia(int $ano)
    {
        return self::join('municipios', 'datos_mnp.municipio_id', '=', 'municipios.id')
            ->join('provincias', 'municipios.provincia_id', '=', 'provincias.id')
            ->where('datos_mnp.anno', $ano)
            ->select([
                'provincias.codigo_ine',
                'provincias.nombre as provincia',
                'datos_mnp.tipo_evento',
                DB::raw('SUM(datos_mnp.valor) as total'),
                DB::raw('COUNT(DISTINCT municipios.id) as num_municipios'),
                DB::raw('AVG(datos_mnp.valor) as promedio'),
            ])
            ->groupBy('provincias.id', 'provincias.codigo_ine', 'provincias.nombre', 'datos_mnp.tipo_evento')
            ->orderBy('provincias.nombre')
            ->orderBy('datos_mnp.tipo_evento')
            ->get();
    }

    /**
     * Obtener evolución temporal de un municipio
     * Usa Eloquent ORM
     *
     * @param int $municipioId ID del municipio
     * @param int $anoInicio Año inicial
     * @param int $anoFin Año final
     * @return \Illuminate\Support\Collection
     */
    public static function getEvolucionMunicipio(int $municipioId, int $anoInicio, int $anoFin)
    {
        return self::where('municipio_id', $municipioId)
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
     * Usa Eloquent ORM con subconsultas
     *
     * @param string $codigoProvincia Código INE de provincia
     * @param int $ano Año a consultar
     * @return array|null
     */
    public static function getCrecimientoVegetativo(string $codigoProvincia, int $ano): ?array
    {
        $result = self::join('municipios', 'datos_mnp.municipio_id', '=', 'municipios.id')
            ->join('provincias', 'municipios.provincia_id', '=', 'provincias.id')
            ->where('provincias.codigo_ine', $codigoProvincia)
            ->where('datos_mnp.anno', $ano)
            ->select([
                'provincias.nombre as provincia',
                DB::raw("SUM(CASE WHEN datos_mnp.tipo_evento = 'nacimiento' THEN datos_mnp.valor ELSE 0 END) as nacimientos"),
                DB::raw("SUM(CASE WHEN datos_mnp.tipo_evento = 'defuncion' THEN datos_mnp.valor ELSE 0 END) as defunciones"),
                DB::raw("SUM(CASE WHEN datos_mnp.tipo_evento = 'matrimonio' THEN datos_mnp.valor ELSE 0 END) as matrimonios"),
            ])
            ->groupBy('provincias.id', 'provincias.nombre')
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
     * Usa Eloquent ORM
     *
     * @param string $tipoEvento nacimiento, defuncion, matrimonio
     * @param int $ano Año a consultar
     * @param int $limite Número de resultados
     * @return \Illuminate\Support\Collection
     */
    public static function getRankingMunicipios(string $tipoEvento, int $ano, int $limite = 10)
    {
        return self::join('municipios', 'datos_mnp.municipio_id', '=', 'municipios.id')
            ->join('provincias', 'municipios.provincia_id', '=', 'provincias.id')
            ->where('datos_mnp.tipo_evento', $tipoEvento)
            ->where('datos_mnp.anno', $ano)
            ->select([
                'municipios.nombre as municipio',
                'provincias.nombre as provincia',
                'datos_mnp.valor',
            ])
            ->orderBy('datos_mnp.valor', 'desc')
            ->limit($limite)
            ->get();
    }

}
