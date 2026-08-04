<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionReserva extends Model
{
    protected $table = 'configuraciones_reserva';

    protected $fillable = [
        'capacidad_maxima_simultanea',
    ];

    protected $casts = [
        'capacidad_maxima_simultanea' => 'integer',
    ];
}
