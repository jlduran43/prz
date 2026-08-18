<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            [
                'email' => 'admin@prz.cl',
            ],
            [
                'name' => 'Administrador PRZ',
                'password' => Hash::make('admin123!'),
            ]
        );
    }
}
