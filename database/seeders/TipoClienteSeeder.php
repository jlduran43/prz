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
                'tipo_estructura' => 'PERSONA',
                'activo' => false,
            ],

            [
                'codigo' => 'ESTABLECIMIENTO_EDUCACIONAL',
                'nombre' => 'Establecimiento educacional',
                'tipo_estructura' => 'ESTABLECIMIENTO',
                'activo' => true,
            ],

            [
                'codigo' => 'TOUR_OPERADOR_AGENCIA_VIAJES',
                'nombre' => 'Tour operador / Agencia de viajes',
                'tipo_estructura' => 'ORGANIZACION',
                'activo' => true,
            ],

            [
                'codigo' => 'GRUPO_ADULTOS_MAYORES',
                'nombre' => 'Grupo de adultos mayores',
                'tipo_estructura' => 'ORGANIZACION',
                'activo' => true,
            ],
        ];

        foreach ($tipos as $tipo) {

            TipoCliente::query()->updateOrCreate(
                [
                    'codigo' => $tipo['codigo'],
                ],
                $tipo
            );
        }
    }
}
