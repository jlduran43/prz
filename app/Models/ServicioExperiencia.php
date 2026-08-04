<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\CategoriaServicio;

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
        'tipo_cobro',
        'max_cursos_simultaneos',
        'max_alumnos_por_curso',
        'activo',
    ];

    protected $casts = [
        'duracion_minutos' => 'integer',
        'capacidad_minima' => 'integer',
        'capacidad_maxima' => 'integer',
        'precio' => 'decimal:2',
        'requiere_reserva' => 'boolean',
        'max_cursos_simultaneos' => 'integer',
        'max_alumnos_por_curso' => 'integer',
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
            'reserva_servicios',
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

    public function reservasServicios(): HasMany
    {
        return $this->hasMany(
            ReservaServicio::class,
            'servicio_experiencia_id'
        );
    }

    public function horariosDisponibles(): BelongsToMany
    {
        return $this->belongsToMany(
            HorarioDisponible::class,
            'horario_servicio',
            'servicio_experiencia_id',
            'horario_disponible_id'
        )->withTimestamps();
    }
}
