<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoCliente extends Model
{
    protected $table = 'tipos_cliente';

    protected $fillable = [
        'codigo',
        'nombre',
        'tipo_estructura',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];


    public function reservas(): HasMany
    {
        return $this->hasMany(Reserva::class);
    }
}
