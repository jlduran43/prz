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
        'tipo_rut',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    //public function clientes(): HasMany
    //{
        //return $this->hasMany(Cliente::class);
    //}
}
