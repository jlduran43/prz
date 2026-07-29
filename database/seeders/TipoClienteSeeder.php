<?php

namespace Database\Seeders;

use App\Models\TipoCliente;
use Illuminate\Database\Seeder;

class TipoClienteSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            [
                'codigo' => 'PERSONA',
                'nombre' => 'Persona natural',
                'estructura_datos' => 'PERSONA',
                'activo' => false,
            ],
            [
                'codigo' => 'ESTABLECIMIENTO_EDUCACIONAL',
                'nombre' => 'Establecimiento educacional',
                'estructura_datos' => 'ORGANIZACION',
                'activo' => true,
            ],
            [
                'codigo' => 'TOUR_OPERADOR_AGENCIA_VIAJES',
                'nombre' => 'Tour operador / Agencia de viajes',
                'estructura_datos' => 'ORGANIZACION',
                'activo' => true,
            ],
        ];

        foreach ($tipos as $tipo) {
            TipoCliente::query()->updateOrCreate(
                ['codigo' => $tipo['codigo']],
                $tipo
            );
        }
    }
}
