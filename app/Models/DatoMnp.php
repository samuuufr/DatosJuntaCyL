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
}
