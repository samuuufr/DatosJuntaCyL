<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Provincia extends Model
{
    protected $fillable = [
        'codigo_ine',
        'nombre',
    ];

    /**
     * Una provincia tiene muchos municipios
     */
    public function municipios(): HasMany
    {
        return $this->hasMany(Municipio::class);
    }

    /**
     * Obtener todos los datos MNP de una provincia a través de sus municipios
     */
    public function datosMnp(): HasManyThrough
    {
        return $this->hasManyThrough(DatoMnp::class, Municipio::class);
    }
}
