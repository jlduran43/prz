<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionCotizacion extends Model
{
    protected $table = 'configuraciones_cotizacion';

    protected $fillable = [
        'titulo',
        'descripcion_tour',
        'condiciones_pago',
        'titular_cuenta',
        'rut_titular',
        'banco',
        'tipo_cuenta',
        'numero_cuenta',
        'correo_comprobantes',
        'politica_devoluciones',
        'condiciones_museo',
        'recomendaciones_museo',
        'recomendaciones_parque',
        'nota_importante',
        'correo_reservas',
        'telefono_reservas',
        'horario_contacto',
        'dias_validez',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            'dias_validez' => 'integer',
        ];
    }
}
