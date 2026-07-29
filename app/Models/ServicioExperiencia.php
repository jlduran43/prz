<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
