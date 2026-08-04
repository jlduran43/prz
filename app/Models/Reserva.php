<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reserva extends Model
{
    protected $fillable = [
        'tipo_cliente_id',
        'nombres',
        'apellidos',
        'rut_persona',
        'nombre_entidad',
        'rut_entidad',
        'nombre_encargado',
        'rut_encargado',
        'email',
        'telefono',
        'region_id',
        'comuna_id',
        'fecha',
        'hora_inicio',
        'hora_termino',
        'cantidad_asistentes',
        'cantidad_alumnos',
        'cantidad_profesores',
        'nivel_educacional',
        'curso',
        'subtotal',
        'descuento',
        'total',
        'estado',
        'observaciones',
        'objetivo_visita',
    ];

    protected $casts = [
        'fecha' => 'date',
        'cantidad_asistentes' => 'integer',
        'cantidad_alumnos' => 'integer',
        'cantidad_profesores' => 'integer',
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function tipoCliente(): BelongsTo
    {
        return $this->belongsTo(TipoCliente::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function comuna(): BelongsTo
    {
        return $this->belongsTo(Comuna::class);
    }

    public function servicios(): BelongsToMany
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
                'precio_unitario',
                'cantidad_personas',
                'subtotal',
            ])
            ->withTimestamps();
    }

    public function serviciosReserva(): HasMany
    {
        return $this->hasMany(ReservaServicio::class);
    }
}
