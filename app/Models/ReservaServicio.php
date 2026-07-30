<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservaServicio extends Model
{
     protected $table = 'reserva_servicios';

    protected $fillable = [
        'reserva_id',
        'servicio_experiencia_id',
        'horario_disponible_id',
        'precio',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
    ];

    public function reserva(): BelongsTo
    {
        return $this->belongsTo(Reserva::class);
    }

    public function servicio(): BelongsTo
    {
        return $this->belongsTo(
            ServicioExperiencia::class,
            'servicio_experiencia_id'
        );
    }

    public function horario(): BelongsTo
    {
        return $this->belongsTo(
            HorarioDisponible::class,
            'horario_disponible_id'
        );
    }
}
