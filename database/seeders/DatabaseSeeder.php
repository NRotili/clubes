<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'dev@club.com'],
            [
                'name'     => 'Desarrollador',
                'password' => Hash::make('password'),
                'rol'      => 'desarrollador',
            ]
        );

        User::firstOrCreate(
            ['email' => 'admin@club.com'],
            [
                'name'     => 'Administración',
                'password' => Hash::make('password'),
                'rol'      => 'administracion',
            ]
        );
    }
}
