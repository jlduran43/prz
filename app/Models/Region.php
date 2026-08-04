<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Region extends Model
{
    protected $table = 'regiones';

    protected $fillable = [
        'codigo',
        'nombre',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function comunas(): HasMany
    {
        return $this->hasMany(Comuna::class);
    }
}
