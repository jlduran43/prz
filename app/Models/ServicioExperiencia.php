<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServicioExperiencia extends Model
{
    protected $table = 'servicios_experiencias';

    protected $fillable = [
        'categoria_servicio_id',
        'codigo',
        'nombre',
        'descripcion',
        'duracion_minutos',
        'capacidad_minima',
        'capacidad_maxima',
        'precio',
        'requiere_reserva',
        'activo',
    ];

    protected $casts = [
        'duracion_minutos' => 'integer',
        'capacidad_minima' => 'integer',
        'capacidad_maxima' => 'integer',
        'precio' => 'decimal:2',
        'requiere_reserva' => 'boolean',
        'activo' => 'boolean',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(
            CategoriaServicio::class,
            'categoria_servicio_id'
        );
    }

    public function reservas()
    {
        return $this->belongsToMany(
            Reserva::class,
            'reserva_servicio',
            'servicio_experiencia_id',
            'reserva_id'
        )
            ->withPivot([
                'horario_disponible_id',
                'precio_unitario',
                'cantidad_personas',
                'subtotal',
            ])
            ->withTimestamps();
    }

    public function horariosDisponibles(): HasMany
    {
        return $this->hasMany(
            HorarioDisponible::class,
            'servicio_experiencia_id'
        );
    }

    public function reservasServicios(): HasMany
    {
        return $this->hasMany(
            ReservaServicio::class,
            'servicio_experiencia_id'
        );
    }
}
