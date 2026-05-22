<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Affiliate;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@fesirmes.local'],
            ['name' => 'Administrador FESIRMES', 'password' => Hash::make('password'), 'role' => UserRole::Administrador]
        );

        User::updateOrCreate(
            ['email' => 'secretaria@fesirmes.local'],
            ['name' => 'Secretaría FESIRMES', 'password' => Hash::make('password'), 'role' => UserRole::Secretaria]
        );

        User::updateOrCreate(
            ['email' => 'consulta@fesirmes.local'],
            ['name' => 'Usuario Consulta', 'password' => Hash::make('password'), 'role' => UserRole::Consulta]
        );

        Affiliate::updateOrCreate(
            ['ci' => '1234567'],
            [
                'first_name' => 'María',
                'last_name' => 'Quispe',
                'phone' => '70000001',
                'email' => 'maria.quispe@example.com',
                'address' => 'La Paz',
                'joined_at' => now()->toDateString(),
                'status' => 'activo',
                'notes' => 'Registro de ejemplo para probar el CRUD.',
            ]
        );
    }
}
