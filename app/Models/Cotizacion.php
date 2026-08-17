<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cotizacion extends Model
{
    protected $table = 'cotizaciones';

    protected $fillable = [
        'folio',
        'token_acceso',

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

        'cantidad_asistentes',

        'cantidad_alumnos',
        'cantidad_profesores',
        'nivel_educacional',
        'curso',
        'objetivo_visita',

        'convenio_id',
        'codigo_convenio',
        'nombre_convenio',
        'porcentaje_descuento',

        'subtotal',
        'descuento',
        'total',

        'estado',

        'fecha_emision',
        'fecha_vencimiento',
        'condiciones_snapshot',
        'vigencia_hasta',
    ];

    protected function casts(): array
    {
        return [
            'cantidad_asistentes' => 'integer',

            'cantidad_alumnos' => 'integer',
            'cantidad_profesores' => 'integer',

            'porcentaje_descuento' =>
            'decimal:2',

            'subtotal' =>
            'decimal:2',

            'descuento' =>
            'decimal:2',

            'total' =>
            'decimal:2',

            'fecha_emision' =>
            'datetime',

            'fecha_vencimiento' =>
            'date',

            'vigencia_hasta' => 'date',
            'condiciones_snapshot' => 'array',
        ];
    }

    public function servicios(): HasMany
    {
        return $this->hasMany(
            CotizacionServicio::class
        )->orderBy('orden');
    }

    public function tipoCliente(): BelongsTo
    {
        return $this->belongsTo(
            TipoCliente::class
        );
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(
            Region::class
        );
    }

    public function comuna(): BelongsTo
    {
        return $this->belongsTo(
            Comuna::class
        );
    }

    public function convenio(): BelongsTo
    {
        return $this->belongsTo(
            Convenio::class
        );
    }
}
