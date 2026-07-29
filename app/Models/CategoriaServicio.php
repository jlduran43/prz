<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoriaServicio extends Model
{
    protected $table = 'categorias_servicio';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function servicios(): HasMany
    {
        return $this->hasMany(
            ServicioExperiencia::class,
            'categoria_servicio_id'
        );
    }
}
