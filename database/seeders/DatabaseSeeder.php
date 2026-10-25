<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // este se encarga de ejecutar a otris seeders
        $this->call([
            RoleSeeder::class,
            GenreSeeder::class,
        ]);

        // usuario de prueba para el rol de administrador
        User::create([
            'username' => 'admin_test',
            'email' => 'admin@test.com',
            'password' => Hash::make('Admin123$'),
            'role_id' => 3,
        ]);

        // usuario de prueba para el rol de oyente
        User::create([
            'username' => 'oyente_test',
            'email' => 'oyente@example.com',
            'password' => Hash::make('Oyente123$'),
            'role_id' => 1,
        ]);
    }
}