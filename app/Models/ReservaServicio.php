<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ReservaServicio extends Model
{
    protected $table = 'reserva_servicios';

    protected $fillable = [
        'reserva_id',
        'servicio_experiencia_id',
        'horario_disponible_id',
        'fecha',
        'cantidad_personas',
        'precio_unitario',
        'subtotal',
    ];

    protected $casts = [
        'fecha' => 'date',
        'cantidad_personas' => 'integer',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function reserva(): BelongsTo
    {
        return $this->belongsTo(Reserva::class);
    }

    public function servicio(): BelongsToMany
    {
        return $this->belongsToMany(
            ServicioExperiencia::class,
            'reserva_servicios',
            'reserva_id',
            'servicio_experiencia_id'
        )
            ->withPivot([
                'horario_disponible_id',
                'fecha',
                'cantidad_personas',
                'precio_unitario',
                'subtotal',
            ])
            ->withTimestamps();
    }

    public function horario(): BelongsTo
    {
        return $this->belongsTo(
            HorarioDisponible::class,
            'horario_disponible_id'
        );
    }
}
