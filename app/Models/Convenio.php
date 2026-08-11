<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Convenio extends Model
{
    protected $table = 'convenios';

    protected $fillable = [
        'codigo',
        'nombre',
        'porcentaje_descuento',
        'fecha_inicio',
        'fecha_termino',
        'activo',
        'observaciones',
    ];

    protected $casts = [
        'porcentaje_descuento' => 'decimal:2',
        'fecha_inicio' => 'date',
        'fecha_termino' => 'date',
        'activo' => 'boolean',
    ];

    public function entidades(): HasMany
    {
        return $this->hasMany(
            ConvenioEntidad::class,
            'convenio_id'
        );
    }
}
