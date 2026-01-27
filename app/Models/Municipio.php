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
}
