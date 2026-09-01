<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HorarioDisponible extends Model
{
    protected $table = 'horarios_disponibles';

    protected $fillable = [
        'fecha',
        'hora_inicio',
        'hora_termino',
        'activo',
        'google_event_id',
        'google_synced_at',
        'google_sync_error',
        'google_synced_at' => 'datetime',
    ];

    protected $casts = [
        'fecha' => 'date',
        'activo' => 'boolean',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'activo' => 'boolean',
        ];
    }

    public function servicios(): BelongsToMany
    {
        return $this->belongsToMany(
            ServicioExperiencia::class,
            'horario_servicio',
            'horario_disponible_id',
            'servicio_experiencia_id'
        )->withTimestamps();
    }

    public function reservaServicios(): HasMany
    {
        return $this->hasMany(
            ReservaServicio::class,
            'horario_disponible_id'
        );
    }
}
