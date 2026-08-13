<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CotizacionServicio extends Model
{
    protected $table =
    'cotizacion_servicios';

    protected $fillable = [
        'cotizacion_id',
        'servicio_experiencia_id',

        'nombre_servicio',

        'precio_unitario',
        'tipo_cobro',

        'cantidad_asistentes',
        'personas_pagadas',
        'entradas_liberadas',

        'subtotal',

        'orden',
    ];

    protected function casts(): array
    {
        return [
            'precio_unitario' =>
            'decimal:2',

            'cantidad_asistentes' =>
            'integer',

            'personas_pagadas' =>
            'integer',

            'entradas_liberadas' =>
            'integer',

            'subtotal' =>
            'decimal:2',

            'orden' =>
            'integer',
        ];
    }

    public function cotizacion(): BelongsTo
    {
        return $this->belongsTo(
            Cotizacion::class
        );
    }

    public function servicio(): BelongsTo
    {
        return $this->belongsTo(
            ServicioExperiencia::class,
            'servicio_experiencia_id'
        );
    }
}
