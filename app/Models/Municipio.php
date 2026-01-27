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
     * Usa Eloquent ORM
     *
     * @param int $ano Año a consultar
     * @return array
     */
    public function getResumenAno(int $ano)
    {
        $datos = $this->datosMnp()->where('anno', $ano)->get();

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
     * Método estático que usa Eloquent ORM
     *
     * @param int $ano Año a consultar
     * @param string|null $codigoProvincia Filtrar por provincia (opcional)
     * @return \Illuminate\Support\Collection
     */
    public static function conDatosAno(int $ano, ?string $codigoProvincia = null)
    {
        $query = self::join('provincias', 'municipios.provincia_id', '=', 'provincias.id')
            ->leftJoin('datos_mnp', function($join) use ($ano) {
                $join->on('municipios.id', '=', 'datos_mnp.municipio_id')
                     ->where('datos_mnp.anno', '=', $ano);
            })
            ->select([
                'municipios.id',
                'municipios.codigo_ine',
                'municipios.nombre as municipio',
                'provincias.nombre as provincia',
                'datos_mnp.tipo_evento',
                'datos_mnp.valor',
            ]);

        if ($codigoProvincia) {
            $query->where('provincias.codigo_ine', $codigoProvincia);
        }

        return $query->orderBy('municipios.nombre')->get();
    }

    /**
     * Buscar municipios por nombre (búsqueda parcial)
     * Usa Eloquent ORM
     *
     * @param string $termino Término de búsqueda
     * @return \Illuminate\Support\Collection
     */
    public static function buscarPorNombre(string $termino)
    {
        return self::join('provincias', 'municipios.provincia_id', '=', 'provincias.id')
            ->where('municipios.nombre', 'LIKE', "%{$termino}%")
            ->select([
                'municipios.id',
                'municipios.codigo_ine',
                'municipios.nombre as municipio',
                'provincias.nombre as provincia',
            ])
            ->orderBy('municipios.nombre')
            ->get();
    }

}
