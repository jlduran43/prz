<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConvenioEntidad extends Model
{
    protected $table = 'convenio_entidades';

    protected $fillable = [
        'convenio_id',
        'nombre_entidad',
        'rut_entidad',
        'rut_normalizado',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function convenio(): BelongsTo
    {
        return $this->belongsTo(
            Convenio::class,
            'convenio_id'
        );
    }
}
