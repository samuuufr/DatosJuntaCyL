<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Usuario extends Authenticatable
{
    protected $table = 'usuarios';

    protected $fillable = [
        'email',
        'password',
        'nombre',
        'rol',
        'fecha_registro',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'fecha_registro' => 'datetime',
    ];

    /**
     * Un usuario puede tener muchos favoritos
     */
    public function favoritos(): HasMany
    {
        return $this->hasMany(Favorito::class);
    }

    /**
     * Relación muchos a muchos con municipios a través de favoritos
     */
    public function municipiosFavoritos(): BelongsToMany
    {
        return $this->belongsToMany(Municipio::class, 'favoritos');
    }

    /**
     * Alias para municipiosFavoritos
     */
    public function municipios(): BelongsToMany
    {
        return $this->municipiosFavoritos();
    }
}
