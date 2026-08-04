<?php

namespace Database\Seeders;

use App\Models\ConfiguracionReserva;
use Illuminate\Database\Seeder;

class ConfiguracionReservaSeeder extends Seeder
{
     public function run(): void
    {
        ConfiguracionReserva::query()
            ->updateOrCreate(
                ['id' => 1],
                [
                    'capacidad_maxima_simultanea' => 200,
                ]
            );
    }
}
