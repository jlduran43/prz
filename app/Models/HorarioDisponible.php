<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HorarioDisponible extends Model
{
    protected $table = 'horarios_disponibles';

    protected $fillable = [
        'servicio_experiencia_id',
        'fecha',
        'hora_inicio',
        'hora_termino',
        'activo',
    ];

    protected $casts = [
        'fecha' => 'date',
        'activo' => 'boolean',
    ];


    public function servicio(): BelongsTo
    {
        return $this->belongsTo(
            ServicioExperiencia::class,
            'servicio_experiencia_id'
        );
    }

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'activo' => 'boolean',
        ];
    }
}
